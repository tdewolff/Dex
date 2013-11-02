<?php

$db->exec("
DROP TABLE IF EXISTS module_pages;
CREATE TABLE module_pages (
    module_pages_id INTEGER PRIMARY KEY,
    link_module_id INTEGER,
    content TEXT,
    parsed_content TEXT,
    FOREIGN KEY(link_module_id) REFERENCES link_module(link_module_id)
);");

?>
