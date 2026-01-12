<?php

class SQLParser
{
    public function parse(string $sql): array
    {
        $sql = trim($sql);

        // CREATE TABLE
        if (preg_match('/^CREATE TABLE (\w+) \((.+)\)$/i', $sql, $m)) {
            return [
                'type' => 'create',
                'table' => $m[1],
                'schema' => $this->parseSchema($m[2])
            ];
        }

        // INSERT
        if (preg_match('/^INSERT INTO (\w+) VALUES \((.+)\)$/i', $sql, $m)) {
            return [
                'type' => 'insert',
                'table' => $m[1],
                'values' => array_map(
                    fn($v) => trim($v, "'\" "),
                    explode(',', $m[2])
                )
            ];
        }

        // SELECT with optional WHERE
        if (preg_match('/^SELECT \* FROM (\w+)( WHERE (\w+) = (.+))?$/i', $sql, $m)) {
            return [
                'type' => 'select',
                'table' => $m[1],
                'where' => isset($m[3])
                    ? ['column' => $m[3], 'value' => trim($m[4], "'\" ")]
                    : null
            ];
        }

        // JOIN
        if (preg_match(
            '/^SELECT \* FROM (\w+) JOIN (\w+) ON (\w+)\.(\w+) = (\w+)\.(\w+)$/i',
            $sql,
            $m
        )) {
            return [
                'type' => 'join',
                'left' => $m[1],
                'right' => $m[2],
                'left_col' => $m[4],
                'right_col' => $m[6]
            ];
        }

        throw new Exception("Unsupported SQL");
    }

    private function parseSchema(string $schema): array
    {
        $columns = [];

        foreach (explode(',', $schema) as $col) {
            $parts = preg_split('/\s+/', trim($col));

            $name = $parts[0];
            $type = strtoupper($parts[1]);

            $columns[$name] = [
                'type' => $type,
                'primary' => in_array('PRIMARY', $parts),
                'unique' => in_array('UNIQUE', $parts)
            ];
        }

        return $columns;
    }
}
