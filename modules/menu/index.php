<?php

function menu_setup() {
    global $db;

    menu_destroy();

    $db->exec("
    CREATE TABLE module_menu (
        id INTEGER PRIMARY KEY,
        parent_id INTEGER,
        link_id INTEGER,
        name VARCHAR(50),
        position INTEGER
    );

    INSERT INTO link_modules (link_id, module_name) VALUES (
        0,
        'menu'
    );

    INSERT INTO module_menu (parent_id, link_id, name, position) VALUES (
        0,
        1,
        'Home',
        0
    );");
}

function menu_destroy() {
    global $db;

    $db->exec("
    DELETE FROM link_modules WHERE module_name = 'menu';
    DROP TABLE IF EXISTS module_menu;");
}

?>
