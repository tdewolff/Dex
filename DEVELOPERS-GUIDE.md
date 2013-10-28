Developers Guide
================

This document contains information about how Dexterous works and is useful for developers of modules, themes or the core.

Folder structure
----------------

Core (all vital code), modules and themes comply to the following folder layout:

    main directory
    ├── admin
    |   └── setup.php
    ├── resources
    |   ├── fonts
    |   ├── images
    |   ├── scripts
    |   └── styles
    ├── templates
    |   └── admin
    └── hooks.php

Modules and themes also have a config.ini in the main directory. Adhere to this folder structure, the config.ini and hooks files are obligated!
The admin folder contains all admin panel pages (where setup.php is used for installation), the hooks.php file defines how and when the code attaches to the frame. For example it defines that your module is run in the footer section. Config.ini contains details about the module or theme.

    root directory
    ├── cache
    ├── core
    ├── include
    ├── logs
    ├── media
    ├── modules
    |   └── (module directories)
    ├── themes
    |   └── (theme directories)
    ├── .htaccess
    ├── database.sqlite3
    ├── favicon.ico
    └── index.php

The cache directory will contain all merged CSS and JS files as well as resized images. They are named SHA1(merged_filenames + last_modify_time). Core and include directories contain all essential code for Dexterous. Media has all uploaded data such as images or other files.

Links
-----

All links are rewritten and checked against the database. Links do not correspond to the folder structure! All resources start with 'res/'. CSS and JS files are merged and cached into cache/, that means all images loaded in the stylesheets or scripts must be relative to the cache directory. If images need to be resized, append a query string with 'w' and/or 'h' parameters defining respectively the pixel width or height. One can also use 's' for scaling. All JS files are merged and loaded before </head>, all deferred JS files are loaded before </body>.

Each link has a set of modules bound to it which are loaded when the URL is requested. Each module defines at which point in the output it will run using hooks. Links are created using content modules such as Pages (for static content).

Hooks
-----

Hooks can hook a function onto an event, whenever that event fires the function is run. Current events are:

- header
- navigation
- main
- footer
- error
- admin_header (admin panel only)
- admin_footer (ditto)

Attached functions are run in the sequence they were attached unless you specify its order. Negative number means before the core hook, zero or positive means after (default).

Modules
-------

In config.ini you set the details for your module including the admin panel link it adds. In setup.php you write the database setup code. Create the admin page and let it start with ``Module::set('__module_name__');``. You can now use the static ``Module`` class to add stylesheets, scripts, variables for your template and to render the template. Your admin page must end with ``exit;``. Use the hooks.php file to hook your module to the pages and use ``Module::set('__module_name__');`` and the ``Module`` class like in your admin page. Add all resources to the appropriate resource folders.

### Adding links for your module ###

Each module can be attached to a link (URL + title) in a many-many relation. You can retrieve the current link_id using ``Module::getLinkId();`` within your hooks. In the admin pages you can create a new link using ``$link_id = Module::getLink(__url__, __title__);`` where title is optional, this will retrieve link_id (by creating a new link or if the link already exists it will update title if necessary). You can attach the module to the link by calling ``$link_module_id = Module::attachToLink(__link_id__);`` or if all pages need this module you can call ``Module::attachToAllLinks();``. The retrieved link_module_id is needed to detach using ``Module::detachFromLink(__link_module_id__);`` or ``Module::detachFromAllLinks();``, so it is essential that in your module table you store the link_module_id.

Themes
------

In config.ini you set the details for your theme. Your hooks.php file tells how to hook the theme to the pages, typically you hook like ``Hooks::attach('header', -1, function () { ... });`` and add your stylesheets and script after setting ``Theme::set('__theme_name__');``. Set a 256x256 preview image at resources/preview.png and add all resources to the appropriate resource folders.
