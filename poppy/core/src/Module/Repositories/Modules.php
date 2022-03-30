<?php

namespace Poppy\Core\Module\Repositories;

use Illuminate\Support\Collection;
use Poppy\Core\Classes\PyCoreDef;
use Poppy\Core\Module\Module;
use Poppy\Framework\Exceptions\LoadConfigurationException;
use Poppy\Framework\Support\Abstracts\Repository;
use Symfony\Component\Yaml\Yaml;

/**
 * 所有模块的配置信息.
 */
class Modules extends Repository
{

    /**
     * @var bool
     */
    protected $loadFromCache = true;

    /**
     * Initialize.
     * @param Collection $slugs 集合
     */
    public function initialize(Collection $slugs)
    {
        $files       = app('files');
        $this->items = sys_cache('py-core')->remember(
            PyCoreDef::ckModule('module'),
            PyCoreDef::MIN_HALF_DAY * 60,
            function () use ($slugs, $files) {
                // load from file
                $this->loadFromCache = false;
                $collection          = collect();
                $slugs->each(function ($slug) use ($collection, $files) {
                    $module = new Module($slug);
                    if ($files->exists($module->directory() . DIRECTORY_SEPARATOR . 'manifest.json')) {
                        // load config
                        $configurations = $this->loadConfigurations($module->directory());

                        // set config to module
                        $configurations->isNotEmpty() && $configurations->each(function ($value, $item) use ($module) {
                            $module->offsetSet($item, $value);
                        });

                        // is enable
                        $module->offsetSet('enabled', $module->isEnabled());

                        // put all config to repository use key `slug`
                        $collection->put($slug, $module);
                    }
                });

                return $collection->all();
            }
        );
    }

    /**
     * @return Collection
     */
    public function enabled(): Collection
    {
        return $this->filter(function (Module $module) {
            return $module->get('enabled') === true;
        });
    }

    /**
     * @return Collection
     */
    public function loaded(): Collection
    {
        return $this->filter(function (Module $module) {
            return $module->get('initialized') === true;
        });
    }

    /**
     * @return Collection
     */
    public function notLoaded(): Collection
    {
        return $this->filter(function (Module $module) {
            return $module->get('initialized') === false;
        });
    }

    /**
     * Load configuration from module configurations folder.
     * @param string $directory 字典
     * @return Collection
     * @throws LoadConfigurationException
     */
    protected function loadConfigurations(string $directory): Collection
    {
        $files     = app('files');
        $directory .= DIRECTORY_SEPARATOR . 'configurations';
        if ($files->isDirectory($directory)) {
            $configurations = collect();

            // put it in filename key
            collect($files->files($directory))->each(function ($file) use ($configurations, $files) {
                $name = basename(realpath($file), '.yaml');
                if ($name !== 'module' && $files->isReadable($file)) {
                    $configurations->put($name, Yaml::parse(file_get_contents($file)));
                }
            });

            return $configurations;
        }

        throw new LoadConfigurationException('Load Module fail: ' . $directory);
    }
}
