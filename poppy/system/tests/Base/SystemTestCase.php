<?php

namespace Poppy\System\Tests\Base;

use DB;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Foundation\Application;
use Log;
use Poppy\Faker\Generator;
use Poppy\Framework\Application\TestCase;
use Poppy\Framework\Exceptions\FakerException;
use Poppy\Framework\Helper\StrHelper;
use Poppy\Framework\Helper\UtilHelper;
use Poppy\System\Classes\Traits\DbTrait;
use Poppy\System\Models\PamAccount;
use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Output\ConsoleOutput;

class SystemTestCase extends TestCase
{

    use DbTrait;

    /**
     * @var PamAccount
     */
    protected $pam;

    /**
     * 控制台输出
     * @var array
     */
    protected $reportType = ['log', 'console'];

    public function setUp(): void
    {
        parent::setUp();
        DB::enableQueryLog();
    }

    /**
     * 测试日志
     * @param bool   $result  测试结果
     * @param string $message 测试消息
     * @param mixed  $context 上下文信息, 数组
     * @return string
     */
    public function runLog($result = true, $message = '', $context = null): string
    {
        $type    = $result ? '[Success]' : '[ Error ]';
        $message = 'Test : ' . $type . $message;
        if ($context instanceof Arrayable) {
            $context = $context->toArray();
        }
        if (in_array('log', $this->reportType(), true)) {
            Log::info($message, $context ?: []);
        }
        if (in_array('console', $this->reportType(), true)) {
            dump([
                'message' => $message,
                'context' => $context ?: [],
            ]);
        }

        return $message;
    }


    protected function initPam($username = '')
    {
        $username = $username ?: $this->env('pam');
        $pam      = PamAccount::passport($username);
        $this->assertNotNull($pam, 'Testing user pam is not exist');
        $this->pam = $pam;
    }

    /**
     * @return Generator|Application|mixed
     * @throws FakerException
     */
    protected function faker()
    {
        return py_faker();
    }

    /**
     * 设置环境变量
     * @param string $key
     * @param string $default
     * @return mixed|string
     */
    protected function env($key = '', $default = ''): string
    {
        if (!$key) {
            return '';
        }

        return env('TESTING_' . strtoupper($key), $default);
    }

    /**
     * 读取模块 Json 文件
     * @param $module
     * @param $path
     * @return array
     */
    protected function readJson($module, $path): array
    {
        $filePath = poppy_path($module, $path);
        if (file_exists($filePath)) {
            $config = file_get_contents($filePath);
            if (UtilHelper::isJson($config)) {
                return json_decode($config, true);
            }
            return [];
        }
        return [];
    }

    /**
     * 汇报类型
     * @return array
     */
    protected function reportType(): array
    {
        $reportType = $this->env('report_type');
        if ($reportType) {
            return StrHelper::separate(',', $reportType);
        }

        return $this->reportType;
    }

    /**
     * SQL Log 提示
     */
    protected function sqlLog(): void
    {
        $logs = $this->fetchQueryLog();

        if (count($logs)) {
            $this->table(['Query', 'Time'], $logs);
        }
    }

    /**
     * Format input to textual table.
     *
     * @param array           $headers
     * @param Arrayable|array $rows
     * @param string          $tableStyle
     * @param array           $columnStyles
     * @return void
     */
    protected function table(array $headers, $rows, $tableStyle = 'default', array $columnStyles = []): void
    {
        $table = new Table(new ConsoleOutput());

        if ($rows instanceof Arrayable) {
            $rows = $rows->toArray();
        }

        $table->setHeaders((array) $headers)->setRows($rows)->setStyle($tableStyle);

        foreach ($columnStyles as $columnIndex => $columnStyle) {
            $table->setColumnStyle($columnIndex, $columnStyle);
        }

        $table->render();
    }

    /**
     * 输出变量
     * @param array|string $var
     */
    protected function export($var): void
    {
        $export = var_export($var, true);
        echo $export;
    }

    /**
     * 当前的描述
     * @param string $append 追加的信息
     * @return string
     */
    protected static function desc($append = ''): string
    {
        $bt       = debug_backtrace(DEBUG_BACKTRACE_PROVIDE_OBJECT, 2);
        $function = $bt[1]['function'];
        $class    = $bt[1]['class'];

        return '|' . $class . '@' . $function . '|' . ($append ? $append . '|' : '');
    }
}