Dexterous
=========

[Developers Guide](DEVELOPERS-GUIDE.md)

Small but feature-rich CMS for small and medium sized websites and is meant for webdevelopers in need of a framework to deliver websites to their clients. The software focusses on speed overall and usability for the client. It tries to follow modern web methods (mobile support, typography, HTML5) and have a high-rating on PageSpeed.

Pro's
- Fast (due to no redirects, minifying, browser caching, server caching)
- Modular (adding themes and modules is easy and site is portable due to all data being stored in one folder (incl. database))
- Useful (intuitive admin panel for clients)

Cons:
- Not suited for blogs, forums or large sites (underdeveloped and software is build for speed not scalability)

The CMS tries to rely on modern but common used software. This includes the use of Sqlite3, HTML5 and CSS3. It is not hold back by legacy but doesn't use or require experimental software either.

Installation
------------

Move the files into a directory of your webserver. Access that directory with your browser, the setup page will show. It will create database.sqlite3 which will contain all your site data. After setup you are immediately logged in on the admin panel. With the default Pages module you can create a page and with Menu you can create the navigation on your site.

If you ever want to do the installation again, delete the database.sqlite3 file using, for example, FTP and the next time your load a page you will go through the setup page again.

Make sure that the following Apache modules are enabled:
- mod_deflate
- mod_expires
- mod_filter
- mod_headers
- mod_rewrite (essential)

And these PHP extensions:
- php_sqlite3 (essential)

Installing modules and themes
-----------------------------

Move the module or theme folder into modules/ or themes/ respectively. The next time you load a page on the admin panel the new module will be installed. When you delete the module or theme folder, it is uninstalled. Modules can also be disabled.

Setting up a site
-----------------

After installation of Dexterous and the modules and themes you want, you can add content using the admin panel. Some modules (like Pages) can create content. The Pages module creates pages with static content. In the Menu panel you can add the links to these pages to your navigation so that users can navigate using the menu around your site.