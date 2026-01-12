<?php

require_once __DIR__ . '/../parser/SQLParser.php';
require_once __DIR__ . '/../engine/Executor.php';

$parser = new SQLParser();
$executor = new Executor();

echo "PHP Mini RDBMS\nType 'exit' to quit\n";

while (true) {
    echo "rdbms> ";
    $input = trim(fgets(STDIN));

    if ($input === 'exit') break;

    try {
        $query = $parser->parse($input);
        $result = $executor->execute($query);
        print_r($result);
    } catch (Exception $e) {
        echo "Error: " . $e->getMessage() . PHP_EOL;
    }
}
