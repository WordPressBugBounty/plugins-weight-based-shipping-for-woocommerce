<?php

// autoload_static.php @generated by Composer

namespace WbsVendors\Composer\Autoload;

class ComposerStaticInitadc07ff5c4f6a5f3fc1111388091f881
{
    public static $files = array (
        'b411d774a68934fe83360f73e6fe640f' => __DIR__ . '/..' . '/dangoodman/composer-capsule-runtime/autoload.php',
    );

    public static $prefixLengthsPsr4 = array (
        'W' => 
        array (
            'Wbs\\' => 4,
            'WbsVendors\\Dgm\\WpAjaxApi\\' => 25,
            'WbsVendors\\Dgm\\Shengine\\Woocommerce\\Model\\Item\\' => 47,
            'WbsVendors\\Dgm\\Shengine\\Woocommerce\\Converters\\' => 47,
            'WbsVendors\\Dgm\\Shengine\\Migrations\\' => 35,
            'WbsVendors\\Dgm\\Shengine\\' => 24,
            'WbsVendors\\Dgm\\Range\\' => 21,
            'WbsVendors\\Dgm\\PluginServices\\' => 30,
            'WbsVendors\\Dgm\\NumberUnit\\' => 26,
            'WbsVendors\\Dgm\\Comparator\\' => 26,
        ),
        'D' => 
        array (
            'Dgm\\Composer\\ForceExportIgnore\\' => 31,
        ),
    );

    public static $prefixDirsPsr4 = array (
        'Wbs\\' => 
        array (
            0 => __DIR__ . '/../..' . '/src',
        ),
        'WbsVendors\\Dgm\\WpAjaxApi\\' => 
        array (
            0 => __DIR__ . '/..' . '/dangoodman/wp-ajax-api/src',
        ),
        'WbsVendors\\Dgm\\Shengine\\Woocommerce\\Model\\Item\\' => 
        array (
            0 => __DIR__ . '/..' . '/dangoodman/shengine-wc-item/src',
        ),
        'WbsVendors\\Dgm\\Shengine\\Woocommerce\\Converters\\' => 
        array (
            0 => __DIR__ . '/..' . '/dangoodman/shengine-wc-converters/src',
        ),
        'WbsVendors\\Dgm\\Shengine\\Migrations\\' => 
        array (
            0 => __DIR__ . '/..' . '/dangoodman/shengine-migrations/src',
        ),
        'WbsVendors\\Dgm\\Shengine\\' => 
        array (
            0 => __DIR__ . '/..' . '/dangoodman/shengine/src',
        ),
        'WbsVendors\\Dgm\\Range\\' => 
        array (
            0 => __DIR__ . '/..' . '/dangoodman/range/src',
        ),
        'WbsVendors\\Dgm\\PluginServices\\' => 
        array (
            0 => __DIR__ . '/..' . '/dangoodman/wp-plugin-services/src',
        ),
        'WbsVendors\\Dgm\\NumberUnit\\' => 
        array (
            0 => __DIR__ . '/..' . '/dangoodman/number-unit/src',
        ),
        'WbsVendors\\Dgm\\Comparator\\' => 
        array (
            0 => __DIR__ . '/..' . '/dangoodman/comparator/src',
        ),
        'Dgm\\Composer\\ForceExportIgnore\\' => 
        array (
            0 => __DIR__ . '/..' . '/dangoodman/composer-force-export-ignore',
        ),
    );

    public static $classMap = array (
        'WbsVendors\\Deferred\\Deferred' => __DIR__ . '/..' . '/dangoodman/deferred/Deferred.php',
        'WbsVendors\\Dgm\\Arrays\\Arrays' => __DIR__ . '/..' . '/dangoodman/arrays/Arrays.php',
        'WbsVendors\\Dgm\\ClassNameAware\\ClassNameAware' => __DIR__ . '/..' . '/dangoodman/class-name-aware/ClassNameAware.php',
        'WbsVendors\\Dgm\\SimpleProperties\\SimpleProperties' => __DIR__ . '/..' . '/dangoodman/simple-properties/SimpleProperties.php',
        'WbsVendors\\Dgm\\WcTools\\WcTools' => __DIR__ . '/..' . '/dangoodman/wc-tools/WcTools.php',
    );

    public static function getInitializer(\WbsVendors\Composer\Autoload\ClassLoader $loader)
    {
        return \Closure::bind(function () use ($loader) {
            $loader->prefixLengthsPsr4 = \WbsVendors\Composer\Autoload\ComposerStaticInitadc07ff5c4f6a5f3fc1111388091f881::$prefixLengthsPsr4;
            $loader->prefixDirsPsr4 = \WbsVendors\Composer\Autoload\ComposerStaticInitadc07ff5c4f6a5f3fc1111388091f881::$prefixDirsPsr4;
            $loader->classMap = \WbsVendors\Composer\Autoload\ComposerStaticInitadc07ff5c4f6a5f3fc1111388091f881::$classMap;

        }, null, \WbsVendors\Composer\Autoload\ClassLoader::class);
    }
}
