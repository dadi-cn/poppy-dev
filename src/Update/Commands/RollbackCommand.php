<?php namespace Poppy\Framework\Update\Commands;

use Illuminate\Console\ConfirmableTrait;
use Poppy\Framework\Update\Migration\Migrator;
use Symfony\Component\Console\Input\InputOption;

class RollbackCommand extends BaseCommand
{
	use ConfirmableTrait;

	/**
	 * The console command name.
	 *
	 * @var string
	 */
	protected $name = 'poppy:update:rollback';

	/**
	 * The console command description.
	 *
	 * @var string
	 */
	protected $description = 'Rollback the last update migration';

	/**
	 * The migrator instance.
	 *
	 * @var Migrator
	 */
	protected $migrator;

	/**
	 * Create a new migration rollback command instance.
	 *
	 * @param  Migrator $migrator
	 * @return void
	 */
	public function __construct(Migrator $migrator)
	{
		parent::__construct();

		$this->migrator = $migrator;
	}

	/**
	 * Execute the console command.
	 *
	 * @return void
	 */
	public function handle()
	{
		if (!$this->confirmToProceed()) {
			return;
		}

		$this->migrator->rollback($this->getMigrationPaths());

		// Once the migrator has run we will grab the note output and send it out to
		// the console screen, since the migrator itself functions without having
		// any instances of the OutputInterface contract passed into the class.
		foreach ($this->migrator->getNotes() as $note) {
			$this->output->writeln($note);
		}
	}

	/**
	 * Get the console command options.
	 *
	 * @return array
	 */
	protected function getOptions()
	{
		return [
			['force', null, InputOption::VALUE_NONE, 'Force the operation to run when in production.'],
		];
	}
}
