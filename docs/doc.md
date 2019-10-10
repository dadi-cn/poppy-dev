
```
├── composer.json
├── config
│   └── poppy.php
├── docs
│   ├── code-spec.md
│   ├── _config.yml
│   ├── doc.md
│   └── README_Zh-cn.md
├── phpunit.xml
├── README.md
├── resources
│   ├── lang
│   │   ├── en
│   │   │   └── resp.php
│   │   └── zh
│   │       ├── http.php
│   │       └── resp.php
│   ├── stubs
│   │   ├── console
│   │   │   ├── make_module_introduction.stub
│   │   │   └── make_module_step_1.stub
│   │   └── poppy
│   │       ├── configurations
│   │       ├── docs
│   │       ├── manifest.json
│   │       ├── resources
│   │       │   ├── images
│   │       │   ├── lang
│   │       │   │   └── zh
│   │       │   │       └── seo.php
│   │       │   ├── libs
│   │       │   ├── scss
│   │       │   └── views
│   │       └── src
│   │           ├── action
│   │           ├── classes
│   │           │   └── functions.php
│   │           ├── database
│   │           │   ├── factories
│   │           │   ├── migrations
│   │           │   └── seeds
│   │           ├── events
│   │           ├── http
│   │           │   ├── request
│   │           │   │   ├── api
│   │           │   │   │   └── DemoController.php
│   │           │   │   ├── backend
│   │           │   │   │   └── DemoController.php
│   │           │   │   └── web
│   │           │   │       └── DemoController.php
│   │           │   ├── routes
│   │           │   │   ├── api.php
│   │           │   │   ├── backend.php
│   │           │   │   └── web.php
│   │           │   └── RouteServiceProvider.php
│   │           ├── jobs
│   │           ├── listeners
│   │           ├── models
│   │           │   ├── filters
│   │           │   ├── policies
│   │           │   └── resources
│   │           ├── ServiceProvider.php
│   │           └── testing
│   └── views
│       └── template
│           ├── default.blade.php
│           └── message.blade.php
├── src
│   ├── Agamotto
│   │   ├── Agamotto.php
│   │   └── AgamottoServiceProvider.php
│   ├── Application
│   │   ├── ApiController.php
│   │   ├── Controller.php
│   │   ├── Event.php
│   │   ├── Job.php
│   │   ├── Request.php
│   │   └── TestCase.php
│   ├── Classes
│   │   ├── ClassLoader.php
│   │   ├── Number.php
│   │   ├── Resp.php
│   │   └── Traits
│   │       ├── AppTrait.php
│   │       ├── HasAttributesTrait.php
│   │       ├── KeyParserTrait.php
│   │       ├── MigrationTrait.php
│   │       ├── PoppyTrait.php
│   │       └── ViewTrait.php
│   ├── Console
│   │   ├── Commands
│   │   │   ├── PoppyDisableCommand.php
│   │   │   ├── PoppyEnableCommand.php
│   │   │   ├── PoppyListCommand.php
│   │   │   ├── PoppyMigrateCommand.php
│   │   │   ├── PoppyMigrateRefreshCommand.php
│   │   │   ├── PoppyMigrateResetCommand.php
│   │   │   ├── PoppyMigrateRollbackCommand.php
│   │   │   ├── PoppyOptimizeCommand.php
│   │   │   └── PoppySeedCommand.php
│   │   ├── ConsoleServiceProvider.php
│   │   ├── GeneratorCommand.php
│   │   ├── Generators
│   │   │   ├── MakeCommandCommand.php
│   │   │   ├── MakeControllerCommand.php
│   │   │   ├── MakeMiddlewareCommand.php
│   │   │   ├── MakeMigrationCommand.php
│   │   │   ├── MakeModelCommand.php
│   │   │   ├── MakePolicyCommand.php
│   │   │   ├── MakePoppyCommand.php
│   │   │   ├── MakeProviderCommand.php
│   │   │   ├── MakeRequestCommand.php
│   │   │   ├── MakeSeederCommand.php
│   │   │   ├── MakeTestCommand.php
│   │   │   └── stubs
│   │   │       ├── command.stub
│   │   │       ├── controller.resource.stub
│   │   │       ├── controller.stub
│   │   │       ├── middleware.stub
│   │   │       ├── model.stub
│   │   │       ├── policy.stub
│   │   │       ├── provider.stub
│   │   │       ├── request.stub
│   │   │       ├── seeder.stub
│   │   │       └── test.stub
│   │   └── GeneratorServiceProvider.php
│   ├── Database
│   │   └── Migrations
│   │       └── Migrator.php
│   ├── Events
│   │   ├── LocaleChanged.php
│   │   └── PoppyMake.php
│   ├── Exceptions
│   │   ├── AjaxException.php
│   │   ├── ApplicationException.php
│   │   ├── ArithmeticException.php
│   │   ├── BaseException.php
│   │   ├── DoException.php
│   │   ├── LoadConfigurationException.php
│   │   ├── ModuleNotFoundException.php
│   │   ├── ParamException.php
│   │   ├── PolicyException.php
│   │   ├── RbacException.php
│   │   ├── RuntimeException.php
│   │   └── TransactionException.php
│   ├── Filesystem
│   │   └── Filesystem.php
│   ├── Foundation
│   │   ├── Application.php
│   │   ├── Bootstrap
│   │   │   └── RegisterClassLoader.php
│   │   ├── Console
│   │   │   └── Kernel.php
│   │   ├── Contracts
│   │   │   └── Bootstrap.php
│   │   ├── Exception
│   │   │   └── Handler.php
│   │   └── Http
│   │       └── Kernel.php
│   ├── FrameworkServiceProvider.php
│   ├── Helper
│   │   ├── ArrayHelper.php
│   │   ├── CookieHelper.php
│   │   ├── EnvHelper.php
│   │   ├── FileHelper.php
│   │   ├── HtmlHelper.php
│   │   ├── ImageHelper.php
│   │   ├── RouterHelper.php
│   │   ├── SearchHelper.php
│   │   ├── StrHelper.php
│   │   ├── TimeHelper.php
│   │   ├── TreeHelper.php
│   │   ├── UtilHelper.php
│   │   └── WebHelper.php
│   ├── Http
│   │   ├── BladeServiceProvider.php
│   │   ├── Middlewares
│   │   │   ├── CrossPreflight.php
│   │   │   └── EnableCrossRequest.php
│   │   └── Pagination
│   │       └── PageInfo.php
│   ├── Parse
│   │   ├── Ini.php
│   │   ├── ParseServiceProvider.php
│   │   ├── Xml.php
│   │   └── Yaml.php
│   ├── Poppy
│   │   ├── Abstracts
│   │   │   └── Repository.php
│   │   ├── Contracts
│   │   │   └── Repository.php
│   │   ├── Events
│   │   │   └── PoppyOptimized.php
│   │   ├── FileRepository.php
│   │   ├── Poppy.php
│   │   └── PoppyServiceProvider.php
│   ├── Support
│   │   ├── Abstracts
│   │   │   └── Repository.php
│   │   ├── functions.php
│   │   └── PoppyServiceProvider.php
│   ├── Translation
│   │   ├── TranslationServiceProvider.php
│   │   └── Translator.php
│   └── Validation
│       └── Rule.php
└── tests
    ├── Helper
    │   ├── ArrayHelperTest.php
    │   ├── EnvHelperTest.php
    │   └── TimeHelperTest.php
    └── Poppy
        ├── AgamottoTest.php
        └── PoppyTest.php
```