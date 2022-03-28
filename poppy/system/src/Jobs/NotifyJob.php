<?php

namespace Poppy\System\Jobs;

use Carbon\Carbon;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Poppy\Framework\Application\Job;
use Poppy\Framework\Helper\ArrayHelper;
use Poppy\Framework\Helper\UtilHelper;
use Poppy\System\Classes\Traits\ListenerTrait;
use Psr\Http\Message\ResponseInterface;

/**
 * 回调执行
 */
class NotifyJob extends Job implements ShouldQueue
{
    use ListenerTrait, Queueable;

    /**
     * @var string 请求网址
     */
    private string $url;

    /**
     * @var string 请求方法
     */
    private string $method;

    /**
     * @var array 请求参数
     */
    private array $params;

    /**
     * @var int 请求次数
     */
    private int $execNum;

    /**
     * 统计用户计算数量
     * @param string $url 请求的URL 地址
     * @param string $method 请求的方法
     * @param array $params 请求的参数
     * @param int $exec_num 请求次数
     */
    public function __construct(string $url, string $method, array $params = [], int $exec_num = 0)
    {
        $this->url     = $url;
        $this->method  = $method;
        $this->params  = $params;
        $this->execNum = $exec_num;
    }

    /**
     * 执行
     */
    public function handle()
    {
        /* 重发次数
         -------------------------------------------- */
        if ($time = sys_setting('py-system::callback.exam_time')) {
            $timeMap = explode(',', $time);
        }
        else {
            $timeMap = [
                0 => 10,
                1 => 30,
                2 => 60,
            ];
        }

        $curl    = new Client();
        $options = [

            'timeout' => 10
        ];
        try {
            if ($this->method === 'post') {
                $resp = $curl->post($this->url, array_merge($options, [
                    'form_params' => $this->params,
                ]));
            }
            else {
                $resp = $curl->get($this->url, array_merge([
                    'query' => $this->params,
                ]));
            }
            sys_info('py-system', self::class, $this->log($resp));
        } catch (GuzzleException $e) {
            if ($this->execNum < count($timeMap)) {
                $delayDesc = 'next will exec at (' . Carbon::now()->addSeconds($timeMap[$this->execNum])->toDateTimeString() . ')(' . $timeMap[$this->execNum] . 's)';
                sys_error('py-system', self::class, $this->log($e, $delayDesc));
                dispatch((new self($this->url, $this->method, $this->params, $this->execNum + 1))->delay($timeMap[$this->execNum]));
            }
            else {
                sys_error('py-system', self::class, $this->log($e));
            }
        }
    }

    /**
     * 生成记录日志
     * @param GuzzleException|ResponseInterface $result
     * @param string $append
     * @return string
     */
    private function log($result, string $append = ''): string
    {
        $resp = '';
        if ($result instanceof ResponseInterface) {
            $content = $result->getBody()->getContents();
            if (UtilHelper::isJson($content)) {
                $result = json_decode($content, true);
                $resp   = ArrayHelper::toKvStr($result);
            }
            else {
                $resp = $content;
            }
        }
        if ($result instanceof GuzzleException) {
            $resp = $result->getMessage();
        }
        $kvParams = ArrayHelper::toKvStr($this->params);

        return ($this->execNum + 1) . '\'s Request:' .
            "url : {$this->url}, method : {$this->method}" .
            ", params : {$kvParams}, result : {$resp} " .
            ($append ? ", tip : {$append}" : '');
    }
}