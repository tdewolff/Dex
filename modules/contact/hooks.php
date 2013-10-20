<?php

function contact_render() {
    global $db;
    Module::set('contact');

    $table = $db->query("SELECT * FROM module_contact;");
    while ($row = $table->fetch())
        Module::assign($row['key'], $row['value']);

    Module::render('index.tpl');
}

Hooks::attach('header', 1, function() {
    contact_render();
});

Hooks::attach('footer', -1, function() {
    contact_render();
});

?>