<?php

$query = "BEGIN;
DROP TABLE IF EXISTS module_menu;
CREATE TABLE module_menu (
    module_menu_id INTEGER PRIMARY KEY,
    link_id INTEGER,
    position INTEGER,
    level INTEGER,
    name TEXT,
    enabled INTEGER,
    FOREIGN KEY(link_id) REFERENCES link(link_id)
);";

$table = Db::query("SELECT * FROM link");
while ($row = $table->fetch())
    $query .= "
    INSERT INTO module_menu (link_id, position, level, name, enabled) VALUES (
        '" . Db::escape($row['link_id']) . "',
        '0',
        '0',
        '" . Db::escape($row['title']) . "',
        '1'
    );";
Db::exec($query . "COMMIT;");

if (!Db::singleQuery("SELECT * FROM link_module WHERE link_id = '0' AND module_name = 'menu' LIMIT 1"))
    Db::exec("
    INSERT INTO link_module (link_id, module_name) VALUES (
        '0',
        'menu'
    );");

?>
