<?php

function contact_setup() {
    global $db;

    contact_destroy();

    $db->exec("
    CREATE TABLE module_contact (
        id INTEGER PRIMARY KEY,
        key VARCHAR(20),
        value VARCHAR(50)
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
}

function contact_destroy() {
    global $db;

    $db->exec("
    DROP TABLE IF EXISTS module_contact;");
}

?>
