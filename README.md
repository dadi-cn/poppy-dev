
[![Join the chat at https://gitter.im/poppy-framework/Lobby](https://badges.gitter.im/poppy-framework/Lobby.svg)](https://gitter.im/poppy-framework/Lobby?utm_source=badge&utm_medium=badge&utm_campaign=pr-badge&utm_content=badge)

## Agamotto

Agamotto is a simple PHP API extension for DateTime, and is the cousin of Carbon. 
The goal of this library is to create a solution for displaying localized dates 
and introducing more robust features.

This library acts as an umbrella for the Date library:
[Date](https://github.com/jenssegers/date)
[Carbon](https://github.com/briannesbitt/carbon)

## Console

### Make Module

Create a new Poppy module and bootstrap it.

```
$ php artisan poppy:make {slug} [-Q|--quick]
```

The file tree lists:

```
├── configurations        # configuration
├── docs                  # document
├── resources             
│   ├── lang              # lang file
│   │   └── zh            # language folder
│   ├── mixes             # vue modules
│   └── views             # blade template
└── src
    ├── classes
    ├── database
    │   ├── factories
    │   ├── migrations
    │   └── seeds
    ├── events
    ├── http
    │   └── routes
    ├── listeners
    ├── models
    └── request
        ├── api
        ├── backend
        └── web
```
### List Modules

List all application modules

```
$ php artisan poppy:list

+------+--------+--------+-------------------------------------------------------+---------+
| #    | Name   | Slug   | Description                                           | Status  |
+------+--------+--------+-------------------------------------------------------+---------+
| 9001 | System | system | This is the description for the poppy Backend module. | Enabled |
| 9001 | Slt    | slt    | Sour Lemon Team                                       | Enabled |
+------+--------+--------+-------------------------------------------------------+---------+
```

### Enable/Disable Module

```
$ php artisan poppy:enable {slug}
$ php artisan poppy:disable {slug}
```

### Optimize Module

```
$ php artisan poppy:optimize
```

### Poppy Document

Use php-cs-fixer fix code style, the .php_cs style in root package folder
```
$ php artisan poppy:doc {phpcs/cs} 
```

Use phplint check code errors.
```
$ php artisan poppy:doc {phplint/lint} 
```

Use sami generate php document.
```
$ php artisan poppy:doc {sami/php} 
```

Use docsify generate project document.
```
$ php artisan poppy:doc {app} 
```

Display current tail log command.
```
$ php artisan poppy:doc {log} 
```

### Poppy Database Manager

```
poppy:migrate           Run the database migrations for a specific or all modules
poppy:migrate:refresh   Reset and re-run all migrations for a specific or all modules
poppy:migrate:reset     Rollback all database migrations for a specific or all modules
poppy:migrate:rollback  Rollback the last database migrations for a specific or all modules
poppy:migration {slug}  Create a new module migration file
```

### Check Event Named 

Check Event/Listeners is in rule.
```
php artisan poppy:check
```

## Poppy Generators

Generator Tools

```
php artisan poppy:command {slug} {name}
php artisan poppy:controller {slug} {api/web} {name}
php artisan poppy:middleware {slug} {name}
php artisan poppy:model {slug} {name}
php artisan poppy:policy {slug} {name}
php artisan poppy:provider {slug} {name}
php artisan poppy:request {slug} {name}
php artisan poppy:seed {slug} {name}
php artisan poppy:seeder {slug} {name}
php artisan poppy:test {slug} {name}
```


## Events

```
// Locale Changed
Events\LocaleChanged($locale)

// Module Maked
Events\PoppyMake($slug)
```

## GraphQl Support

// todo

## Helpers

```
ArrayHelper
CacheHelper
ContentHelper
EnvHelper
FileHelper
HtmlHelper
ImageHelper
RawCookieHelper
RouterHelper
SearchHelper
StrHelper
TimeHelper
TreeHelper
UtilHelper
WebHelper
```

## Parse

Support Xml,Ini,Yaml

## Blade 

```
@poppy
// You Can check if module is exist and enabled.
@endpoppy
```
@