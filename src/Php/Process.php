<?php

/*
 * This file is part of composer/xdebug-handler.
 *
 * (c) Composer <https://github.com/composer>
 *
 * For the full copyright and license information, please view
 * the LICENSE file that was distributed with this source code.
 */

namespace KevinGH\Box\Php;

/**
 * Provides utility functions to prepare a child process command-line.
 *
 * @author John Stevenson <john-stevenson@blueyonder.co.uk>
 */
class Process
{
    /**
     * Returns the process arguments, appending a color option if required
     *
     * A color option is needed because child process output is piped.
     *
     * @param array $args The argv array
     * @param $colorOption The long option to force color output
     *
     * @return array
     */
    public static function addColorOption(array $args, $colorOption)
    {
        if (!$colorOption
            || in_array($colorOption, $args)
            || !preg_match('/^--([a-z]+$)|(^--[a-z]+=)/', $colorOption, $matches)) {
            return $args;
        }

        if (isset($matches[2])) {
            // Handle --color(s)= options. Note args[0] is the script name
            if ($index = array_search($matches[2].'auto', $args)) {
                $args[$index] = $colorOption;
                return $args;
            } elseif (preg_grep('/^'.$matches[2].'/', $args)) {
                return $args;
            }
        } elseif (in_array('--no-'.$matches[1], $args)) {
            return $args;
        }

        $args[] = $colorOption;
        return $args;
    }


    /**
     * Escapes a string to be used as a shell argument.
     *
     * From https://github.com/johnstevenson/winbox-args
     * MIT Licensed (c) John Stevenson <john-stevenson@blueyonder.co.uk>
     *
     * @param string $arg  The argument to be escaped
     * @param bool   $meta Additionally escape cmd.exe meta characters
     * @param bool $module The argument is the module to invoke
     *
     * @return string The escaped argument
     */
    public static function escape($arg, $meta = true, $module = false)
    {
        if (!defined('PHP_WINDOWS_VERSION_BUILD')) {
            return escapeshellarg($arg);
        }

        $quote = strpbrk($arg, " \t") !== false || $arg === '';
        $arg = preg_replace('/(\\\\*)"/', '$1$1\\"', $arg, -1, $dquotes);

        if ($meta) {
            $meta = $dquotes || preg_match('/%[^%]+%/', $arg);

            if (!$meta) {
                $quote = $quote || strpbrk($arg, '^&|<>()') !== false;
            } elseif ($module && !$dquotes && $quote) {
                $meta = false;
            }
        }

        if ($quote) {
            $arg = preg_replace('/(\\\\*)$/', '$1$1', $arg);
            $arg = '"'.$arg.'"';
        }

        if ($meta) {
            $arg = preg_replace('/(["^&|<>()%])/', '^$1', $arg);
        }

        return $arg;
    }

    /**
     * Returns true if the output stream supports colors
     *
     * This is tricky on Windows, because Cygwin, Msys2 etc emulate pseudo
     * terminals via named pipes, so we can only check the environment.
     *
     * @param mixed $output A valid CLI output stream
     *
     * @return bool
     */
    public static function supportsColor($output)
    {
        if (defined('PHP_WINDOWS_VERSION_BUILD')) {
            return (function_exists('sapi_windows_vt100_support')
                && sapi_windows_vt100_support($output))
                || false !== getenv('ANSICON')
                || 'ON' === getenv('ConEmuANSI')
                || 'xterm' === getenv('TERM');
        }

        if (function_exists('stream_isatty')) {
            return stream_isatty($output);
        } elseif (function_exists('posix_isatty')) {
            return posix_isatty($output);
        }

        $stat = fstat($output);
        // Check if formatted mode is S_IFCHR
        return $stat ? 0020000 === ($stat['mode'] & 0170000) : false;
    }
}
