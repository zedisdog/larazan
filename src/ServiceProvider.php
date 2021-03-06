<?php
/**
 * Created by zed.
 */

namespace Dezsidog\Larazan;


use Dezsidog\Youzanphp\Api\Client;
use Dezsidog\Youzanphp\Oauth2\Oauth;
use Dezsidog\Youzanphp\Sec\Decrypter;
use Illuminate\Foundation\Application;

class ServiceProvider extends \Illuminate\Support\ServiceProvider
{
//    /**
//     * 标记则提供器是延迟加载的
//     *
//     * @var bool
//     */
//    protected $defer = true;

    public function register()
    {
        $this->app->bind(Manager::class,function(Application $app, array $config = []){
            $defaultConfig = [
                'clientId' => config('larazan.clientId'),
                'clientSecret' => config('larazan.clientSecret'),
                'kdtId' => config('larazan.kdtId'),
                'multiSeller' => config('larazan.multiSeller')
            ];
            $config = array_merge($defaultConfig, $config);
            $oauth = new Oauth($config['clientId'], $config['clientSecret'], $app->make('log'));
            $client = new Client('', $app->make('log'));
            if ($config['multiSeller']) {
                return new Manager($app, $client, $oauth, 0, $app->make('cache')->getStore());
            } else {
                return new Manager($app, $client, $oauth, $config['kdtId'], $app->make('cache')->getStore());
            }
        });
        $this->app->alias(Manager::class, 'larazan');

        $this->app->bind(Decrypter::class, function(Application $app, array $config = []){
            $secret = $config['secret'] ?? config('larazan.clientSecret');
            return new Decrypter($secret);
        });
    }

    /**
     * @throws \Illuminate\Contracts\Container\BindingResolutionException
     */
    public function boot() {
        if ($this->app instanceof Application) {
            $this->publishes([
                __DIR__.'/config.php' => config_path('larazan.php'),
            ]);
        }

        $this->mergeConfigFrom(__DIR__.'/config.php', 'larazan');

        $router = $this->app->make('router');
        $router->prefix(config('larazan.callback.prefix'))
            ->middleware(config('larazan.callback.middlewares'))
            ->any(config('larazan.callback.url'), config('larazan.callback.action'));
        $router->prefix(config('larazan.hook.prefix'))
            ->middleware(config('larazan.hook.middlewares'))
            ->any(config('larazan.hook.url'), config('larazan.hook.action'));

        if (config('larazan.ticket.enabled')) {
            $router->prefix(config('larazan.ticket.notify.prefix'))
                ->middleware(config('larazan.ticket.notify.middlewares'))
                ->any(config('larazan.ticket.notify.url'), config('larazan.ticket.notify.action'));
            $router->prefix(config('larazan.ticket.compensate.prefix'))
                ->middleware(config('larazan.ticket.compensate.middlewares'))
                ->any(config('larazan.ticket.compensate.url'), config('larazan.ticket.compensate.action'));
        }
    }

    /**
     * 取得提供者提供的服务
     *
     * @return array
     */
    public function provides()
    {
        return [Manager::class];
    }
}