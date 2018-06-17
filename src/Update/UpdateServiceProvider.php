<?php namespace Poppy\Framework\Update;

use Illuminate\Support\ServiceProvider;
use Poppy\Framework\Update\Migration\DatabaseMigrationRepository;
use Poppy\Framework\Update\Migration\MigrationCreator;
use Poppy\Framework\Update\Migration\MigrationRepositoryInterface;
use Poppy\Framework\Update\Migration\Migrator;


class UpdateServiceProvider extends ServiceProvider
{
	/**
	 * Indicates if loading of the provider is deferred.
	 *
	 * @var bool
	 */
	protected $defer = true;

	/**
	 * Register the service provider.
	 *
	 * @return void
	 */
	public function register()
	{
		$this->registerRepository();

		$this->registerMigrator();

		$this->registerCreator();

		$this->registerCommands();
	}

	private function registerCommands()
	{
		$this->commands([
			Commands\InstallCommand::class,
			Commands\MigrateMakeCommand::class,
			Commands\MigrateCommand::class,
			Commands\StatusCommand::class,
			Commands\RollbackCommand::class,
		]);
	}

	/**
	 * Register the migration repository service.
	 *
	 * @return void
	 */
	protected function registerRepository()
	{
		$this->app->singleton('update.repository', function ($app) {
			$table = $app['config']['poppy.database.update'];

			return new DatabaseMigrationRepository($app['db'], $table);
		});

		// bind with class
		$this->app->alias('update.repository', MigrationRepositoryInterface::class);
	}

	/**
	 * Register the migrator service.
	 *
	 * @return void
	 */
	protected function registerMigrator()
	{
		// The migrator is responsible for actually running and rollback the migration
		// files in the application. We'll pass in our database connection resolver
		// so the migrator can resolve any of these connections when it needs to.
		$this->app->singleton('updator', function ($app) {
			$repository = $app['update.repository'];

			return new Migrator($repository, $app['db'], $app['files']);
		});

		$this->app->alias('updator', Migrator::class);
	}

	/**
	 * Register the migration creator.
	 *
	 * @return void
	 */
	protected function registerCreator()
	{
		$this->app->singleton('update.creator', function ($app) {
			return new MigrationCreator($app['files'], $app['updator']);
		});

		// bind with class
		$this->app->alias('update.creator', MigrationCreator::class);
	}

	/**
	 * Get the services provided by the provider.
	 *
	 * @return array
	 */
	public function provides(): array
	{
		return [
			'updator',
			'update.repository',
			'update.creator',
		];
	}
}
