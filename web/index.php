<?php

require_once __DIR__ . '/../parser/SQLParser.php';
require_once __DIR__ . '/../engine/Executor.php';

$parser = new SQLParser();
$executor = new Executor();

if ($_POST['sql'] ?? false) {
    try {
        $query = $parser->parse($_POST['sql']);
        $result = $executor->execute($query);
    } catch (Exception $e) {
        $result = $e->getMessage();
    }
}
?>

<form method="post">
    <textarea name="sql" rows="4" cols="60"></textarea><br>
    <button>Run SQL</button>
</form>

<pre><?php print_r($result ?? null); ?></pre>
