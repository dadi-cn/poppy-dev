<?php

namespace Poppy\CodeGenerator\Classes;

use Illuminate\Filesystem\Filesystem;

class CopyService
{
    private string $newSubDir;

    private Filesystem $filesystem;

    public function __construct(string $newSubDir = Constants::APPEND_DIR_NAME)
    {
        $this->filesystem = app('files');
        $this->newSubDir  = $newSubDir;
    }

    /**
     * @param string $filePath
     * @return void
     */
    public function copy(string $filePath): string
    {
        $newDir = dirname($filePath) . '/' . $this->newSubDir;

        $this->createNewDir($newDir);

        $newFilePath = $newDir . '/' . basename($filePath);

        $this->filesystem->copy($filePath, $newFilePath);

        return $newFilePath;
    }

    /**
     * @param string $directory
     * @return void
     */
    private function createNewDir(string $directory): void
    {
        if ($this->filesystem->isDirectory($directory)) {
            return;
        }

        $this->filesystem->makeDirectory($directory, 0755, true);
    }
}