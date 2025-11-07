<?php
declare(strict_types=1);

final class Env {
    private static bool $loaded = false;
    private static array $vars = [];

    public static function load(?string $path = null): void {
        if (self::$loaded) return;
        $dir  = __DIR__;
        $file = $path ?: $dir . '/.env';
        if (!is_readable($file)) { self::$loaded = true; return; }

        foreach (file($file, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES) as $line) {
            if ($line === '' || $line[0] === '#') continue;
            if (!str_contains($line, '=')) continue;
            [$k, $v] = array_map('trim', explode('=', $line, 2));
            if ((str_starts_with($v,'"') && str_ends_with($v,'"')) ||
                (str_starts_with($v,"'") && str_ends_with($v,"'"))) $v = substr($v,1,-1);
            self::$vars[$k] = $v; putenv("$k=$v"); $_ENV[$k] = $v;
        }
        self::$loaded = true;
    }

    public static function get(string $key, $default=null) {
        self::load();
        return self::$vars[$key] ?? (getenv($key)!==false ? getenv($key) : $default);
    }
    public static function int(string $k,int $d=0):int {
        $v=self::get($k,$d); return is_numeric($v)?(int)$v:$d;
    }
    public static function bool(string $k,bool $d=false):bool {
        $v=strtolower((string)self::get($k,$d?'1':'0'));
        return in_array($v,['1','true','yes','on'],true);
    }
}
