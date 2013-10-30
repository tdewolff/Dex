<?php

$db->exec("
DROP TABLE IF EXISTS module_contact;
CREATE TABLE module_contact (
    module_contact_id INTEGER PRIMARY KEY,
    key TEXT,
    value TEXT
);

INSERT INTO link_module (link_id, module_name) VALUES (
    0,
    'contact'
);

INSERT INTO module_contact (key, value) VALUES (
    'url',
    ''
);

INSERT INTO module_contact (key, value) VALUES (
    'organization',
    ''
);

INSERT INTO module_contact (key, value) VALUES (
    'name',
    ''
);

INSERT INTO module_contact (key, value) VALUES (
    'tel',
    ''
);

INSERT INTO module_contact (key, value) VALUES (
    'email',
    ''
);");

?>
