<?php

namespace Poppy\Core\Commands;

use DB;
use Illuminate\Console\Command;
use Illuminate\Contracts\Filesystem\FileNotFoundException;
use Illuminate\Routing\Route;
use Illuminate\Support\Str;
use Poppy\Core\Classes\Inspect\CommentParser;
use Poppy\Core\Classes\PyCoreDef;
use Poppy\Core\Classes\Traits\CoreTrait;
use Poppy\Framework\Classes\Traits\KeyParserTrait;
use ReflectionClass;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Finder\SplFileInfo;
use Throwable;

/**
 * 检查代码规则
 */
class InspectCommand extends Command
{
    use KeyParserTrait, CoreTrait;

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'py-core:inspect
		{type? : Support type need to input, [apidoc, method, file, db, env]}
		{--dir= : The dir to check with directory}
		{--module= : The module to check}
		{--export= : The module to check}
		{--class_load_only : Only load class with not show tables}
		{--log : Is Display Request Log}

	';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Inspect code style';

    /**
     * @var array  File Rules
     */
    private $fileRules = [];

    /**
     * @var array Name Rules
     */
    private $nameRules = [];

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $type = $this->argument('type');
        switch ($type) {
            case 'apidoc':
                $dir = $this->option('dir');
                if (!$dir) {
                    $this->warn('You need point the directory to check!');

                    return;
                }
                $this->inspectApidoc();
                break;
            case 'class':
                $this->inspectClass();
                break;
            case 'file':
                $this->inspectFile();
                break;
            case 'db':
            case 'database':
                $this->inspectDatabase();
                break;
            case 'controller':
                $this->inspectController();
                break;
            case 'action':
                $this->inspectAction();
                break;
            case 'seo':
                $this->inspectSeo();
                break;
            case 'db_seo':
                $this->inspectDbSeo();
                break;
            case 'trans':
                $this->inspectTrans();
                break;
            default:
                $dirs = array_keys(config('poppy.core.apidoc'));
                if (count($dirs)) {
                    foreach ($dirs as $dir) {
                        $this->call('py-core:inspect', [
                            'type'  => 'apidoc',
                            '--dir' => $dir,
                        ]);
                    }
                }

                // batch seo
                $modules = $this->coreModule()->enabled()->keys();
                $modules->each(function ($module) {
                    $this->call('py-core:inspect', [
                        'type'     => 'seo',
                        '--module' => $module,
                    ]);
                });

                $this->call('py-core:inspect', [
                    'type' => 'name',
                ]);

                $this->call('py-core:inspect', [
                    'type' => 'db',
                ]);

                $this->call('py-core:inspect', [
                    'type' => 'class',
                ]);

                $this->call('py-core:inspect', [
                    'type' => 'pages',
                ]);
                break;
        }
    }

    private function inspectTrans()
    {
        $export = $this->option('export');
        try {
            $content = app('files')->get($export);
        } catch (Throwable $e) {
            $this->error(sys_mark('poppy.core', __CLASS__, $e->getMessage()));
            return;
        }
        if (preg_match_all("/trans\((.*)?['\"]/", $content, $matches, PREG_PATTERN_ORDER)) {
            $uniTrans = array_unique($matches[1]);
            if (!count($uniTrans)) {
                $this->error(sys_mark('poppy.core', __CLASS__, '没有可以匹配的条目'));
                return;
            }
            $trans = [];
            foreach ($uniTrans as $tran) {
                $tran = trim($tran, '\'"');
                if (trans($tran) === $tran) {
                    $trans[] = [$tran];
                }
            }
            $this->table(['Trans'], $trans);
        }
    }

    /**
     * 生成 seo 项目
     */
    private function inspectSeo()
    {
        $seoList         = [];
        $unUniformedKeys = [];
        collect(\Route::getRoutes())->map(function (Route $route) use (&$seoList, &$unUniformedKeys) {
            $name = $route->getName();
            if (!$name) {
                return;
            }
            if (!preg_match('/(.+?):(.+?)\.(.+?)\.(.+?)/', $name, $match)) {
                $unUniformedKeys[] = [
                    $name,
                ];
                return;
            }

            $module   = Str::before($name, ':');
            $seoKey   = str_replace([':', '.', '::'], ['::', '_', '::seo.'], $name);
            $transKey = trans($seoKey);
            if ($transKey === $seoKey || $transKey === '') {
                $seoList[] = [
                    $module,
                    '\'' . str_replace([$module . ':', '.'], ['', '_'], $name) . '\' => \'\', ',
                ];
            }
        });

        if (count($unUniformedKeys)) {
            $this->warn('[Inspect: Uniformed Route Url]');
            $this->table(['Route Name'], $unUniformedKeys);
        }


        $this->warn('[Inspect: Seo Names]');
        if ($seoList) {
            $this->table(['Module', 'Key'], $seoList);
        }
        else {
            $this->info('Perfect, Seo rule are matched');
        }
    }

    /**
     * 检查数据库配置
     */
    private function inspectDatabase()
    {
        $tables = array_map('reset', DB::select('show tables'));

        $suggestString   = function ($col) {
            if (strpos($col['Type'], 'char') !== false) {
                if ($col['Null'] === 'YES') {
                    return '(Char-null)';
                }
                if ($col['Default'] !== '' && $col['Default'] !== null) {
                    if (!is_string($col['Default'])) {
                        return '(Char-default)';
                    }
                }
            }

            return '';
        };
        $suggestInt      = function ($col) {
            if (strpos($col['Type'], 'int') !== false) {
                switch ($col['Key']) {
                    case 'PRI':
                        // 主键不能为Null (Allow Null 不可选)
                        // Default 不可填入值
                        // 所以无任何输出
                        break;
                    default:
                        if (!is_numeric($col['Default'])) {
                            return '(Int-default)';
                        }
                        if ($col['Null'] === 'YES') {
                            return '(Int-Null)';
                        }
                        break;
                }
            }

            return '';
        };
        $suggestDecimal  = function ($col) {
            if (strpos($col['Type'], 'decimal') !== false) {
                if ($col['Default'] !== '0.00') {
                    return '(Decimal-default)';
                }
                if ($col['Null'] === 'YES') {
                    return '(Decimal-Null)';
                }
            }

            return '';
        };
        $suggestDatetime = function ($col) {
            if (strpos($col['Type'], 'datetime') !== false) {
                if ($col['Default'] !== null) {
                    return '(Datetime-default)';
                }
                if ($col['Null'] === 'NO') {
                    return '(Datetime-null)';
                }
            }

            return '';
        };
        $suggestFloat    = function ($col) {
            if (strpos($col['Type'], 'float') !== false) {
                return '(Float-set)';
            }

            return '';
        };

        $suggest = [];
        foreach ($tables as $table) {
            $columns = DB::select('show full columns from ' . $table);
            /*
             * column 字段
             * Field      : account_no
             * Type       : varchar(100)
             * Collation  : utf8_general_ci
             * Null       : NO
             * Key        : ""
             * Default    : ""
             * Extra      : ""
             * Privileges : select,insert,update,references
             * Comment    : 账号
             * ---------------------------------------- */

            foreach ($columns as $column) {
                $column     = (array) $column;
                $colSuggest =
                    $suggestString($column) .
                    $suggestInt($column) .
                    $suggestDecimal($column) .
                    $suggestDatetime($column);
                $suggestFloat($column);
                $column['suggest'] = $colSuggest;
                if ($colSuggest) {
                    $suggest[] = [
                        $table,
                        data_get($column, 'Field'),
                        data_get($column, 'Type'),
                        data_get($column, 'Null'),
                        data_get($column, 'suggest'),
                        data_get($column, 'Comment'),
                    ];
                }
            }
        }
        if ($suggest) {
            $this->table(['Table', 'Field', 'Type', 'IsNull', 'Advice', 'Comment'], $suggest);
        }
    }

    /**
     * Apidoc 检测
     */
    private function inspectApidoc()
    {
        $dir         = $this->option('dir');
        $file        = app('files');
        $appendCheck = (array) config('poppy.core.apidoc.' . $dir . '.check');
        $jsonFile    = base_path('public/docs/' . $dir . '/api_data.json');
        if (!$file->exists($jsonFile)) {
            $this->warn('ApiDoc not exist, run `php artisan system:doc api` to generate.');

            return;
        }
        $funToTable = function ($field, $type, $url) {
            $field['description'] = strip_tags($field['description']);

            return [
                $url,
                $field['field'],
                $field['type'],
                $type,
                $field['description'],
            ];
        };

        $match = function ($field, $url, $type, &$table) use ($funToTable, $appendCheck) {
            $match = [
                    'id'           => 'Integer',
                    'title'        => 'String',
                    'page'         => 'Integer',
                    'pagesize'     => 'Integer',
                    'start_at'     => 'Date',
                    'end_at'       => 'Date',
                    'created_at'   => 'String',
                    'success_at'   => 'String',
                    'failed_at'    => 'String',
                    'amount'       => 'String',
                    'status'       => 'String',
                    'app_id'       => 'String',
                    'app_key'      => 'String',
                    'fee'          => 'String',
                    'note'         => 'String',
                    '*_at'         => 'String',
                    '*_reason'     => 'String',
                    '*_no'         => 'String',
                    '*_id'         => 'Integer',
                    '*_success_at' => 'String',
                    '*_failed_at'  => 'String',
                ] + $appendCheck;

            foreach ($match as $item_key => $item) {
                if ($field['field'] === $item_key && $field['type'] !== $item) {
                    $table[] = $funToTable($field, $type, $url);
                    continue;
                }
                if (strpos($item_key, '*') !== false) {
                    if (array_key_exists($field['field'], $match)) {
                        continue;
                    }
                    $string = ltrim($item_key, '*');
                    if ($field['type'] !== $item && Str::contains($field['field'], $string)) {
                        $table[] = $funToTable($field, $type, $url);
                    }
                    continue;
                }
            }
        };

        try {
            $arrApi = json_decode($file->get($jsonFile), true);
        } catch (FileNotFoundException $e) {
            $this->warn($e->getMessage());

            return;
        }
        $table = [];
        foreach ($arrApi as $api) {
            $url = $api['url'];

            $params    = data_get($api, 'parameter.fields.Parameter');
            $successes = data_get($api, 'success.fields.Success 200');

            if ($params && count($params)) {
                foreach ($params as $param) {
                    $match($param, $url, 'param', $table);
                }
            }

            if ($successes && count($successes)) {
                foreach ($successes as $success) {
                    $match($success, $url, 'success', $table);
                }
            }
        }

        $this->warn('[Inspect:Apidoc:' . $dir . ']');
        if (count($table)) {
            $this->table(['Url', 'Field', 'Type', 'Input/Output', 'Description'], $table);
        }
        else {
            $this->info('Apidoc Comment Check Success!');
        }
    }

    /**
     * 方法检测
     */
    private function inspectClass()
    {
        $optClassLoadOnly = $this->option('class_load_only');

        $baseDir = base_path();
        $folders = array_merge(
            glob($baseDir . '/{poppy,modules}/*/src', GLOB_BRACE)
        );

        $table      = [];
        $classTable = [];
        foreach ($folders as $dir) {
            $files = app('files')->allFiles($dir);
            foreach ($files as $file) {
                $pathName = $file->getPathname();

                $module = $this->moduleName($pathName);

                // 排除指定的类
                if (Str::contains($pathName, [
                    'database/', 'Database/', 'update/', 'functions.php', 'ServiceProvider', 'autoload',
                    'http/routes/', 'Http/Routes/', '.sql', '.txt', '.pem', '.xml', '.md', '.yaml', '.table', '.stub',
                    'TestCase.php',
                ])) {
                    continue;
                }

                // 模块名称解析错误
                if (!$module) {
                    $this->warn('Error module name in path:' . $pathName);
                    return;
                }

                $slug = '';
                if ($module['type'] === 'modules') {
                    $slug = 'module.' . $module['module'];
                }
                if ($module['type'] === 'poppy') {
                    $slug = 'poppy.' . $module['module'];
                }

                $relativePath = $file->getRelativePath();
                $className    = $this->className($slug, $relativePath, $file->getFilename());

                try {
                    $refection = new ReflectionClass($className);
                } catch (Throwable $e) {
                    $classTable[] = [
                        $slug,
                        $e->getMessage(),
                    ];
                    continue;
                }

                $properties = $refection->getProperties();
                $varDesc    = [];
                foreach ($properties as $property) {
                    if ($property->class !== $className) {
                        continue;
                    }
                    // action variable do not need
                    if (strpos($className, '\\Models\\') !== false && in_array($property->getName(), [
                            'timestamps', 'table', 'fillable', 'primaryKey', 'dates',
                        ], true)) {
                        continue;
                    }
                    if (strpos($className, '\\Commands\\') !== false && in_array($property->getName(), [
                            'signature', 'description',
                        ], true)) {
                        continue;
                    }

                    if (!$property->getDocComment()) {
                        $varDesc[] = [
                            $slug,
                            '',
                            '$' . $property->name,
                            '[comment missing]',
                        ];
                    }

                    // 检测 CamelCase
                    if (!$this->isCamelCase($property->getName())) {
                        $varDesc[] = [
                            $slug,
                            '',
                            '',
                            'param : => ' . '$' . Str::camel($property->getName()),
                        ];
                    }

                }
                $methods = $refection->getMethods();
                if ($methods === null) {
                    continue;
                }

                $methodDesc = [];

                foreach ($methods as $method) {
                    $methodName = $method->getName();
                    if (
                        in_array($methodName, [
                            'handle', '',
                        ], true)
                        &&
                        (
                            strpos($className, '\\Listeners\\') !== false
                            ||
                            strpos($className, '\\Middlewares\\') !== false
                        )) {
                        continue;
                    }

                    // 排除继承的方法
                    if ($method->class !== $className) {
                        continue;
                    }

                    // 排除魔术方法
                    if (Str::startsWith($methodName, '__')) {
                        continue;
                    }

                    // 不是本文件中的. 略过
                    if (basename($method->getFileName()) !== basename($file->getRealPath())) {
                        continue;
                    }

                    // 检查 注释
                    $comment = $method->getDocComment();
                    $item    = [
                        $slug,
                        '',
                        $methodName,
                    ];
                    if (!$comment) {
                        $item[]       = '[comment: missing]';
                        $methodDesc[] = $item;
                    }
                    else {
                        $Parser      = new CommentParser();
                        $parsed      = $Parser->parseMethod($comment);
                        $commentDesc = '';
                        if (count($parsed['params'])) {
                            foreach ($parsed['params'] as $param) {
                                $name = $param['var_name'] ?? '';
                                if (!$name) {
                                    continue;
                                }

                                $desc = $param['var_desc'] ?? '';
                                $type = $param['var_type'] ?? '';
                                if (!$desc || !$type) {
                                    $commentDesc    .= "{$name} ";
                                    $varCommentDesc = '';
                                    if (!$type) {
                                        $varCommentDesc .= 'type:' . ($type ?: '--') . ',';
                                    }
                                    if (!$desc) {
                                        $varCommentDesc .= 'desc:' . ($desc ?: '--') . ',';
                                    }
                                    $commentDesc .= $varCommentDesc ? '[' . rtrim($varCommentDesc, ',') . ']' : '';
                                    $commentDesc .= "\n";
                                }
                            }
                        }

                        if ($commentDesc) {
                            $item[]       = rtrim($commentDesc);
                            $methodDesc[] = $item;
                        }

                        // 代码是否已经审核
                        if (isset($parsed['verify'])) {
                            $item[]       = '';
                            $item[]       = 'method: ' . $methodName . ': verify √';
                            $methodDesc[] = $item;
                        }
                    }

                    // 检测 CamelCase
                    if (!$this->isCamelCase($methodName)) {
                        $methodDesc[] = [
                            $slug,
                            '',
                            '',
                            'method : => ' . Str::camel($methodName),
                        ];
                    }
                }

                $trimComment       = str_replace(['/', '*', "\t", "\n", ' '], '', $refection->getDocComment());
                $docCommentMissing = true;
                if ($trimComment) {
                    $docCommentMissing = false;
                }
                if ($docCommentMissing || $varDesc || $methodDesc) {
                    $table[] = [
                        $slug,
                        $className,
                        '',
                        $docCommentMissing ? '[class doc missing]' : '',
                    ];
                    foreach (array_merge($varDesc, $methodDesc) as $item) {
                        $table[] = $item;
                    }
                }
            }
        }

        if ($optClassLoadOnly) {
            return;
        }

        $this->warn('[Inspect:Comment]');
        if ($table) {
            if ($this->option('module')) {
                $num   = 1;
                $table = collect($table)->filter(function ($item) {
                    return stripos($item[0], $this->option('module')) === 0;
                })->map(function ($item) use (&$num) {
                    array_unshift($item, $num++);

                    return $item;
                })->toArray();
                $this->table(['Id', 'Module', 'Class Name', 'Method', 'Comment'], $table);
            }
            else {
                $this->table(['Module', 'Class Name', 'Method', 'Comment', 'Verify'], $table);
            }
        }
        else {
            $this->info('So good, You did not has bad design.');
        }
        $this->warn('[Inspect:Module Namespace]');
        if ($classTable) {
            $this->table(['Module', 'Tips'], $classTable);
        }
        else {
            $this->info('So good, You did not has bad design in module.');
        }
    }

    /**
     * 检查文件命名
     */
    private function inspectFile()
    {
        $baseDir  = base_path();
        $folders  = glob($baseDir . '/{modules}/*/src/{events,listeners,models}', GLOB_BRACE);
        $iterator = Finder::create()
            ->files()
            ->name('*.php')
            ->in($folders);

        $checkFile = function (SplFileInfo $file) {
            $pathName = $file->getPathname();
            $fileName = $file->getFilename();
            $module   = function ($str) {
                if (preg_match('/modules\/(.+)\/src/', $str, $match)) {
                    return $match[1];
                }

                return '';
            };
            if (strpos($pathName, '/events/') !== false && substr(pathinfo($fileName)['filename'], -5) !== 'Event') {
                $this->nameRules[] = [
                    'module' => $module($pathName),
                    'file'   => $fileName,
                    'path'   => $pathName,
                ];
            }
            if (strpos($pathName, '/listeners/') !== false && substr(pathinfo($fileName)['filename'], -8) !== 'Listener') {
                $this->nameRules[] = [
                    'module' => $module($pathName),
                    'file'   => $fileName,
                    'path'   => $pathName,
                ];
            }

            if (strpos($pathName, '/policies/') !== false) {
                if (substr(pathinfo($fileName)['filename'], -6) !== 'Policy') {
                    $this->nameRules[] = [
                        'module' => $module($pathName),
                        'file'   => $fileName,
                        'path'   => $pathName,
                    ];
                }
                else {
                    $basePolicy = str_replace('Policy', '', pathinfo($fileName)['filename']);
                    $model      = poppy_path($module($pathName), 'src/models/' . $basePolicy . '.php');
                    if (!app('files')->exists($model)) {
                        $this->nameRules[] = [
                            'module' => $module($pathName),
                            'file'   => $fileName,
                            'path'   => $pathName,
                        ];
                    }
                }
            }
        };

        foreach ($iterator as $file) {
            $checkFile($file);
        }

        $this->warn('[Inspect:Name Rule]');
        if ($this->nameRules) {
            $this->table([
                'module' => 'Module', 'file' => 'FileName', 'path' => 'Path',
            ], $this->nameRules);
        }
        else {
            $this->info('Beautiful, Name rules are matched.');
        }
    }

    private function inspectDbSeo()
    {
        $modules = app('poppy')->enabled();
        $models  = [];
        $modules->each(function ($item) use (&$models) {
            $slug = $item['slug'];
            if (Str::startsWith($slug, 'poppy')) {
                $path = poppy_path($slug, '/src/Models/*.php');
            }
            else {
                $path = poppy_path($slug, '/src/models/*.php');
            }

            $files = glob($path);
            $seoDb = [];
            try {
                foreach ($files as $file) {
                    if (preg_match('/[mM]odels\/(?<model>[A-Za-z]+)\.php/', $file, $matches)) {
                        $key           = Str::snake($matches['model']);
                        $className     = poppy_class($slug, 'Models\\' . $matches['model']);
                        $ref           = new ReflectionClass($className);
                        $docComment    = $ref->getDocComment();
                        $CommentParser = new CommentParser();
                        $comments      = $CommentParser->parseMethod($docComment);
                        $params        = $comments['params'] ?? [];
                        $fields        = [];
                        collect($params)->where('type', 'property')->each(function ($item) use (&$fields) {
                            $desc  = $item['var_desc'] ?? '';
                            $field = str_replace('$', '', $item['var_name']);
                            if (preg_match('/(?<input_value>\[.*?])/', $desc ?? '', $match)) {
                                $desc = str_replace($match['input_value'], '', $desc);
                            }
                            $fields[$field] = $desc;
                        });
                        $seoDb[$key] = $fields;
                    }
                }
            } catch (Throwable $e) {
                $this->error($e->getMessage());
            }

            $models = array_merge($models, $seoDb);
        });
        sys_cache('py-core')->forever(PyCoreDef::ckLangModels(), $models);
        $this->info('Cached models Success!');
    }

    /**
     * 把所有的功能点都列出来
     */
    private function inspectController()
    {
        $baseDir = base_path();
        $folders = glob($baseDir . '/{modules}/*/src', GLOB_BRACE);

        $table = [];
        foreach ($folders as $dir) {
            $files = app('files')->allFiles($dir);
            foreach ($files as $file) {
                $pathName   = $file->getPathname();
                $moduleName = $this->moduleName($pathName);

                // 排除指定的类
                if (!Str::contains($pathName, ['http/request/'])) {
                    continue;
                }

                // 模块名称解析错误
                if (!$moduleName) {
                    $this->warn('Error module name in path:' . $pathName);

                    return;
                }

                if (!preg_match('/request\/([a-z0-9_]+)\/([A-Z0-9a-z]+)Controller.php/', $pathName, $match)) {
                    continue;
                }

                $requestGroup  = $match[1] ?? '';
                $requestAction = $match[2] ?? '';

                $relativePath = $file->getRelativePath();
                $className    = $this->className($moduleName, $relativePath, $file->getFilename());
                try {
                    $refection = new ReflectionClass($className);
                } catch (Throwable $e) {
                    $this->warn($moduleName . $e->getMessage());
                    continue;
                }

                $methods = $refection->getMethods();
                if ($methods === null) {
                    continue;
                }

                foreach ($methods as $method) {
                    $methodName = $method->getName();
                    // 排除继承的方法
                    if ($method->class !== $className) {
                        continue;
                    }

                    // 排除魔术方法
                    if (Str::startsWith($methodName, '__')) {
                        continue;
                    }

                    // 不是本文件中的. 略过
                    if (basename($method->getFileName()) !== basename($file->getRealPath())) {
                        continue;
                    }

                    // 检查 注释
                    $comment = $method->getDocComment();
                    $item    = [
                        $moduleName,
                        $requestGroup,
                        $requestAction,
                        $methodName,
                    ];
                    if (!$comment) {
                        $item[] = '[comment: missing]';
                    }
                    else {
                        $Parser = new CommentParser();
                        $parsed = $Parser->parseMethod($comment);
                        $item[] = str_replace(['/', PHP_EOL], '', $parsed['description'] ?? '');
                    }

                    $table[] = $item;
                }
            }
        }

        $this->table(['module', 'group', 'action', 'do', 'description'], $table);
    }

    /**
     * 把所有的业务逻辑都列出来
     */
    private function inspectAction()
    {
        $baseDir = base_path();
        $folders = glob($baseDir . '/{modules}/*/src/action', GLOB_BRACE);

        $table = [];
        foreach ($folders as $dir) {
            $files = app('files')->allFiles($dir);
            foreach ($files as $file) {
                $pathName   = $file->getPathname();
                $moduleName = $this->moduleName($pathName);

                // 排除指定的类
                if (!Str::contains($pathName, ['action/'])) {
                    continue;
                }

                // 模块名称解析错误
                if (!$moduleName) {
                    $this->warn('Error module name in path:' . $pathName);

                    return;
                }

                if (!preg_match('/action\/(\w+)\.php/', $pathName, $match)) {
                    continue;
                }

                $action = $match[1] ?? '';

                $className = $this->className($moduleName, 'action', $file->getFilename());

                try {
                    $refection = new ReflectionClass($className);
                } catch (Throwable $e) {
                    $this->warn($moduleName . $e->getMessage());
                    continue;
                }

                $methods = $refection->getMethods();
                if ($methods === null) {
                    continue;
                }

                foreach ($methods as $method) {
                    $methodName = $method->getName();
                    // 排除继承的方法
                    if (
                        $method->class !== $className
                        ||
                        $method->isPrivate()
                        ||
                        $method->isProtected()
                        ||
                        $method->isConstructor()
                        ||
                        Str::startsWith($methodName, ['set', 'get'])
                    ) {
                        continue;
                    }

                    // 不是本文件中的. 略过
                    if (basename($method->getFileName()) !== basename($file->getRealPath())) {
                        continue;
                    }

                    // 检查 注释
                    $comment = $method->getDocComment();
                    $item    = [
                        $moduleName,
                        $action,
                        $methodName,
                    ];
                    if (!$comment) {
                        $item[] = '[comment: missing]';
                    }
                    else {
                        $Parser      = new CommentParser();
                        $parsed      = $Parser->parseMethod($comment);
                        $description = trim($parsed['description'] ?? '', "\/\n");

                        $descriptions = explode(PHP_EOL, $description);
                        $item[]       = str_replace(['/', PHP_EOL], '', $descriptions[0] ?? '');
                    }

                    $table[] = $item;
                }
            }
        }

        $this->table(['module', 'action', 'do', 'description'], $table);
    }

    /**
     * 获取模块信息ß
     * @param mixed $path path
     * @return array
     */
    private function moduleName($path): array
    {
        if (preg_match('/\/(poppy|modules)\/([a-z-_]{1,20})\/src/', $path, $match)) {
            return [
                'type'   => $match[1],
                'module' => $match[2],
            ];
        }

        return [];
    }

    /**
     * 生成类名
     * @param string $module        模块
     * @param string $relative_path 相对路径
     * @param string $file_name     文件名
     * @return string
     */
    private function className(string $module, string $relative_path, string $file_name): string
    {
        if (Str::startsWith($module, 'module.')) {
            $m         = Str::after($module, 'module.');
            $className = ucfirst(Str::camel($m));
        }
        else {
            $m = Str::after($module, 'poppy.');
            if (Str::contains($m, 'ext-')) {
                $className = 'Poppy\\Extension\\' . ucfirst(Str::camel(Str::after($m, 'ext-')));
            }
            else {
                $className = 'Poppy\\' . ucfirst(Str::camel($m));
            }

        }
        $paths = explode('/', $relative_path);
        foreach ($paths as $path) {
            if (Str::contains($relative_path, ['Provider/en_', 'Provider/zh_'])) {
                $className .= '\\' . $path;
            }

            else {
                $className .= '\\' . ucfirst(Str::camel($path));
            }

        }
        $basename  = pathinfo($file_name);
        $className .= '\\' . $basename['filename'];

        return str_replace('\\\\', '\\', $className);
    }

    /**
     * 是否驼峰类型
     * @param string $str 字符串
     * @return bool
     */
    private function isCamelCase(string $str): bool
    {
        return Str::camel($str) === $str;
    }
}