<?php

class Storage {
    private string $dataDir = __DIR__ . '/../data/';

    public function createTable(string $name, array $schema): void {
        $file = $this->dataDir . "$name.json";

        if (file_exists($file)) {
            throw new Exception("Table already exists");
        }

        $table = [
            'schema' => $schema,
            'rows' => []
        ];

        file_put_contents($file, json_encode($table, JSON_PRETTY_PRINT));
    }

    public function loadTable(string $name): array {
        $file = $this->dataDir . "$name.json";
        if (!file_exists($file)) {
            throw new Exception("Table not found");
        }
        return json_decode(file_get_contents($file), true);
    }

    public function saveTable(string $name, array $table): void {
        $file = $this->dataDir . "$name.json";
        file_put_contents($file, json_encode($table, JSON_PRETTY_PRINT));
    }
}
