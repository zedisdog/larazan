<?php
/**
 * Created by zed.
 */

declare(strict_types=1);
namespace Dezsidog\Larazan;


use Carbon\Carbon;
use Dezsidog\Larazan\Exceptions\MethodNotFoundException;
use Dezsidog\Larazan\Exceptions\NoCacheException;
use Dezsidog\Larazan\Exceptions\NoStoreException;
use Dezsidog\Youzanphp\Api\Client;
use Dezsidog\Youzanphp\Exceptions\BaseGatewayException;
use Dezsidog\Youzanphp\Exceptions\TokenException;
use Dezsidog\Youzanphp\Oauth2\Oauth;
use Illuminate\Console\Application;
use Illuminate\Contracts\Cache\Store;
use Illuminate\Contracts\Container\Container;
use Psr\Log\LoggerInterface;

/**
 * Class Manager
 * @package Dezsidog\LYouzanphp
 * @method string getClientId()
 * @method string getClientSecret()
 * @method array|null getTrade(string $tid, string $version = '4.0.0')
 * @method bool|null pointIncrease(string $accountId, int $accountType, int $points, string $reason, string $bizValue = '', string $version = '3.1.0')
 * @method array|null getSalesman(string|int $identification, string $version = '3.0.1')
 * @method string|null getPhoneByTrade(string $tradeId, string $version = '3.0.0')
 * @method array|null addTags(int $id, string $tags, $version = '3.0.0')
 * @method array|null itemSearch(string $keyword,int $pageNo = 1,int $pageSize = 100,?int $showSoldOut = null,$version = '3.0.0')
 * @method array|null itemListByItemIds(array $item_ids,int $page_no = 1,int $page_size = 100,?int $show_sold_out = null,$version = '3.0.0')
 * @method bool|null addCostumerTags(string $accountType, string $accountId, array $tags, string $version = '4.0.0')
 * @method array|null getOpenIdByPhone(string $mobile, string $countryCode = '86', string $version = '3.0.0')
 * @method array|null getShopInfo(string $version = '3.0.0')
 * @method array|null getItemCategories(string $version = '3.0.0')
 * @method array|null getOnSaleItems(int $pageNo = 1,int $pageSize = 40,string $q = '',int $tagId = 0,?Carbon $updateTimeStart = null,?Carbon $updateTimeEnd = null,string $orderBy = 'created_time:desc',string $version = '3.0.0')
 * @method array|null getInventoryItems(int $pageNo = 1,int $pageSize = 40,string $banner = '',string $q = '',int $tagId = 0,?Carbon $updateTimeStart = null,?Carbon $updateTimeEnd = null,string $orderBy = 'created_time:desc',string $version = '3.0.0')
 * @method array|null getProducts(int $pageNo = 1,int $pageSize = 40,string $itemIds = '',int $showSoldOut = 2,string $q = '',string $tagIds = '',string $version = '3.0.0')
 * @method array|null getShopBaseInfo(string $version = '3.0.0')
 * @method array|null givePresent(int $activityId, int $fansId, int $buyerId = 0, string $version = '3.0.0')
 * @method array|null getPresents(string $version = '3.0.0')
 * @method array|null getUnfinishedCoupons(string $fields = '', string $version = '3.0.0')
 * @method array|null getCoupon(int $id, string $version='3.0.0')
 * @method array|null takeCoupon(int $couponGroupId, string $identify, string $type, $version='3.0.0')
 * @method array|null getCouponList(string $groupType, string $status, int $pageNo = 1, int $pageSize = 1000, string $version = '3.0.0')
 * @method array|null getSalesmanList($pageNo = 1, $pageSize = 100, $version = '3.0.0')
 * @method array|null itemGet($identification, $alias = false, string $version = '3.0.0')
 * @method array|null getFollower($id, string $version='3.0.0')
 */
class Manager
{
    const TOKEN_CACHE_BASE_KEY = 'larazan';
    /**
     * @var Application|\Illuminate\Foundation\Application
     */
    protected $app;
    /**
     * @var Store
     */
    protected $store;
    /**
     * @var Client
     */
    protected $client;
    /**
     * @var Oauth
     */
    protected $oauthClient;
    /**
     * @var int
     */
    protected $refreshTokenExpires;
    /**
     * @var int
     */
    protected $shopId;
    /**
     * @var bool
     */
    protected $dontReportAll;
    /**
     * @var LoggerInterface
     */
    protected $logger;

    /**
     * Manager constructor.
     * @param Container $app
     * @param Client $client
     * @param Oauth $oauth
     * @param int $shopId
     * @param Store|null $store
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function __construct(Container $app, Client $client, Oauth $oauth, int $shopId = 0, ?Store $store = null)
    {
        $this->app = $app;
        $this->store = $store;
        $this->client = $client;
        $this->oauthClient = $oauth;
        $this->logger = $this->app->make('log');
        if ($shopId) {
            $this->setShopId($shopId);
        }
        $this->refreshTokenExpires = config('larazan.refreshTokenExpires');
    }

    public function dontReportAll()
    {
        $this->dontReportAll = true;
        $this->client->dontReportAll();
    }

    /**
     * @return int
     */
    public function getShopId(): int
    {
        return $this->shopId;
    }
    /**
     * @param int $shopId
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function setShopId(int $shopId)
    {
        if (!$this->store && config('larazan.multiSeller')) {
            throw new NoStoreException('no store, no cache');
        }
        $this->shopId = intval($shopId);
        $tokenKey = $this->getTokenCacheKey();
        $refreshTokenKey = $this->getRefreshTokenCacheKey();

        if (!$this->store->get($tokenKey)) {
            if (config('larazan.multiSeller') === false) {
                $this->exchangeTokenSilent();
            } elseif (!$this->store->get($refreshTokenKey)) {
                if (!$this->dontReportAll) {
                    throw new NoCacheException('specific shop has no cache');
                } else {
                    $this->logger->warning('specific shop has no cache', ['shop_id' => $shopId]);
                }
            } else {
                $this->exchangeTokenByRefreshToken($this->store->get($refreshTokenKey));
            }
        } else {
            $this->client->setAccessToken($this->store->get($tokenKey));
        }
    }

    public function getAccessToken()
    {
        $tokenKey = $this->getTokenCacheKey();
        return $this->store->get($tokenKey);
    }

    /**
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function exchangeTokenSilent()
    {
        $token = $this->oauthClient->requestTokenSilent($this->shopId);
        if ($this->store) {
            $this->cacheToken($token);
        }
        $this->client->setAccessToken($token['access_token']);
    }
    /**
     * 获取token
     * @param string $code
     * @param string $redirectUri
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function exchangeTokenByCode(string $code, string $redirectUri = '')
    {
        if (!$redirectUri) {
            $redirectUri = config('larazan.redirectUri');
        }
        $token = $this->oauthClient->requestToken($code, $redirectUri);
        $this->shopId = intval($token['authority_id']);
        if ($this->store) {
            $this->cacheToken($token);
        }
        $this->client->setAccessToken($token['access_token']);
    }

    /**
     * 通过refresh_token刷新access_token
     * @param string $refreshToken
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function exchangeTokenByRefreshToken(string $refreshToken) {
        $token = $this->oauthClient->refreshToken($refreshToken);
        $this->shopId = intval($token['authority_id']);
        if ($this->store) {
            $this->cacheToken($token);
        }
        $this->client->setAccessToken($token['access_token']);
    }

    /**
     * @param array|null $token
     */
    protected function cacheToken(?array $token)
    {
        if (!$token) {
            $this->logger->warning('no token data received');
        }
        // 自用型授权没有refresh_token
        if ($this->app->version() < '5.8') {
            $token['expires'] = intval($token['expires']/1000/60);
            if (config('larazan.multiSeller')) {
                $token['refresh_expires'] = $this->refreshTokenExpires;
            }
        } else {
            $token['expires'] = intval($token['expires']/1000);
            if (config('larazan.multiSeller')) {
                $token['refresh_expires'] = $this->refreshTokenExpires/60;
            }
        }
        $tokenKey = $this->getTokenCacheKey();
        $refreshTokenKey = $this->getRefreshTokenCacheKey();

        $this->store->put($tokenKey, $token['access_token'], $token['expires']);
        if (config('larazan.multiSeller')) {
            $this->store->put($refreshTokenKey, $token['refresh_token'], $token['refresh_expires']);
        }
    }

    public function getTokenCacheKey(): string
    {
        // 根据client id 分组 支持多应用
        return sprintf('%s.%s.token.%s', $this->oauthClient->getClientId(), self::TOKEN_CACHE_BASE_KEY, $this->shopId);
    }

    public function getRefreshTokenCacheKey(): string
    {
        // 根据client id 分组 支持多应用
        return sprintf('%s.%s.refresh_token.%s', $this->oauthClient->getClientId(), self::TOKEN_CACHE_BASE_KEY, $this->shopId);
    }

    /**
     * @param $name
     * @param $arguments
     * @return mixed
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function __call($name, $arguments)
    {
        if (method_exists($this->client, $name)) {
            try {
                return $this->client->$name(...$arguments);
            } catch (BaseGatewayException $e) {
                if ($this->shopId) {
                    switch (get_class($e)) {
                        case TokenException::class:
                            $this->setShopId($this->shopId);
                            return $this->client->$name(...$arguments);
                            break;
                        default:
                            throw $e;
                    }
                } else {
                    throw $e;
                }
            }
        } elseif (method_exists($this->oauthClient, $name)) {
            return $this->oauthClient->$name(...$arguments);
        } else {
            throw new MethodNotFoundException(sprintf('method [%s] with params [%s] not found', $name, implode(',', $arguments)));
        }
    }
}