<?php

namespace PDOx;

class Autoloader
{
    const NAME_SPACE = 'PDOx';
    protected static $base_dir;

    public static function register($dirname = __DIR__)
    {
        if (is_null(self::$base_dir)) {
            self::$base_dir = $dirname;
            spl_autoload_register(array(__CLASS__, "autoload"));
        }
    }

    public static function unregister()
    {
        spl_autoload_unregister(array(__CLASS__, "autoload"));
    }

    public static function autoload($name)
    {
        $retval = false;

        var_dump($name);
        exit;

        if (strpos($name, self::NAME_SPACE) === 0) {
            $parts = explode("\\", $name);
            array_shift($parts);

            $expected_path = join(DIRECTORY_SEPARATOR, array(self::$base_dir, join(DIRECTORY_SEPARATOR, $parts) . ".php"));

            if (is_file($expected_path) && is_readable($expected_path)) {
                require $expected_path;
                $retval = true;
            }
        }

        return $retval;
    }
}
