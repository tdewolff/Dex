<?php

function pages_setup() {
    global $db;

    pages_destroy();

    $db->exec("
    CREATE TABLE module_pages (
        id INTEGER PRIMARY KEY,
        link_module_id INTEGER,
        content TEXT,
        parsed_content TEXT
    );

    INSERT INTO links (link, title) VALUES (
        '',
        'Title'
    );

    INSERT INTO link_modules (link_id, module_name) VALUES (
        last_insert_rowid(),
        'pages'
    );

    INSERT INTO module_pages (link_module_id, content, parsed_content) VALUES (
        last_insert_rowid(),
        'Lorem ipsum',
        'Lorem ipsum'
    );");
}

function pages_destroy() {
    global $db;

    $db->exec("
    DELETE FROM link_modules WHERE module_name = 'pages';
    DELETE FROM links WHERE id NOT IN (SELECT DISTINCT link_id FROM link_modules);
    DROP TABLE IF EXISTS module_pages;");
}

?>
