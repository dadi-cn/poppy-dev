<?php namespace Poppy\Framework\Update\Commands;

use Illuminate\Console\Command;
use Poppy\Framework\Update\Migration\MigrationRepositoryInterface;

class InstallCommand extends Command
{
	/**
	 * The console command name.
	 *
	 * @var string
	 */
	protected $name = 'poppy:update:install';

	/**
	 * The console command description.
	 *
	 * @var string
	 */
	protected $description = 'Create the update migration repository';

	/**
	 * The repository instance.
	 *
	 * @var MigrationRepositoryInterface
	 */
	protected $repository;

	/**
	 * Create a new migration install command instance.
	 *
	 * @param  MigrationRepositoryInterface $repository
	 * @return void
	 */
	public function __construct(MigrationRepositoryInterface $repository)
	{
		parent::__construct();

		$this->repository = $repository;
	}

	/**
	 * Execute the console command.
	 *
	 * @return void
	 */
	public function handle()
	{
		$this->repository->createRepository();

		$this->info('Update table created successfully.');
	}
}
