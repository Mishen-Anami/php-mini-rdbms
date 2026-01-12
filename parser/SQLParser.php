<?php

class SQLParser {

    public function parse(string $sql): array {
        $sql = trim($sql);

        if (preg_match('/^CREATE TABLE (\w+) \((.+)\)$/i', $sql, $m)) {
            return [
                'type' => 'create',
                'table' => $m[1],
                'columns' => $this->parseColumns($m[2])
            ];
        }

        if (preg_match('/^INSERT INTO (\w+) VALUES \((.+)\)$/i', $sql, $m)) {
            return [
                'type' => 'insert',
                'table' => $m[1],
                'values' => array_map('trim', explode(',', $m[2]))
            ];
        }

        if (preg_match('/^SELECT \* FROM (\w+)$/i', $sql, $m)) {
            return [
                'type' => 'select',
                'table' => $m[1]
            ];
        }

        throw new Exception("Unsupported SQL");
    }

    private function parseColumns(string $cols): array {
        $columns = [];
        foreach (explode(',', $cols) as $col) {
            [$name, $type] = array_map('trim', explode(' ', trim($col)));
            $columns[$name] = strtoupper($type);
        }
        return $columns;
    }
}
