Dex
=========

[Developers Guide](DEVELOPERS-GUIDE.md)

Dex is a small and simple CMS designed for small- to medium-sized websites. It aims for high usability and intuitivity for users, while remaining robust and modular for webmasters.

### Properties
1. Fast
 - Makes use of server and browser caching
 - Concatenates JS and CSS to one file to reduce requests
 - Minifies HTML output
2. Modular
 - Adding themes, modules and templates is as easy as uploading the files
 - Using callbacks modules can hook onto certain actions to add functionality
3. Portable
 - All data is being stored in one folder
 - No need to setup a database, since it uses SQLite3
 - No references to the URL or directory, so it is truly portable
4. Usable
 - Stripped-down admin panel for editors
 - Simple editor and asset management
5. Strict
 - Themes do not contain logic or HTML
 - Page content is true WYSIWYG, edit you page in-place

### Note
Dex is _not_ suited for blogs, forums or large sites. The CMS is simply underdeveloped at this point of time and is in the first place not build for very large or dynamic sites.

The CMS relies on modern but common used software. This includes the use of Sqlite3, HTML5 and CSS3. It is not hold back by legacy but doesn't use experimental software either.

### Comparison to Wordpress
The project was initiated after heavy use of Wordpress for sites. Where Wordpress is officially a blog platform, I noticed how users dislike the large admin panel which is mostly for webmasters anyways. Dex tries to minimize the options editors have, they edit pages and manage assets. Period.

Wordpress, while modular, is not ideal to build themes for. Build a theme for Wordpress from the start and notice how many blog related elements need to be hidden. Dex is very modular too, but is more strict with themes. Themes do not contain logic or HTML, which clears the line between logic and presentation. Dex sets a basic HTML structure and modules can expand on that, themes do not.

Another downside of Wordpress is its size. The output HTML is large, CSS and JS files are imported plenty (WP Total Cache solves a lot but is complex and clumsy). Dex automatically concatenates CSS and JS files and minifies output HTML.

Installation
------------

Use git clone or download the zipfile from Git and extract. Move the files onto your webserver and access it with your browser. The setup page will show and a (SQLite) database is created containing all site-specific data. The non-server/ directory does not have to be uploaded.

Accessing the admin panel is done by appending admin/ to the base site URL. Removing *.db will reinitiate the setup process.

Make sure that the following Apache modules are enabled:
- mod_rewrite (essential)
- mod_deflate
- mod_expires
- mod_filter
- mod_headers

And these PHP extensions:
- php_sqlite3 (essential)
- php_curl (or allow_url_fopen = 1)

### Installing modules, themes and templates

Move the module, theme or template folder into modules/, themes/ or templates/ respectively. The next time you load a page in the admin panel the new module will be installed. Deleting the module, theme or template folder will uninstall.

### Setting up a site

After setup you can add content using the admin panel. Create pages and upload assets. Make sure to make pages visible in the menu module so the pages can be navigated to.
