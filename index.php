#!/usr/bin/env php
<?php

declare(strict_types=1);

/*
 * This file is part of the box project.
 *
 * (c) Kevin Herrera <kevin@herrera.io>
 *     Théo Fidry <theo.fidry@gmail.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace KevinGH\Box;

use ErrorException;
use KevinGH\Box\Console\Application;
use RuntimeException;

$findAutoloader = function () {
    if (file_exists($autoload = __DIR__.'/../../../autoload.php')) {
        // Is installed via composer
        return $autoload;
    }

    if (file_exists($autoload = __DIR__.'/../vendor/autoload.php')) {
        // Is installed locally
        return $autoload;
    }

    throw new RuntimeException('Unable to find the Composer or PHP-Scoper autoloader.');
};

require $findAutoloader();

register_compactor_aliases();

// Convert errors to exceptions
set_error_handler(
    function ($code, $message, $file, $line): void {
        if (error_reporting() & $code) {
            throw new ErrorException($message, 0, $code, $file, $line);
        }
    }
);

$app = new Application();
$app->run();
