<?php

$db->exec("
DROP TABLE IF EXISTS module_menu;
CREATE TABLE module_menu (
    module_menu_id INTEGER PRIMARY KEY,
    link_id INTEGER,
    position INTEGER,
    level INTEGER,
    name TEXT,
    enabled INTEGER,
    FOREIGN KEY(link_id) REFERENCES link(link_id)
);

INSERT INTO link_module (link_id, module_name) VALUES (
    0,
    'menu'
);");

?>
