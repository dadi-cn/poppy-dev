let mix = require('laravel-mix');

/*
 |--------------------------------------------------------------------------
 | Web Url : https://laravel-mix.com
 |--------------------------------------------------------------------------
 */
mix
    .browserSync({
        // 这里替换地址
        proxy : 'http://poppy.duoli.com/',
        files : [
            "public/assets/**/*.js",
            "public/assets/**/*.css",
            "modules/**/src/request/**/*.php",
            "modules/**/resources/views/**/*.blade.php",
            "modules/**/resources/js/**/*.js"
        ]
    })
    .options({
        processCssUrls : false
    })
    .disableNotifications()
    .version()
    /* 开发使用[便于文件加载]
     * ---------------------------------------- */
    // develop
    .less(
        'poppy/mgr-page/resources/less/mgr-page.less',
        'public/assets/libs/boot/style.css'
    )
    .scripts([
            'poppy/mgr-page/resources/libs/poppy/util.js',
            'poppy/mgr-page/resources/libs/poppy/cp.js',
            'poppy/mgr-page/resources/libs/poppy/mgr-page/cp.js'
        ],
        'public/assets/libs/boot/poppy.mgr.min.js'
    )
    .scripts([
            'poppy/mgr-page/resources/libs/jquery/2.2.4/jquery.min.js',
            'poppy/mgr-page/resources/libs/jquery/form/jquery.form.js',
            'poppy/mgr-page/resources/libs/jquery/pjax/jquery.pjax.js',
            'poppy/mgr-page/resources/libs/jquery/poshytip/jquery.poshytip.js',
            'poppy/mgr-page/resources/libs/jquery/validation/jquery.validation.js',
            'poppy/mgr-page/resources/libs/jquery/drag-arrange/drag-arrange.js',
            'poppy/mgr-page/resources/libs/jquery/tokenize2/jquery.tokenize2.js',
            'poppy/mgr-page/resources/libs/clipboard/clipboard.min.js'
        ],
        'public/assets/libs/boot/vendor.min.js'
    )
    .copyDirectory('poppy/mgr-page/resources/font/', 'public/assets/font/')
    .copyDirectory('poppy/mgr-page/resources/images/', 'public/assets/images/')
    .copyDirectory('poppy/mgr-page/resources/libs/jquery/', 'public/assets/libs/jquery/')
    .copyDirectory('poppy/mgr-page/resources/libs/easy-web/', 'public/assets/libs/easy-web')
    .copyDirectory('poppy/mgr-page/resources/libs/layui/', 'public/assets/libs/layui')
    .copyDirectory('poppy/mgr-page/resources/libs/vue/', 'public/assets/libs/vue')
    .copyDirectory('poppy/mgr-page/resources/libs/underscore/', 'public/assets/libs/underscore');