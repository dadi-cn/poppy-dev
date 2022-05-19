<?php

declare(strict_types = 1);

namespace Poppy\CodeGenerator\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Str;
use Poppy\CodeGenerator\Classes\Constants;
use Poppy\CodeGenerator\Classes\Generator\Hook\HookService;
use Poppy\CodeGenerator\Classes\Generator\Hook\ModuleWrite;
use Poppy\Core\Classes\Traits\CoreTrait;
use Poppy\Core\Module\Module;
use Poppy\Core\Module\Repositories\Modules;

class SettingGenerateCommand extends Command
{
    use CoreTrait;

    private static $skipModulePrefixes = [
        'poppy.',
    ];

    protected $signature = 'py-code-generator:setting';

    protected $description = '根据mgr-page的配置生成mgr-app的配置';

    /**
     * @var \Illuminate\Contracts\Foundation\Application|mixed|HookService
     */
    private $hookService;

    public function __construct()
    {
        parent::__construct();

        $this->addArgument('module', null, '模块名称', null);
    }

    public function handle()
    {
        $module = $this->argument('module');

        $modules = $this->coreModule()
            ->modules()
            ->when($module, function (Modules $modules, $module) {
                return $modules->filter(fn($value, string $key) => $module === $this->getModuleByHookKey($key));
            })
            ->when(!$module, function (Modules $modules) {
                return $modules->filter(fn($value, string $key) => !Str::contains($key, self::$skipModulePrefixes));
            });

        if ($modules->isEmpty()) {
            $this->error(sprintf('Module [%s] Not Found!', $module));
            return;
        }

        $this->hookService = app(HookService::class);

        $errors = collect();
        $modules->each(function (Module $values, string $key) use ($errors) {
            $hooks = (array) collect($values->get('hooks', []))
                ->pluck('hooks', 'name')
                ->get('poppy.mgr-page.settings', []);

            $module = $this->getModuleByHookKey($key);
            if (empty($hooks)) {
                return;
            }

            $newClasses = [];
            foreach ($hooks as $clazz) {
                try {
                    $handled = $this->hookService->handle($clazz);
                    if (!$handled) {
                        continue;
                    }

                    $names     = explode('\\', $clazz);
                    $className = array_pop($names);
                    $names[]   = Constants::APPEND_NAMESPACE;
                    $names[]   = $className;

                    $newClass     = implode('\\', $names);
                    $newClasses[] = $newClass;

                    $this->info("[{$module}] {$className} 生成成功");
                } catch (\Throwable $e) {
                    $error = sprintf(
                        'module: %s, class: %s, message: %s',
                        $module, $clazz, $e->getMessage()
                    );
                    $errors->add($error);
                }
            }

            (new ModuleWrite())->write($module, $newClasses);
        });

        if ($errors->isNotEmpty()) {
            $this->error($errors->implode("\n"));
        }
    }

    /**
     * @param string $key
     * @return string
     */
    private function getModuleByHookKey(string $key): string
    {
        return last(explode('.', $key));
    }
}