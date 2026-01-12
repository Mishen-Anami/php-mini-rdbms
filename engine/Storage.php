<?php

class Storage
{
    private string $dataDir;

    public function __construct()
    {
        $this->dataDir = __DIR__ . '/../data/';
        if (!is_dir($this->dataDir)) {
            mkdir($this->dataDir, 0777, true);
        }
    }

    public function createTable(string $name, array $schema): void
    {
        $file = $this->dataDir . "$name.json";
        if (file_exists($file)) {
            throw new Exception("Table already exists");
        }

        $table = [
            'schema' => $schema,
            'rows' => [],
            'indexes' => []
        ];

        file_put_contents($file, json_encode($table, JSON_PRETTY_PRINT));
    }

    public function loadTable(string $name): array
    {
        $file = $this->dataDir . "$name.json";
        if (!file_exists($file)) {
            throw new Exception("Table not found");
        }
        return json_decode(file_get_contents($file), true);
    }

    public function saveTable(string $name, array $table): void
    {
        $file = $this->dataDir . "$name.json";
        file_put_contents($file, json_encode($table, JSON_PRETTY_PRINT));
    }
}
