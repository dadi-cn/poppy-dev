<?php namespace Poppy\Framework\Update\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Composer;
use Illuminate\Support\Str;
use Poppy\Framework\Update\Migration\MigrationCreator;

class MigrateMakeCommand extends Command
{
	/**
	 * The console command signature.
	 *
	 * @var string
	 */
	protected $signature = 'poppy:update:migration 
	    {slug : The slug of the module.}
    	{name : The name of the update.}
		';

	/**
	 * The console command description.
	 *
	 * @var string
	 */
	protected $description = 'Create a new update migration file';

	/**
	 * The migration creator instance.
	 *
	 * @var MigrationCreator
	 */
	protected $creator;

	/**
	 * The Composer instance.
	 *
	 * @var \Illuminate\Support\Composer
	 */
	protected $composer;

	private $slug;

	private $updateName;

	/**
	 * Create a new migration install command instance.
	 *
	 * @param  MigrationCreator             $creator
	 * @param  \Illuminate\Support\Composer $composer
	 * @return void
	 */
	public function __construct(MigrationCreator $creator, Composer $composer)
	{
		parent::__construct();

		$this->creator  = $creator;
		$this->composer = $composer;
	}

	/**
	 * Execute the console command.
	 *
	 * @return void
	 * @throws \Exception
	 */
	public function handle()
	{
		$this->slug = $this->input->getArgument('slug');

		if (!app('poppy')->exists($this->slug)) {
			$this->error('Slug `' . $this->slug . '` not exist!');

			return;
		}

		// It's possible for the developer to specify the tables to modify in this
		// schema operation. The developer may also specify if this table needs
		// to be freshly created so we can create the appropriate migrations.
		$this->updateName = Str::snake(trim($this->input->getArgument('name')));

		// Now we are ready to write the migration out to disk. Once we've written
		// the migration out, we will dump-autoload for the entire framework to
		// make sure that the migrations are registered by the class loaders.
		$this->writeMigration($this->slug, $this->updateName);

		$this->composer->dumpAutoloads();
	}

	/**
	 * Write the migration file to disk.
	 *
	 * @param  string $slug
	 * @param  string $name
	 * @return string
	 * @throws \Exception
	 */
	protected function writeMigration($slug, $name)
	{
		$file = pathinfo($this->creator->create(
			$slug,
			$name
		), PATHINFO_FILENAME);

		$this->line("<info>Created Update Migration:</info> {$file}");
	}

	/**
	 * Get migration path (either specified by '--path' option or default location).
	 *
	 * @return string
	 */
	protected function getMigrationPath()
	{
		try {
			return poppy_path($this->slug, 'src/update/');
		} catch (\Exception $e) {
			return '';
		}
	}
}
