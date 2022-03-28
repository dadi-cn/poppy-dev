<?php

namespace Poppy\System\Tests\Base;

use DB;
use Exception;
use GuzzleHttp\Client;
use Illuminate\Contracts\Support\Arrayable;
use Log;
use Poppy\Framework\Application\TestCase;
use Poppy\Framework\Classes\ConsoleTable;
use Poppy\Framework\Helper\StrHelper;
use Poppy\System\Classes\Traits\DbTrait;
use Poppy\System\Models\PamAccount;
use Throwable;

class SystemTestCase extends TestCase
{

    use DbTrait;

    protected $enableDb = false;

    /**
     * @var PamAccount
     */
    protected $pam;

    /**
     * 控制台输出
     * @var array
     */
    protected $reportType = ['log', 'console'];

    /**
     * 访问内容
     * @var string|null
     */
    protected $visitContent;

    public function setUp(): void
    {
        parent::setUp();
        DB::enableQueryLog();
        if (!$this->enableDb) {
            try {
                DB::beginTransaction();
            } catch (Exception $e) {
            }
        }
    }


    public function tearDown(): void
    {
        if (!$this->enableDb) {
            try {
                DB::rollBack();
                parent::tearDown();
            } catch (Throwable $e) {

            }
        }
    }

    /**
     * 测试日志
     * @param bool $result 测试结果
     * @param string $message 测试消息
     * @param mixed $context 上下文信息, 数组
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
            $Table = new ConsoleTable();
            $Table->headers([
                'Query', 'Time',
            ])->rows($logs);
            $Table->display();
        }
    }

    /**
     * 对 Url 地址进行请求并且获取请求内容
     * @param $url
     */
    protected function visit($url)
    {
        try {

            $Curl = new Client();
            $resp = $Curl->get($url);
            $this->assertTrue(true);
            $this->visitContent = $resp->getBody()->getContents();
        } catch (Throwable $e) {
            $this->fail('Visit Url ' . $url . ' Failed,  Reason:' . $e->getMessage());
        }
    }

    /**
     * 输出变量
     * @param array|string $var
     * @deprecated 3.1
     * @removed    4.0
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