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

namespace KevinGH\Box\Console\Command;

use function basename;
use function KevinGH\Box\FileSystem\copy;
use function KevinGH\Box\unique_id;
use function pathinfo;
use const PATHINFO_EXTENSION;
use function sys_get_temp_dir;

/**
 * @private
 */
trait CreateTemporaryPharFile
{
    private function createTemporaryPhar(string $file): string
    {
        if ('' === pathinfo($file, PATHINFO_EXTENSION)) {
            copy($file, $tmpFile = sys_get_temp_dir().'/'.unique_id('').basename($file).'.phar');

            return $tmpFile;
        }

        return $file;
    }
}
