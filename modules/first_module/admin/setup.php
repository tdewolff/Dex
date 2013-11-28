<?php
$db->exec('
    DROP TABLE IF EXISTS module_first_module;

    CREATE TABLE module_first_module (
        module_first_module_id INTEGER PRIMARY KEY,
        key TEXT,
        value TEXT
    );

    DELETE FROM link_module WHERE module_name = "first_module" AND link_id = 0;

    INSERT INTO link_module (link_id, module_name) VALUES (
        0,
        "first_module"
    );

    INSERT INTO module_first_module (key, value) VALUES (
        "message",
        "Hello, World"
    );');
