Developers Guide
================

This document contains information about how Dex works and is useful for developers of modules, themes or templates.

Creating modules
----------------

Create a folder inside modules/ with your module's name and follow the folder layout below:

    root directory
    └── modules
        └── [module_name]
            ├── admin
            ├── api
            ├── resources
            |   ├── fonts
            |   ├── images
            |   ├── scripts
            |   └── styles
            |
            ├── templates
            |   └── admin
            |
            ├── config.ini
            └── hooks.php

Enter module details into config.ini, which has the following options:

    title = "[title]"
    author = "[author]"
    description = "[description]"

    name = "[module_name]"                ; name used in the back-end
    regex = "admin/module/[module_name]/" ; regex for valid URLs routing to this module
    file = "index.php"                    ; admin module index file
    url = "admin/module/[module_name]/"   ; admin module index URL
    icon = "[icon]"                       ; FontAwesome icon name
    admin_only = 0                        ; make 1 to hide it for users

The hooks.php file defines at what point the module hooks into the site and what it does. See more below at [Hooks](#hooks). Your resources go into the appropriate resource directories. All referenced templates in hooks and admin pages go into templates/ and templates/admin/ respectively.

### Admin

TODO

### API

TODO

Creating themes
----------------

Create a folder inside themes/ with your theme's name and follow the folder layout below:

    root directory
    └── themes
        └── [theme_name]
            ├── resources
            |   ├── fonts
            |   ├── images
            |   ├── scripts
            |   ├── styles
            |   └── preview.png
            |
            ├── config.ini
            └── hooks.php

Enter theme details into config.ini, which has the following options:

    title = "[title]"
    author = "[author]"
    description = "[description]"

The hooks.php file defines at what point the module hooks into the site and what it does. See more below at [Hooks](#hooks). Your resources go into the appropriate resource directories. Make sure to make a preview.png file with 256x256 pixels in resources/.

Creating templates
----------------

Create a folder inside templates/ with your template's name and follow the folder layout below:

    root directory
    └── templates
        └── [template_name]
            ├── config.ini
            ├── form.php
            └── template.php

Enter template details into config.ini, which has the following options:

    title = "[title]"
    author = "[author]"
    description = "[description]"

The form.php gets included to add form elements, it has a predefined variable $form. Use it to add more form elements (using the Form class in includes/form.class.php).

Template.php is included when the template is displayed. It has a predefined array variable $_ with keys matching the form elements in form.php.

Links
-----

All links are rewritten and checked against the database. Links do not correspond to the folder structure! All resources start with 'res/', all API calls with 'api/' and all admin pages with 'admin/'. 'res/core/scripts/' for example corresponds to the content of the '/core/resources/scripts/' folder.

When linking to an image, you can append a query string with 'w' and/or 'h' parameters defining respectively the pixel width or height. One can also use 's' for scaling.

Hooks
-----

Hooks can hook a function onto an event, whenever that event fires the function is run. Current events are:

- site-header
- header
- navigation
- main
- footer
- site-footer
- error

Attached functions are run in the sequence they were attached unless you specify its order. Negative number means before the core hook, zero or positive means after (default).