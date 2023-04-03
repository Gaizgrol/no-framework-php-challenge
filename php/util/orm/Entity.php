<?php

declare(strict_types=1);

namespace DB;

use Logger;
use ReflectionClass;

abstract class Entity
{
    public static function schema()
    {
        return 'public';
    }

    public static function table()
    {
        return strtolower(static::class);
    }

    public static function primaryKey()
    {
        return 'id';
    }

    private static function prepare()
    {
        $conn = Connection::get();
        return [
            $conn,
            pg_escape_string($conn, static::schema()),
            pg_escape_string($conn, static::table())
        ];
    }

    public static function resultToEntity(object | array $result)
    {
        $entity = new static();
        $metadata = new ReflectionClass(static::class);
        $fields = [];
        foreach ($metadata->getProperties() as $attr) {
            $fields[$attr->getName()] = $attr->getType()->getName();
        }
        $result = (array) $result;

        foreach ($result as $column => $value) {
            if (!key_exists($column, $fields)) {
                continue;
            }
            $type = $fields[$column];
            $val = $value;
            settype($val, $type);
            $entity->$column = $val;
        }

        return $entity;
    }

    public static function findOne(string $field, mixed $value): ?static
    {
        [$conn, $schema, $table] = static::prepare();

        $query = "SELECT * FROM {$schema}.{$table} WHERE {$field} = $1";
        $result = pg_fetch_array(pg_query_params($conn, $query, [$value]));
        return $result ? self::resultToEntity($result) : null;
    }

    public static function findAll(int $offset, int $limit, bool $ascending = true, string|null $orderByField = null): array
    {
        [$conn, $schema, $table] = static::prepare();

        $orderByField ??= static::primaryKey();
        $ascending = $ascending ? 'ASC' : 'DESC';
        $query = "SELECT * FROM {$schema}.{$table} ORDER BY {$orderByField} {$ascending} LIMIT $1 OFFSET $2";
        $results = pg_fetch_all(pg_query_params($conn, $query, [$limit, $offset]));
        return $results ? array_map(function ($result) {
            return self::resultToEntity($result);
        }, $results) : [];
    }

    public function save()
    {
        $metadata = new ReflectionClass(static::class);
        $properties = [];
        foreach ($metadata->getProperties() as $attr) {
            $prop = $attr->getName();
            $this->$prop ??= null;
            $properties[$prop] = $this->$prop;
        }

        $pk = static::primaryKey();

        if (!key_exists($pk, $properties) || $properties[$pk] == null) {
            $this->create($properties);
        } else {
            $this->update($properties);
        }
    }

    private function create(array $properties)
    {
        [$conn, $schema, $table] = static::prepare();
        $pk = static::primaryKey();

        unset($properties[$pk]);

        $propCount = count($properties);
        $paramCounter = 1;

        $queryTable = "{$schema}.{$table}";
        $queryColumns = "";
        $queryValues = "";
        $values = [];
        foreach ($properties as $column => $value) {
            $queryColumns .= $column;
            $queryValues .= "\${$paramCounter}";
            if ($paramCounter != $propCount) {
                $queryColumns .= ", ";
                $queryValues .= ", ";
            }
            array_push($values, $value);
            $paramCounter++;
        }

        $query = "INSERT INTO {$queryTable} ({$queryColumns}) VALUES ({$queryValues}) RETURNING {$pk}";

        $metadata = new ReflectionClass(static::class);
        $type = 'null';
        foreach ($metadata->getProperties() as $attr) {
            if ($attr->getName() == $pk) {
                $type = $attr->getType()->getName();
                break;
            }
        }

        $arr = pg_fetch_array(pg_query_params($conn, $query, $values));
        settype($arr[$pk], $type);
        $this->$pk = $arr[$pk];
    }

    private function update(array $properties): void
    {
        [$conn, $schema, $table] = static::prepare();
        $pk = static::primaryKey();

        $propCount = count($properties);
        $paramCounter = 1;

        $queryTable = "{$schema}.{$table}";
        $queryColumns = "";
        $values = [];
        foreach ($properties as $column => $value) {
            $queryColumns .= "{$column} = \${$paramCounter}";
            if ($paramCounter != $propCount) {
                $queryColumns .= ", ";
            }
            array_push($values, $value);
            $paramCounter++;
        }
        $queryWhere = "";
        array_push($values, $this->$pk);

        $query = "UPDATE {$queryTable} SET {$queryColumns} WHERE {$pk} = \${$paramCounter}";

        pg_query_params($conn, $query, $values);
    }
}
