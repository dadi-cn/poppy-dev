<?php

namespace Poppy\System;

use Illuminate\Auth\Events\Login as AuthLoginEvent;
use Illuminate\Console\Scheduling\Schedule;
use Poppy\Core\Events\PermissionInitEvent;
use Poppy\Framework\Classes\Traits\PoppyTrait;
use Poppy\Framework\Events\PoppyOptimized;
use Poppy\Framework\Exceptions\ModuleNotFoundException;
use Poppy\Framework\Support\PoppyServiceProvider;
use Poppy\System\Classes\Api\Sign\DefaultApiSignProvider;
use Poppy\System\Classes\Auth\Password\DefaultPasswordProvider;
use Poppy\System\Classes\Auth\Provider\BackendProvider;
use Poppy\System\Classes\Auth\Provider\DevelopProvider;
use Poppy\System\Classes\Auth\Provider\PamProvider;
use Poppy\System\Classes\Auth\Provider\WebProvider;
use Poppy\System\Classes\Contracts\ApiSignContract;
use Poppy\System\Classes\Contracts\PasswordContract;
use Poppy\System\Classes\Contracts\UploadContract;
use Poppy\System\Classes\Uploader\DefaultUploadProvider;
use Poppy\System\Events\LoginTokenPassedEvent;
use Poppy\System\Models\PamAccount;
use Poppy\System\Models\PamRole;
use Poppy\System\Models\Policies\PamAccountPolicy;
use Poppy\System\Models\Policies\PamRolePolicy;

/**
 * @property $listens;
 */
class ServiceProvider extends PoppyServiceProvider
{
    use PoppyTrait;

    protected array $listens = [
        // laravel
        AuthLoginEvent::class           => [

        ],
        PermissionInitEvent::class      => [
            Listeners\PermissionInit\InitToDbListener::class,
        ],
        PoppyOptimized::class           => [
            Listeners\PoppyOptimized\ClearCacheListener::class,
        ],
        LoginTokenPassedEvent::class    => [
            Listeners\LoginTokenPassed\SsoListener::class,
        ],

        // system
        Events\LoginSuccessEvent::class => [
            Listeners\LoginSuccess\UpdatePasswordHashListener::class,
            Listeners\LoginSuccess\LogListener::class,
            Listeners\LoginSuccess\UpdateLastLoginListener::class,
        ],
    ];

    protected array $policies = [
        PamRole::class    => PamRolePolicy::class,
        PamAccount::class => PamAccountPolicy::class,
    ];

    /**
     * Bootstrap the module services.
     * @return void
     * @throws ModuleNotFoundException
     */
    public function boot()
    {
        parent::boot('poppy.system');

        $this->bootConfigs();
    }

    /**
     * Register the module services.
     * @return void
     */
    public function register()
    {
        // 配置文件
        $this->mergeConfigFrom(dirname(__DIR__) . '/resources/config/system.php', 'poppy.system');

        $this->app->register(Http\MiddlewareServiceProvider::class);
        $this->app->register(Http\RouteServiceProvider::class);
        $this->app->register(Setting\SettingServiceProvider::class);

        $this->registerConsole();

        $this->registerAuth();

        $this->registerSchedule();

        $this->registerContracts();
    }

    public function provides(): array
    {
        return [];
    }

    private function registerSchedule()
    {
        app('events')->listen('console.schedule', function (Schedule $schedule) {
            $schedule->command('py-system:user', ['auto_enable'])
                ->everyFifteenMinutes()->appendOutputTo($this->consoleLog());
            $schedule->command('py-system:user', ['clear_log'])
                ->dailyAt('04:00')->appendOutputTo($this->consoleLog());
            // 每天清理一次
            $schedule->command('py-system:user', ['clear_expired'])
                ->dailyAt('06:00')->appendOutputTo($this->consoleLog());
        });
    }

    /**
     * register rbac and alias
     */
    private function registerContracts()
    {
        $this->app->bind('poppy.system.api_sign', function ($app) {
            /** @var ApiSignContract $signProvider */
            $signProvider = config('poppy.system.api_sign_provider') ?: DefaultApiSignProvider::class;
            return new $signProvider();
        });
        $this->app->alias('poppy.system.api_sign', ApiSignContract::class);


        $this->app->bind('poppy.system.password', function ($app) {
            $pwdClass = config('poppy.system.password_provider') ?: DefaultPasswordProvider::class;
            return new $pwdClass();
        });
        $this->app->alias('poppy.system.password', PasswordContract::class);


        /* 文件上传提供者
         * ---------------------------------------- */
        $this->app->bind('poppy.system.uploader', function ($app) {
            $uploadType = sys_setting('py-system::picture.save_type');
            $hooks      = sys_hook('poppy.system.upload_type');
            if (!$uploadType) {
                $uploadType = 'default';
            }
            $uploader      = $hooks[$uploadType];
            $uploaderClass = $uploader['provider'] ?? DefaultUploadProvider::class;
            return new $uploaderClass();
        });
        $this->app->alias('poppy.system.uploader', UploadContract::class);

    }

    private function registerConsole()
    {
        // system
        $this->commands([
            // system:module
            Commands\UserCommand::class,
            Commands\InstallCommand::class,
        ]);
    }

    private function registerAuth()
    {
        app('auth')->provider('pam.web', function ($app) {
            return new WebProvider(PamAccount::class);
        });
        app('auth')->provider('pam.backend', function ($app) {
            return new BackendProvider(PamAccount::class);
        });
        app('auth')->provider('pam.develop', function ($app) {
            return new DevelopProvider(PamAccount::class);
        });
        app('auth')->provider('pam', function ($app) {
            return new PamProvider(PamAccount::class);
        });
    }

    private function bootConfigs()
    {
        config([
            'mail.driver'       => sys_setting('py-system::mail.driver') ?: config('mail.driver'),
            'mail.encryption'   => sys_setting('py-system::mail.encryption') ?: config('mail.encryption'),
            'mail.port'         => sys_setting('py-system::mail.port') ?: config('mail.port'),
            'mail.host'         => sys_setting('py-system::mail.host') ?: config('mail.host'),
            'mail.from.address' => sys_setting('py-system::mail.from') ?: config('mail.from.address'),
            'mail.from.name'    => sys_setting('py-system::mail.from') ?: config('mail.from.name'),
            'mail.username'     => sys_setting('py-system::mail.username') ?: config('mail.username'),
            'mail.password'     => sys_setting('py-system::mail.password') ?: config('mail.password'),
        ]);

        config([
            'poppy.framework.title'       => sys_setting('py-system::site.name'),
            'poppy.framework.description' => sys_setting('py-system::site.description'),
        ]);
    }
}