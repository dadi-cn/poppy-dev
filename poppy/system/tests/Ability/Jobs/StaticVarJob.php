<?php

namespace Poppy\System\Tests\Ability\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Poppy\Framework\Application\Job;
use Poppy\Framework\Helper\ArrayHelper;

/**
 * 测试静态变量在队列中是否追加, Sync 由于是单进程持续执行, 所以队列数据会追加
 * 而 Listen 方式则是单进程,多条单进程, 彼此独立, 所以数据不会追加, 所以不要在队列中使用静态变量
 */
class StaticVarJob extends Job implements ShouldQueue
{
    use Queueable;

    /**
     * 脚本目录
     * @var int $shellPath
     */
    private int $var;

    /**
     * Create a new job instance.
     * @param int $appendVar 追加的变量
     */
    public function __construct(int $appendVar)
    {
        $this->var = $appendVar;
    }

    /**
     * Execute the job.
     * @return void
     */
    public function handle()
    {
        static $vars;
        $vars[] = $this->var;
        sys_info('testing', self::class, 'vars:' . ArrayHelper::toKvStr($vars));
        if ($this->var < 20) {
            dispatch(new self($this->var + 1))->delay(1);
        }
    }
}
