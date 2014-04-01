<?php

function contact_render() {
    Module::setModuleName('contact');

    $contact = array();
    $table = Db::query("SELECT * FROM module_contact;");
    while ($row = $table->fetch())
        if (!empty($row['value']))
        {
            if (strpos($row['key'], 'adr_') === 0)
                $contact['adr'][substr($row['key'], 4)] = $row['value'];
            else
                $contact[$row['key']] = $row['value'];
        }

    Module::set('contact', $contact);
    Module::render('index.tpl');
}

Hooks::attach('header', 1, function () {
    contact_render();
});

Hooks::attach('footer', -1, function () {
    contact_render();
});

?>