<?php

function contact_render() {
    global $db;

    $table = $db->query("SELECT * FROM module_contact;");
    while ($row = $table->fetch())
        Dexterous::assign($row['key'], $row['value']);

    Dexterous::renderModule('contact', 'index.tpl');
}

Hooks::attach('header', 1, function() {
    contact_render();
});

Hooks::attach('footer', -1, function() {
    contact_render();
});

?>