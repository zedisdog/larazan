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
 * @method string getClientId() 获取client_id
 * @method string getClientSecret() 获取client_secret
 * @method bool itemDelete($item_id, string $yz_open_id = '', $version = '3.0.1') 删除商品
 * @method bool addSalesmanAccount($identification,int $fans_type = 0,string $from_mobile = '',int $level = 0,int $group_id = 0,string $version = '3.0.1') 添加分销员
 * @method array|null refund(string $desc, string $oid, int $refundFee, string $tid, string $version = '3.0.0') 主动退款
 * @method bool|null ticketVerify(array $params, $version = '1.0.0') 核销
 * @method array|null getTrade(string $tid, string $version = '4.0.0') 获取订单详情
 * @method bool|null ticketCreate(string $tickets, string $orderNo, int $singleNum = 1, string $version = '1.0.0') 创建核销码
 * @method bool|null pointIncrease(string $accountId, int $accountType, int $points, string $reason, string $bizValue = '', string $version = '3.1.0') 加积分
 * @method array|null getSalesman(string|int $identification, string $version = '3.0.1') 获取分销员详情
 * @method string|null getPhoneByTrade(string $tradeId, string $version = '3.0.0') 通过订单id获取分销员手机号
 * @method array|null addTags(int $id, string $tags, $version = '3.0.0') 向用户添加tag
 * @method array|null itemSearch(string $keyword,int $pageNo = 1,int $pageSize = 100,int $showSoldOut = 2,$version = '3.0.0') 搜索商品
 * @method array|null itemListByItemIds(array $item_ids,int $page_no = 1,int $page_size = 100,int $show_sold_out = 2,$version = '3.0.0') 根据商品id获取商品数组
 * @method bool|null addCostumerTags(string $accountType, string $accountId, array $tags, string $version = '4.0.0') 向客户添加tag
 * @method array|null getOpenIdByPhone(string $mobile, string $countryCode = '86', string $version = '3.0.0') 根据手机号码获取openId
 * @method array|null getShopInfo(string $version = '3.0.0') 获取店铺信息
 * @method array|null getItemCategories(string $version = '3.0.0') 获取商品分组
 * @method array|null getOnSaleItems(int $pageNo = 1,int $pageSize = 40,string $q = '',int $tagId = 0,?Carbon $updateTimeStart = null,?Carbon $updateTimeEnd = null,string $orderBy = 'created_time:desc',string $version = '3.0.0') 获取上架的商品
 * @method array|null getInventoryItems(int $pageNo = 1,int $pageSize = 40,string $banner = '',string $q = '',int $tagId = 0,?Carbon $updateTimeStart = null,?Carbon $updateTimeEnd = null,string $orderBy = 'created_time:desc',string $version = '3.0.0') 获取仓库中的商品
 * @method array|null getProducts(int $pageNo = 1,int $pageSize = 40,string $itemIds = '',int $showSoldOut = 2,string $q = '',string $tagIds = '',string $version = '3.0.0') 获取所有商品，包括上架的和仓库中的
 * @method array|null getShopBaseInfo(string $version = '3.0.0') 获取店铺基础信息
 * @method array|null givePresent(int $activityId, int $fansId, int $buyerId = 0, string $version = '3.0.0') 向用户发送赠品
 * @method array|null getPresents(string $version = '3.0.0') 获取进行中的赠品
 * @method array|null getUnfinishedCoupons(string $fields = '', string $version = '3.0.0') 获取未结束的优惠活动
 * @method array|null getCoupon(int $id, string $version='3.0.0') 获取优惠券详情
 * @method array|null takeCoupon(int $couponGroupId, string $identify, string $type, $version='3.0.0') 发放优惠券/码
 * @method array|null getCouponList(string $groupType, string $status, int $pageNo = 1, int $pageSize = 1000, string $version = '3.0.0') （分页查询）查询优惠券（码）活动列表
 * @method array|null getSalesmanList($pageNo = 1, $pageSize = 100, $version = '3.0.0') 获取分销员列表
 * @method array|null itemGet($identification, $alias = false, string $version = '3.0.0') 获取商品
 * @method array|null getFollower($id, string $version='3.0.0') 通过 open_id 或者 fans_id 获取用户信息
 * @method bool itemUpdate(array $params, string $version = '3.0.1') 更新商品
 * @method array|null itemCreate(array $params, string $version = '3.0.1') 创建商品
 * @method array|null uploadImage(string $filename, string $version = '3.0.0') 上传图片
 * @method array|null getTags(bool $isSort = false, string $version = '3.0.0') 获取分组
 * @method bool|null logisticsUpdate(array $param, string $version = '3.0.1') 更新物流信息
 * @method array|null logisticsConfirm(array $param, string $version = '3.0.0') 确认发货接口
 * @method array|null expressGet(string $version = '3.0.0') 获取快递公司列表
 * @method array|null queryDcByOrderNo(string $tid, array $options = [], string $kdt_id = '', string $version = '1.0.0') 快递单查询
 * @method array|null logisticsTemplateGet(int $page_no, int $page_size = 20, string $version = '3.0.0') 运费模板查询
 * @method int logisticsFee(string $order_no, string $province_name, string $city_name, string $county_name, array $item_param_list, string $version = '3.0.0') 计算运费
 * @method array|null salesmanTrades(array $params, $version = '3.0.1') 获取分销员订单
 * @method array|null getRefund($refund_id, $version = '3.0.0') 获取退款详情(youzan.trade.refund.get)
 * @method bool agreeRefund(string $refund_id, $version, string $api_version = '3.0.0') 商家同意退款
 * @method bool returnGoodsRefuse(string $remark, string $refund_id, $version, string $api_version = '3.0.0') 商家拒绝退货
 * @method bool returnGoodsAgree(string $refund_id, string $version, string $address, string $post, string $mobile, string $name, string $remark = '', string $tel = '', string $api_version = '3.0.0') 商家同意退货
 * @method bool refuseRefund(string $remark, string $refund_id, $version, $api_version = '3.0.0') 商家拒绝退款
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
     * @throws \Illuminate\Contracts\Container\BindingResolutionException
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
     * @param bool $refresh
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function setShopId(int $shopId, bool $refresh = false)
    {
        if (!$this->store && config('larazan.multiSeller')) {
            throw new NoStoreException('no store, no cache');
        }
        $this->shopId = intval($shopId);
        $tokenKey = $this->getTokenCacheKey();
        $refreshTokenKey = $this->getRefreshTokenCacheKey();

        if (!$this->store->get($tokenKey) || $refresh) {
            if (config('larazan.multiSeller') === false) {
                $this->exchangeTokenSilent();
            } elseif (!$this->store->get($refreshTokenKey)) {
                if (!$this->dontReportAll) {
                    throw new NoCacheException('specific shop has no cache:' . $shopId . ', refresh_token key:' . $refreshTokenKey);
                } else {
                    $this->logger->warning('specific shop has no cache', ['shop_id' => $shopId, 'refresh_token_key' => $refreshTokenKey, 'stack' => debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS, 50)]);
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
     * @param string $push_url
     * @param string $compensate_url
     * @param string $provider
     * @return array|bool
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function ticketBind(string $push_url, string $compensate_url, string $provider = 'STANDARD')
    {
        return $this->client->ticketBind($push_url, $compensate_url, $provider);
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
                            $this->setShopId($this->shopId, true);
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