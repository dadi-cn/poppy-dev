<?php namespace Poppy\Framework\Console\Commands;

use Illuminate\Console\Command;
use Poppy\Framework\Poppy\Poppy;

class PoppyListCommand extends Command
{
	/**
	 * The console command name.
	 * @var string
	 */
	protected $name = 'poppy:list';

	/**
	 * The console command description.
	 * @var string
	 */
	protected $description = 'List all application modules';

	/**
	 * @var Poppy
	 */
	protected $poppy;

	/**
	 * The table headers for the command.
	 * @var array
	 */
	protected $headers = ['#', 'Name', 'Slug', 'Description', 'Status'];

	/**
	 * Create a new command instance.
	 * @param Poppy $poppy
	 */
	public function __construct(Poppy $poppy)
	{
		parent::__construct();

		$this->poppy = $poppy;
	}

	/**
	 * Execute the console command.
	 * @return mixed
	 */
	public function handle()
	{
		$modules = $this->poppy->all();

		if (count($modules) == 0) {
			$this->error("Your application doesn't have any modules.");
			return null;
		}

		$this->displayModules($this->getModules());
	}

	/**
	 * Get all modules.
	 * @return array
	 */
	protected function getModules()
	{
		$modules = $this->poppy->all();
		$results = [];

		foreach ($modules as $module) {
			$results[] = $this->getModuleInformation($module);
		}

		return array_filter($results);
	}

	/**
	 * Returns module manifest information.
	 * @param string $module
	 * @return array
	 */
	protected function getModuleInformation($module)
	{
		return [
			'#'           => $module['order'],
			'name'        => isset($module['name']) ? $module['name'] : '',
			'slug'        => $module['slug'],
			'description' => isset($module['description']) ? $module['description'] : '',
			'status'      => ($this->poppy->isEnabled($module['slug'])) ? 'Enabled' : 'Disabled',
		];
	}

	/**
	 * Display the module information on the console.
	 * @param array $modules
	 */
	protected function displayModules(array $modules)
	{
		$this->table($this->headers, $modules);
	}
}
