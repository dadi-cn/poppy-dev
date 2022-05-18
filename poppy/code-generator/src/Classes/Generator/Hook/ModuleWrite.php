<?php

declare(strict_types = 1);

namespace Poppy\CodeGenerator\Classes\Generator\Hook;

use Illuminate\Filesystem\Filesystem;

class ModuleWrite
{
    private string $configPath = 'configurations/hooks.yaml';

    private Filesystem $filesystem;

    public function __construct()
    {
        $this->filesystem = app('files');
    }

    public function write(string $module, array $classes): void
    {
        $hookPath = poppy_path($module, $this->configPath);

        $this->filesystem->append($hookPath, $this->hookContent($classes));
    }

    private function hookContent(array $classes): string
    {
        $content = <<<CONTENT

-
  name: 'poppy.mgr-app.settings'
  hooks:
CONTENT;

        foreach ($classes as $class) {
            $content .= "
    - '{$class}'";
        }

        return $content;
    }
}