<?php namespace Poppy\Framework\Update\Commands;

use Illuminate\Support\Collection;
use Poppy\Framework\Update\Migration\Migrator;

class StatusCommand extends BaseCommand
{
	/**
	 * The console command name.
	 *
	 * @var string
	 */
	protected $name = 'poppy:update:status';

	/**
	 * The console command description.
	 *
	 * @var string
	 */
	protected $description = 'Show the status of each update migration';

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

		if (!$this->migrator->repositoryExists()) {
			$this->error('No migrations found.');
			return;
		}

		$ran = $this->migrator->getRepository()->getRan();

		if (count($migrations = $this->getStatusFor($ran)) > 0) {
			$this->table(['Ran?', 'Migration'], $migrations);
		}
		else {
			$this->error('No migrations found');
		}
	}

	/**
	 * Get the status for the given ran migrations.
	 *
	 * @param  array $ran
	 * @return \Illuminate\Support\Collection
	 */
	protected function getStatusFor(array $ran)
	{
		return Collection::make($this->getAllMigrationFiles())
			->map(function ($migration) use ($ran) {
				$migrationName = $this->migrator->getMigrationName($migration);

				return in_array($migrationName, $ran)
					? ['<info>Y</info>', $migrationName]
					: ['<fg=red>N</fg=red>', $migrationName];
			});
	}

	/**
	 * Get an array of all of the migration files.
	 *
	 * @return array
	 */
	protected function getAllMigrationFiles()
	{
		return $this->migrator->getMigrationFiles(
			$this->getMigrationPaths()
		);
	}

}
