<?php

$db->exec("
CREATE TABLE module_page (
    module_page_id INTEGER PRIMARY KEY,
    link_module_id INTEGER,
    content TEXT,
    parsed_content TEXT,
    FOREIGN KEY(link_module_id) REFERENCES link_module(link_module_id)
);");

?>
