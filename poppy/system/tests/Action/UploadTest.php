<?php

namespace Poppy\System\Tests\Action;

use Poppy\Framework\Application\TestCase;
use Poppy\System\Classes\Uploader\DefaultUploadProvider;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Throwable;

/**
 * 上传测试[本地上传测试]
 */
class UploadTest extends TestCase
{

    /**
     * 进行上传
     */
    public function testUpload()
    {
        try {
            $file   = poppy_path('poppy.system', 'tests/files/demo.jpg');
            $image  = new UploadedFile($file, 'test.jpg', null, null, true);
            $Upload = new DefaultUploadProvider();

            $Upload->setExtension(['jpg']);
            if (!$Upload->saveFile($image)) {
                $this->fail($Upload->getError());
            }

            // 检测文件存在
            $url = $Upload->getUrl();
            if (file_get_contents($url)) {
                $this->assertTrue(true);
                $path = base_path('public/' . $Upload->getDestination());
                $this->outputVariables($path);
                $result = app('files')->delete(base_path('public/' . $Upload->getDestination()));
                $this->assertTrue($result);
            } else {
                $this->fail("Url {$url} 不可访问!");
            }
        } catch (Throwable $e) {
            $this->fail($e->getMessage());
        }
    }

    /**
     * 进行上传
     */
    public function testDest()
    {
        try {
            $file   = poppy_path('poppy.system', 'tests/files/demo.jpg');
            $image  = new UploadedFile($file, 'test.jpg', null, null, true);
            $Upload = new DefaultUploadProvider();

            $Upload->setExtension(['jpg']);
            $Upload->setDestination('dev/testing/upload-dest.jpg');
            if (!$Upload->saveFile($image)) {
                $this->fail($Upload->getError());
            }

            // 检测文件存在
            $url = $Upload->getUrl();
            if (file_get_contents($url)) {
                $this->assertTrue(true);
                $path = base_path('public/' . $Upload->getDestination());
                $this->outputVariables($path);
                $result = app('files')->delete(base_path('public/' . $Upload->getDestination()));
                $this->assertTrue($result);
            } else {
                $this->fail("Url {$url} 不可访问!");
            }
        } catch (Throwable $e) {
            $this->fail($e->getMessage());
        }
    }
}