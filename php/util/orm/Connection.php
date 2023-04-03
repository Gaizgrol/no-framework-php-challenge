<?php

namespace DB;

class Connection
{
    private static ?\PgSql\Connection $connection = null;

    public static function get()
    {
        if (!self::$connection) {
            self::$connection = \pg_connect(
                'host=' . getenv('DB_HOST') . ' ' .
                    'port=' . getenv('DB_PORT') . ' ' .
                    'dbname=' . getenv('DB_NAME') . ' ' .
                    'user=' . getenv('DB_USER') . ' ' .
                    'password=' . getenv('DB_PASSWORD')
            );
        }

        return self::$connection;
    }
}
