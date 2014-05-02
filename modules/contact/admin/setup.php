<?php

$query = "BEGIN;
DROP TABLE IF EXISTS module_contact;
CREATE TABLE module_contact (
	module_contact_id INTEGER PRIMARY KEY,
	key TEXT,
	value TEXT
);";

$keys = array('fn', 'org', 'url', 'email', 'tel', 'adr_street-address', 'adr_locality', 'adr_region', 'adr_postal-code', 'adr_country-name');
foreach ($keys as $key)
	$query .= "
	INSERT INTO module_contact (key, value) VALUES (
		'" . Db::escape($key) . "',
		''
	);";
Db::exec($query . "COMMIT;");

if (!Db::singleQuery("SELECT * FROM link_module WHERE link_id = '0' AND module_name = 'contact' LIMIT 1"))
	Db::exec("
	INSERT INTO link_module (link_id, module_name) VALUES (
		0,
		'contact'
	);");
