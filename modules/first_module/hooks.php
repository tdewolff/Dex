<?php
function first_module_render() {
    global $db;
    $data = [
        "message" => "Hello, World!"
    ];
    if ($getMessage = $db->query("SELECT value FROM module_first_module WHERE key = 'message' LIMIT 1")) {
        if($row = $getMessage->fetch()) {
            $message = $row['value'];
            if ($message) {
                $data['message'] = $message;
            }
        }
    }
    Module::set("first_module");
    Module::assign("first_module", $data);
    Module::render('index.tpl');
}

Hooks::attach('header', 1, function() {
    first_module_render();
});

Hooks::attach('footer', -1, function() {
    first_module_render();
});

