<?php

require_once __DIR__ . '/Storage.php';

class Executor {
    private Storage $storage;

    public function __construct() {
        $this->storage = new Storage();
    }

    public function execute(array $query) {
        switch ($query['type']) {

            case 'create':
                $this->storage->createTable($query['table'], $query['columns']);
                return "Table created";

            case 'insert':
                $table = $this->storage->loadTable($query['table']);
                $row = array_combine(
                    array_keys($table['schema']),
                    array_map(fn($v) => trim($v, "'\""), $query['values'])
                );
                $table['rows'][] = $row;
                $this->storage->saveTable($query['table'], $table);
                return "Row inserted";

            case 'select':
                $table = $this->storage->loadTable($query['table']);
                return $table['rows'];

            default:
                throw new Exception("Unknown query type");
        }
    }
}
