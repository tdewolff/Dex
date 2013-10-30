<?php

$db->exec("
DROP TABLE IF EXISTS module_menu;
CREATE TABLE module_menu (
    module_menu_id INTEGER PRIMARY KEY,
    link_id INTEGER,
    position INTEGER,
    level INTEGER,
    name TEXT,
    FOREIGN KEY(link_id) REFERENCES link(link_id)
);");

?>
