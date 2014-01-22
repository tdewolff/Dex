<?php

Db::exec("
CREATE TABLE IF NOT EXISTS module_contact (
    module_contact_id INTEGER PRIMARY KEY,
    key TEXT,
    value TEXT
);");

if (!Db::querySingle("SELECT * FROM link_module WHERE link_id = '0' AND module_name = 'contact' LIMIT 1"))
    Db::exec("
    INSERT INTO link_module (link_id, module_name) VALUES (
        0,
        'contact'
    );");

$contact = array();
$table = Db::query("SELECT * FROM module_contact;");
while ($row = $table->fetch())
    $contact[$row['key']] = $row['value'];

$keys = array('fn', 'org', 'url', 'email', 'tel', 'adr_street-address', 'adr_locality', 'adr_region', 'adr_postal-code', 'adr_country-name');
foreach ($keys as $key)
    if (!isset($contact[$key]))
        Db::exec("
        INSERT INTO module_contact (key, value) VALUES (
            '" . Db::escape($key) . "',
            ''
        );");

?>
