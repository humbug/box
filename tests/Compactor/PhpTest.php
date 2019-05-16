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

use Generator;
use KevinGH\Box\Annotation\AnnotationDumper;
use KevinGH\Box\Annotation\DocblockAnnotationParser;
use KevinGH\Box\Annotation\DocblockParser;
use KevinGH\Box\Compactor\Php;
use PHPUnit\Framework\TestCase;

/**
 * @covers \KevinGH\Box\Compactor\Php
 */
class PhpTest extends TestCase
{
    /**
     * @dataProvider provideFiles
     */
    public function test_it_supports_PHP_files(string $file, bool $supports): void
    {
        $compactor = new Php(
            new DocblockAnnotationParser(
                new DocblockParser(),
                new AnnotationDumper(),
                []
            )
        );

        $contents = <<<'PHP'
<?php


// PHP file with a lot of spaces

$x = '';


PHP;
        $actual = $compactor->compact($file, $contents);

        $this->assertSame($supports, $contents !== $actual);
    }

    /**
     * @dataProvider providePhpContent
     */
    public function test_it_compacts_PHP_files(DocblockAnnotationParser $annotationParser, string $content, string $expected): void
    {
        $file = 'foo.php';

        $actual = (new Php($annotationParser))->compact($file, $content);

        $this->assertSame($expected, $actual);
    }

    public function provideFiles(): Generator
    {
        yield 'no extension' => ['test', false];

        yield 'PHP file' => ['test.php', true];
    }

    public function providePhpContent(): Generator
    {
        $regularAnnotationParser = new DocblockAnnotationParser(
            new DocblockParser(),
            new AnnotationDumper(),
            []
        );

        yield 'simple PHP file with comments' => [
            $regularAnnotationParser,
            <<<'PHP'
<?php

/**
 * A comment.
 */
class AClass
{
    /**
     * A comment.
     */
    public function aMethod()
    {
        \$test = true;# a comment
    }
}
PHP
            ,
            <<<'PHP'
<?php




class AClass
{



public function aMethod()
{
\$test = true;
 }
}
PHP
        ];

        yield 'PHP file with annotations' => [
            new DocblockAnnotationParser(
                new DocblockParser(),
                new AnnotationDumper(),
                ['ignored']
            ),
            <<<'PHP'
<?php

/**
 * This is an example entity class.
 *
 * @Entity()
 * @Table(name="test")
 */
class Test
{
    /**
     * The unique identifier.
     *
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue()
     * @ORM\Id()
     */
    private \$id;

    /**
     * A foreign key.
     *
     * @ORM\ManyToMany(targetEntity="SomethingElse")
     * @ORM\JoinTable(
     *     name="aJoinTable",
     *     joinColumns={
     *         @ORM\JoinColumn(name="joined",referencedColumnName="foreign")
     *     },
     *     inverseJoinColumns={
     *         @ORM\JoinColumn(name="foreign",referencedColumnName="joined")
     *     }
     * )
     */
    private \$foreign;

    /**
     * @ignored
     */
    private \$none;
}
PHP
            ,
            <<<'PHP'
<?php

/**
@Entity()
@Table(name="test")


*/
class Test
{
/**
@ORM\Column(type="integer")
@ORM\GeneratedValue()
@ORM\Id()


*/
private \$id;

/**
@ORM\ManyToMany(targetEntity="SomethingElse")
@ORM\JoinTable(name="aJoinTable",joinColumns={@ORM\JoinColumn(name="joined",referencedColumnName="foreign")},inverseJoinColumns={@ORM\JoinColumn(name="foreign",referencedColumnName="joined")})










*/
private \$foreign;




private \$none;
}
PHP
        ];

        yield 'legacy issue 14' => [
            new DocblockAnnotationParser(
                new DocblockParser(),
                new AnnotationDumper(),
                ['author', 'inline']
            ),
            <<<'PHP'
<?php

// autoload_real.php @generated by Composer

/**
 * @author Made Up <author@web.com>
 */
class ComposerAutoloaderInitc22fe6e3e5ad79bad24655b3e52999df
{
    private static \$loader;

    /** @inline annotation */
    public static function loadClassLoader(\$class)
    {
        if ('Composer\Autoload\ClassLoader' === \$class) {
            require __DIR__ . '/ClassLoader.php';
        }
    }

    public static function getLoader()
    {
        if (null !== self::\$loader) {
            return self::\$loader;
        }

        spl_autoload_register(array('ComposerAutoloaderInitc22fe6e3e5ad79bad24655b3e52999df', 'loadClassLoader'), true, true);
        self::\$loader = \$loader = new \Composer\Autoload\ClassLoader();
        spl_autoload_unregister(array('ComposerAutoloaderInitc22fe6e3e5ad79bad24655b3e52999df', 'loadClassLoader'));

        \$vendorDir = dirname(__DIR__);
        \$baseDir = dirname(\$vendorDir);

        \$includePaths = require __DIR__ . '/include_paths.php';
        array_push(\$includePaths, get_include_path());
        set_include_path(join(PATH_SEPARATOR, \$includePaths));

        \$map = require __DIR__ . '/autoload_namespaces.php';
        foreach (\$map as \$namespace => \$path) {
            \$loader->set(\$namespace, \$path);
        }

        \$map = require __DIR__ . '/autoload_psr4.php';
        foreach (\$map as \$namespace => \$path) {
            \$loader->setPsr4(\$namespace, \$path);
        }

        \$classMap = require __DIR__ . '/autoload_classmap.php';
        if (\$classMap) {
            \$loader->addClassMap(\$classMap);
        }

        \$loader->register(true);

        return \$loader;
    }
        }

PHP
            ,
            <<<'PHP'
<?php






class ComposerAutoloaderInitc22fe6e3e5ad79bad24655b3e52999df
{
private static \$loader;


public static function loadClassLoader(\$class)
{
if ('Composer\Autoload\ClassLoader' === \$class) {
require __DIR__ . '/ClassLoader.php';
}
}

public static function getLoader()
{
if (null !== self::\$loader) {
return self::\$loader;
}

spl_autoload_register(array('ComposerAutoloaderInitc22fe6e3e5ad79bad24655b3e52999df', 'loadClassLoader'), true, true);
self::\$loader = \$loader = new \Composer\Autoload\ClassLoader();
spl_autoload_unregister(array('ComposerAutoloaderInitc22fe6e3e5ad79bad24655b3e52999df', 'loadClassLoader'));

\$vendorDir = dirname(__DIR__);
\$baseDir = dirname(\$vendorDir);

\$includePaths = require __DIR__ . '/include_paths.php';
array_push(\$includePaths, get_include_path());
set_include_path(join(PATH_SEPARATOR, \$includePaths));

\$map = require __DIR__ . '/autoload_namespaces.php';
foreach (\$map as \$namespace => \$path) {
\$loader->set(\$namespace, \$path);
}

\$map = require __DIR__ . '/autoload_psr4.php';
foreach (\$map as \$namespace => \$path) {
\$loader->setPsr4(\$namespace, \$path);
}

\$classMap = require __DIR__ . '/autoload_classmap.php';
if (\$classMap) {
\$loader->addClassMap(\$classMap);
}

\$loader->register(true);

return \$loader;
}
}

PHP
        ];

        yield 'Invalid PHP file' => [
            $regularAnnotationParser,
            '<ph',
            '<ph',
        ];

        yield 'Invalid annotation with ignored param' => [
            $regularAnnotationParser,
            <<<'PHP'
<?php

/**
 * @param (string|stdClass $x 
 */
function foo($x) {
}
PHP
        ,
            <<<'PHP'
<?php

/**
@param
*/
function foo($x) {
}
PHP
        ];

        yield 'Invalid annotation' => [
            $regularAnnotationParser,
            <<<'PHP'
<?php

/**
 * comment
 *
 * @a({@:1}) 
 */
function foo($x) {
}
PHP
        ,
            <<<'PHP'
<?php

/**
 * comment
 *
 * @a({@:1}) 
 */
function foo($x) {
}
PHP
        ];
    }
}
