<?php

declare(strict_types=1);

// autoload_static.php @generated by Composer

namespace Composer\Autoload;

use Closure;

class ComposerStaticInitb4a255cf9b7ad70a25412f637bc93b02
{
    public static $files = [
        'a4ecaeafb8cfb009ad0e052c90355e98' => __DIR__ . '/..' . '/beberlei/assert/lib/Assert/functions.php',
    ];

    public static $prefixLengthsPsr4 = [
        'A' =>
        [
            'Assert\\' => 7,
        ],
    ];

    public static $prefixDirsPsr4 = [
        'Assert\\' =>
        [
            0 => __DIR__ . '/..' . '/beberlei/assert/lib/Assert',
        ],
    ];

    public static function getInitializer(ClassLoader $loader)
    {
        return Closure::bind(function () use ($loader): void {
            $loader->prefixLengthsPsr4 = ComposerStaticInitb4a255cf9b7ad70a25412f637bc93b02::$prefixLengthsPsr4;
            $loader->prefixDirsPsr4 = ComposerStaticInitb4a255cf9b7ad70a25412f637bc93b02::$prefixDirsPsr4;
        }, null, ClassLoader::class);
    }
}
