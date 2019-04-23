<?php
/**
 * Created by zed.
 */

namespace Dezsidog\LYouzanphp;


use Dezsidog\Youzanphp\Client\Client;
use Dezsidog\Youzanphp\Oauth2\Oauth;
use Illuminate\Foundation\Application;

class ServiceProvider extends \Illuminate\Support\ServiceProvider
{
    /**
     * 标记着提供器是延迟加载的
     *
     * @var bool
     */
    protected $defer = true;

    public function register()
    {
        $this->app->singleton(Manager::class,function(Application $app){
            $oauth = new Oauth(config('larazan.clientId'), config('larazan.clientSecret'));
            $client = new Client();
            if (config('larazan.multiSeller')) {
                return new Manager($app, $client, $oauth, 0, $app->make('cache')->getStore());
            } else {
                return new Manager($app, $client, $oauth, config('larazan.kdtId'), $app->make('cache')->getStore());
            }
        });
        $this->app->alias(Manager::class, 'larazan');
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
//        if (config('larazan.callback')) {
//            $router->prefix(config('larazan.callback.prefix', 'api'))
//                ->middleware(config('larazan.callback.middlewares', 'api'))
//                ->any(config('larazan.callback.url', 'yz-callback'), config('larazan.callback.class'));
//        }
        $router->prefix(config('larazan.hook.prefix', 'api'))
            ->middleware(config('larazan.hook.middlewares'), 'api')
            ->any(config('larazan.hook.url'), config('larazan.hook.action'));
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