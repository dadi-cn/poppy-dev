<?php

// config/sami.php
// php ./resources/sami/sami.phar update ./resources/sami/config.php

$baseDir  = dirname(dirname(__DIR__));
$folders  = glob($baseDir . '/{extensions,modules,framework}/*/src', GLOB_BRACE);
$excludes = [];
foreach ($folders as $folder) {
	$excludes[] = $folder . '/database/seeds';
	$excludes[] = $folder . '/database/migrations';
	$excludes[] = $folder . '/database/factories';
}

$iterator = Symfony\Component\Finder\Finder::create()
	->files()
	->name('*.php')
	->exclude('database')
	->in($folders);

$options = [
	'theme'     => 'default',
	'title'     => 'Lemon Framework API Documentation',
	'build_dir' => $baseDir . '/public/docs/php',
	'cache_dir' => $baseDir . '/storage/sami/cache',
];

$sami = new \Sami\Sami($iterator, $options);

return $sami;
