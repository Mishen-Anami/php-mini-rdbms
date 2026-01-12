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
        return match ($query['type']) {
            'create' => $this->create($query),
            'insert' => $this->insert($query),
            'select' => $this->select($query),
            'update' => $this->update($query),
            'delete' => $this->delete($query),
            'join'   => $this->join($query),
            default  => throw new Exception("Unknown query")
        };
    }

    private function create($q)
    {
        $this->storage->createTable($q['table'], $q['schema']);
        return "Table created";
    }

    private function insert($q)
    {
        $table = $this->storage->loadTable($q['table']);
        $row = array_combine(array_keys($table['schema']), $q['values']);
        $this->validateConstraints($table, $row);
        $table['rows'][] = $row;
        $table['indexes'] = [];
        $this->storage->saveTable($q['table'], $table);
        return "Row inserted";
    }

    private function select($q)
    {
        $table = $this->storage->loadTable($q['table']);
        $rows = $table['rows'];

        if ($q['where']) {
            $rows = array_filter($rows, fn($r) =>
                $r[$q['where']['column']] == $q['where']['value']
            );
        }
        return array_values($rows);
    }

    private function update($q)
    {
        $table = $this->storage->loadTable($q['table']);
        $count = 0;

        foreach ($table['rows'] as &$row) {
            if ($row[$q['where']['column']] == $q['where']['value']) {
                $row[$q['set']['column']] = $q['set']['value'];
                $count++;
            }
        }

        $table['indexes'] = [];
        $this->storage->saveTable($q['table'], $table);
        return "$count row(s) updated";
    }

    private function delete($q)
    {
        $table = $this->storage->loadTable($q['table']);
        $before = count($table['rows']);

        $table['rows'] = array_values(array_filter(
            $table['rows'],
            fn($r) => $r[$q['where']['column']] != $q['where']['value']
        ));

        $table['indexes'] = [];
        $this->storage->saveTable($q['table'], $table);

        return ($before - count($table['rows'])) . " row(s) deleted";
    }

    private function join($q)
    {
        $left = $this->storage->loadTable($q['left']);
        $right = $this->storage->loadTable($q['right']);
        $out = [];

        foreach ($left['rows'] as $l) {
            foreach ($right['rows'] as $r) {
                if ($l[$q['left_col']] == $r[$q['right_col']]) {
                    $out[] = array_merge($l, $r);
                }
            }
        }
        return $out;
    }

    private function validateConstraints($table, $newRow)
    {
        foreach ($table['schema'] as $col => $meta) {
            foreach ($table['rows'] as $row) {
                if (($meta['primary'] || $meta['unique']) && $row[$col] == $newRow[$col]) {
                    throw new Exception("Constraint violation on $col");
                }
            }
        }
    }
}
