<?php

namespace Poppy\Sms;

use Poppy\Framework\Exceptions\ModuleNotFoundException;
use Poppy\Framework\Support\PoppyServiceProvider as ModuleServiceProviderBase;
use Poppy\Sms\Action\Sms;
use Poppy\Sms\Classes\Contracts\SmsContract;
use Poppy\Sms\Classes\Factory;
use Poppy\Sms\Http\RouteServiceProvider;

class ServiceProvider extends ModuleServiceProviderBase
{

    /**
     * Bootstrap the application events.
     * @return void
     * @throws ModuleNotFoundException
     */
    public function boot()
    {
        parent::boot('poppy.sms');
    }

    /**
     * Register the service provider.
     * @return void
     */
    public function register()
    {
        $this->app->register(RouteServiceProvider::class);

        $this->registerConfig();

        // 配置文件
        $this->mergeConfigFrom(dirname(__DIR__) . '/resources/config/sms.php', 'poppy.sms');

        $this->app->singleton('poppy.sms', function () {
            return Factory::instance();
        });

        $this->app->alias('poppy.sms', SmsContract::class);
    }

    /**
     * Get the services provided by the provider.
     * @return array
     */
    public function provides()
    {
        return [
            'poppy.sms',
        ];
    }


    private function registerConfig()
    {
        // 注册配置
        if (sys_setting('py-sms::sms.send_type')) {
            // config 注入
            config([
                'poppy.sms.send_type'      => sys_setting('py-sms::sms.send_type'),
                'poppy.sms.sign'           => sys_setting('py-sms::sms.sign'),
            ]);
        }

        if (sys_setting('py-sms::sms.send_type') === Sms::SCOPE_ALIYUN) {
            config([
                'poppy.sms.aliyun.access_key'    => sys_setting('py-sms::sms.aliyun_access_key'),
                'poppy.sms.aliyun.access_secret' => sys_setting('py-sms::sms.aliyun_access_secret'),
            ]);
        }
        if (sys_setting('py-sms::sms.send_type') === Sms::SCOPE_CHUANGLAN) {
            config([
                'poppy.sms.chuanglan.access_key'        => sys_setting('py-sms::sms.chuanglan_access_key'),
                'poppy.sms.chuanglan.access_secret'     => sys_setting('py-sms::sms.chuanglan_access_secret'),
                'poppy.sms.chuanglan.cty_access_key'    => sys_setting('py-sms::sms.chuanglan_cty_access_key'),
                'poppy.sms.chuanglan.cty_access_secret' => sys_setting('py-sms::sms.chuanglan_cty_access_secret'),
            ]);
        }
    }
}
