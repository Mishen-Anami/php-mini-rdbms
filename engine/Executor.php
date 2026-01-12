<?php

require_once __DIR__ . '/Storage.php';

class Executor
{
    private Storage $storage;

    public function __construct()
    {
        $this->storage = new Storage();
    }

    public function execute(array $query)
    {
        switch ($query['type']) {

            case 'create':
                $this->storage->createTable($query['table'], $query['schema']);
                return "Table created";

            case 'insert':
                return $this->insert($query);

            case 'select':
                return $this->select($query);

            case 'join':
                return $this->join($query);

            default:
                throw new Exception("Unknown query type");
        }
    }

    private function insert(array $query)
    {
        $table = $this->storage->loadTable($query['table']);

        $row = array_combine(
            array_keys($table['schema']),
            $query['values']
        );

        $this->validateConstraints($table, $row);

        $table['rows'][] = $row;
        $table['indexes'] = []; // invalidate indexes

        $this->storage->saveTable($query['table'], $table);
        return "Row inserted";
    }

    private function validateConstraints(array $table, array $newRow): void
    {
        foreach ($table['schema'] as $col => $meta) {
            foreach ($table['rows'] as $row) {
                if (
                    ($meta['primary'] || $meta['unique']) &&
                    $row[$col] == $newRow[$col]
                ) {
                    throw new Exception("Constraint violation on column $col");
                }
            }
        }
    }

    private function select(array $query)
    {
        $table = $this->storage->loadTable($query['table']);
        $rows = $table['rows'];

        if ($query['where']) {
            $col = $query['where']['column'];
            $val = $query['where']['value'];

            if (!isset($table['indexes'][$col])) {
                $this->buildIndex($table, $col);
            }

            $rows = [];
            foreach ($table['indexes'][$col][$val] ?? [] as $i) {
                $rows[] = $table['rows'][$i];
            }

            $this->storage->saveTable($query['table'], $table);
        }

        return array_values($rows);
    }

    private function buildIndex(array &$table, string $column): void
    {
        $index = [];
        foreach ($table['rows'] as $i => $row) {
            $index[$row[$column]][] = $i;
        }
        $table['indexes'][$column] = $index;
    }

    private function join(array $query)
    {
        $left = $this->storage->loadTable($query['left']);
        $right = $this->storage->loadTable($query['right']);

        $results = [];

        foreach ($left['rows'] as $l) {
            foreach ($right['rows'] as $r) {
                if ($l[$query['left_col']] == $r[$query['right_col']]) {
                    $results[] = array_merge($l, $r);
                }
            }
        }

        return $results;
    }
}
