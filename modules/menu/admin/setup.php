<?php

$db->exec("
DROP TABLE IF EXISTS module_menu;
CREATE TABLE module_menu (
    menu_id INTEGER PRIMARY KEY,
    parent_menu_id INTEGER,
    position INTEGER,
    link_id INTEGER,
    name TEXT,
    FOREIGN KEY(parent_menu_id) REFERENCES menu(menu_id)
    FOREIGN KEY(link_id) REFERENCES link(link_id)
);");

?>
