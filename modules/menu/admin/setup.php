<?php

$db->exec("
CREATE TABLE IF NOT EXISTS module_menu (
    module_menu_id INTEGER PRIMARY KEY,
    link_id INTEGER,
    position INTEGER,
    level INTEGER,
    name TEXT,
    enabled INTEGER,
    FOREIGN KEY(link_id) REFERENCES link(link_id)
);");

if (!$db->querySingle("SELECT * FROM link_module WHERE link_id = '0' AND module_name = 'menu' LIMIT 1"))
    $db->exec("
    INSERT INTO link_module (link_id, module_name) VALUES (
        0,
        'menu'
    );");

?>
