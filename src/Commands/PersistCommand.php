<?php namespace Poppy\Core\Commands;


use Illuminate\Console\Command;
use Poppy\Core\Redis\RdsPersist;
use Throwable;

/**
 * Redis 持久化写入到数据库
 */
class PersistCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'py-core:persist
		{table : Table to exec. [pam_log...|all]}
	';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Redis Persistence To DataBase;';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $table = $this->argument('table');

        // 将所有数据写入数据库
        try {
            if ($table === 'all') {
                RdsPersist::exec();
            }
            else {
                RdsPersist::execTable($table);
            }
        } catch (Throwable $e) {
            $this->error(sys_mark('py-core', __CLASS__, $e->getMessage()));
        }

    }
}