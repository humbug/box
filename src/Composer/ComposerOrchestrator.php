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

namespace KevinGH\Box\Composer;

use Humbug\PhpScoper\Autoload\ScoperAutoloadGenerator;
use Humbug\PhpScoper\Whitelist;
use KevinGH\Box\Console\Logger\CompileLogger;
use function KevinGH\Box\FileSystem\dump_file;
use function KevinGH\Box\FileSystem\file_contents;
use const PHP_EOL;
use function preg_replace;
use RuntimeException;
use function str_replace;
use Symfony\Component\Console\Input\StringInput;
use Symfony\Component\Console\Output\NullOutput;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Process\Exception\ProcessFailedException;
use Symfony\Component\Process\ExecutableFinder;
use Symfony\Component\Process\Process;
use function trim;

/**
 * @private
 */
final class ComposerOrchestrator
{
    private function __construct()
    {
    }

    public static function dumpAutoload(
        Whitelist $whitelist,
        string $prefix,
        bool $excludeDevFiles,
        SymfonyStyle $io = null
    ): void {
        if (null === $io) {
            $io = new SymfonyStyle(
                new StringInput(''),
                new NullOutput()
            );
        }

        $composerExecutable = self::retrieveComposerExecutable();

        self::dumpAutoloader($composerExecutable, true === $excludeDevFiles, $io);

        if ('' !== $prefix) {
            $autoloadFile = self::retrieveAutoloadFile($composerExecutable, $io);

            $autoloadContents = self::generateAutoloadStatements(
                $whitelist,
                $prefix,
                file_contents($autoloadFile)
            );

            dump_file($autoloadFile, $autoloadContents);
        }
    }

    private static function generateAutoloadStatements(Whitelist $whitelist, string $prefix, string $autoload): string
    {
        if ([] === $whitelist->toArray()) {
            return $autoload;
        }

        $autoload = str_replace('<?php', '', $autoload);

        $autoload = preg_replace(
            '/return (ComposerAutoloaderInit.+::getLoader\(\));/',
            '\$loader = $1;',
            $autoload
        );

        $whitelistStatements = (new ScoperAutoloadGenerator($whitelist))->dump($prefix);

        $whitelistStatements = preg_replace(
            '/scoper\-autoload\.php \@generated by PhpScoper/',
            '@generated by Humbug Box',
            $whitelistStatements
        );

        $whitelistStatements = preg_replace(
            '/(\s*\\$loader \= .*)/',
            $autoload,
            $whitelistStatements
        );

        return preg_replace(
            '/\n{2,}/m',
            PHP_EOL.PHP_EOL,
            $whitelistStatements
        );
    }

    private static function retrieveComposerExecutable(): string
    {
        $executableFinder = new ExecutableFinder();
        $executableFinder->addSuffix('.phar');

        return $executableFinder->find('composer');
    }

    private static function dumpAutoloader(string $composerExecutable, bool $noDev, SymfonyStyle $io): void
    {
        $composerCommand = [$composerExecutable, 'dump-autoload', '--classmap-authoritative'];

        if (true === $noDev) {
            $composerCommand[] = '--no-dev';
        }

        if (null !== $verbosity = self::retrieveSubProcessVerbosity($io)) {
            $composerCommand[] = $verbosity;
        }

        if ($io->isDecorated()) {
            $composerCommand[] = '--ansi';
        }

        $dumpAutoloadProcess = new Process($composerCommand);

        (new CompileLogger($io))->log(
            CompileLogger::CHEVRON_PREFIX,
            $dumpAutoloadProcess->getCommandLine(),
            OutputInterface::VERBOSITY_VERBOSE
        );

        $dumpAutoloadProcess->run();

        if (false === $dumpAutoloadProcess->isSuccessful()) {
            throw new RuntimeException(
                'Could not dump the autoloader.',
                0,
                new ProcessFailedException($dumpAutoloadProcess)
            );
        }

        if ('' !== $output = $dumpAutoloadProcess->getOutput()) {
            $io->writeln($output, OutputInterface::VERBOSITY_VERBOSE);
        }

        if ('' !== $output = $dumpAutoloadProcess->getErrorOutput()) {
            $io->writeln($output, OutputInterface::VERBOSITY_VERBOSE);
        }
    }

    private static function retrieveAutoloadFile(string $composerExecutable, SymfonyStyle $io): string
    {
        $vendorDirProcess = new Process([$composerExecutable, 'config', 'vendor-dir']);

        if ($io->isDecorated()) {
            $vendorDirProcess->setTty($io);
        }

        $vendorDirProcess->run();

        if (false === $vendorDirProcess->isSuccessful()) {
            new ProcessFailedException($vendorDirProcess);
        }

        return trim($vendorDirProcess->getOutput()).'/autoload.php';
    }

    private static function retrieveSubProcessVerbosity(SymfonyStyle $io): ?string
    {
        if ($io->isDebug()) {
            return '-vvv';
        }

        if ($io->isVeryVerbose()) {
            return '-v';
        }

        return null;
    }
}
