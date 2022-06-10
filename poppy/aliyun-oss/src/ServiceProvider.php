<?php

namespace Poppy\AliyunOss;

use Poppy\AliyunOss\Http\RouteServiceProvider;
use Poppy\Framework\Exceptions\ModuleNotFoundException;
use Poppy\Framework\Support\PoppyServiceProvider as ModuleServiceProviderBase;

class ServiceProvider extends ModuleServiceProviderBase
{

    /**
     * Bootstrap the application events.
     * @return void
     * @throws ModuleNotFoundException
     */
    public function boot()
    {
        parent::boot('poppy.aliyun-oss');

        $this->bootConfig();
    }


    public function register()
    {

        $this->mergeConfigFrom(dirname(__DIR__) . '/resources/config/aliyun-oss.php', 'poppy.aliyun-oss');

        $this->app->register(RouteServiceProvider::class);
    }

    private function bootConfig()
    {
        // 注册配置
        if (sys_setting('py-system::picture.save_type') === 'aliyun') {

            // 设置返回地址
            config([
                'poppy.aliyun-oss.access_key'    => sys_setting('py-aliyun-oss::oss.access_key'),
                'poppy.aliyun-oss.access_secret' => sys_setting('py-aliyun-oss::oss.access_secret'),
                'poppy.aliyun-oss.endpoint'      => sys_setting('py-aliyun-oss::oss.endpoint'),
                'poppy.aliyun-oss.bucket'        => sys_setting('py-aliyun-oss::oss.bucket'),
                'poppy.aliyun-oss.url'           => sys_setting('py-aliyun-oss::oss.url_prefix'),
                'poppy.aliyun-oss.role_arn'      => sys_setting('py-aliyun-oss::oss.role_arn'),
                'poppy.aliyun-oss.temp_key'      => sys_setting('py-aliyun-oss::oss.temp_app_key'),
                'poppy.aliyun-oss.temp_secret'   => sys_setting('py-aliyun-oss::oss.temp_app_secret'),
                'poppy.aliyun-oss.watermark'     => sys_setting('py-aliyun-oss::oss.watermark'),
            ]);
        }
    }
}
