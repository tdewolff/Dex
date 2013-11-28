<?php
$db->exec("
    CREATE TABLE IF NOT EXISTS module_first_module (
        module_first_module_id INTEGER PRIMARY KEY,
        key TEXT,
        value TEXT
    );");

if (!$db->querySingle("SELECT * FROM link_module WHERE link_id = '0' AND module_name = 'first_module' LIMIT 1")) {
    $db->exec("
        INSERT INTO link_module (link_id, module_name) VALUES (
            0,
            'first_module'
        );");
}

$db->exec("
    INSERT INTO module_first_module (key, value) VALUES (
        'message',
        'Hello, World'
    );");
