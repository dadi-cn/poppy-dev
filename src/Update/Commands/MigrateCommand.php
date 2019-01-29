<?php namespace Poppy\Framework\Update\Commands;

use Illuminate\Console\ConfirmableTrait;
use Poppy\Framework\Poppy\Poppy;
use Poppy\Framework\Update\Migration\Migrator;

class MigrateCommand extends BaseCommand
{
	use ConfirmableTrait;

	/**
	 * The name and signature of the console command.
	 *
	 * @var string
	 */
	protected $signature = 'poppy:update:migrate 
                {slug? : The module to update.}
                {--force : Force the operation to run when in production.}';

	/**
	 * The console command description.
	 *
	 * @var string
	 */
	protected $description = 'Run the update migrations';

	/**
	 * The migrator instance.
	 *
	 * @var Migrator
	 */
	protected $migrator;

	/**
	 * @var Poppy Poppy Manager
	 */
	protected $poppy;

	/**
	 * Create a new migration command instance.
	 *
	 * @param  Migrator $migrator
	 * @param Poppy     $poppy
	 */
	public function __construct(Migrator $migrator, Poppy $poppy)
	{
		parent::__construct();

		$this->migrator = $migrator;
		$this->poppy    = $poppy;
	}

	/**
	 * Execute the console command.
	 *
	 */
	public function handle()
	{
		if (!$this->confirmToProceed()) {
			return;
		}

		$this->prepareDatabase();

		if (!empty($this->argument('slug'))) {
			$module = $this->poppy->where('slug', $this->argument('slug'));

			if ($this->poppy->isEnabled($module['slug'])) {
				$this->migrate($module['slug']);

				return;
			}

			if ($this->option('force')) {
				$this->migrate($module['slug']);

				return;
			}

			$this->error('Nothing to migrate.');

			return;
		}

		if ($this->option('force')) {
			$modules = $this->poppy->all();
		}
		else {
			$modules = $this->poppy->enabled();
		}

		foreach ($modules as $module) {
			$this->migrate($module['slug']);
		}
	}

	/**
	 * Prepare the migration database for running.
	 *
	 * @return void
	 */
	protected function prepareDatabase()
	{
		if (!$this->migrator->repositoryExists()) {
			$this->call(
				'poppy:update:install'
			);
		}
	}

	/**
	 * Run migrations for the specified module.
	 * @param string $slug
	 * @return null
	 */
	protected function migrate($slug)
	{
		if ($this->poppy->exists($slug)) {
			$module = $this->poppy->where('slug', $slug);
			$path   = $this->getMigrationPath($slug);

			$this->migrator->run($path);

			event($slug . '.poppy.updated', [$module, $this->option()]);

			// Once the migrator has run we will grab the note output and send it out to
			// the console screen, since the migrator itself functions without having
			// any instances of the OutputInterface contract passed into the class.
			foreach ($this->migrator->getNotes() as $note) {
				$this->line($note);
			}
		}
		else {
			$this->error('Module does not exist.');

			return null;
		}

		return null;
	}
}
