Creating Packages for Dexterous
===============================

Packages may be installed from the admin panel of any live dexterous
web-site. This document describes dexterous packages, and how to create
and publish them.

Meta-Data
---------

Your package must contain the following meta-data:

    {
        name: "unique_package_name",
        version: "1.0.0-beta",
        type: "theme",
        description: "A short description of your package.",
        picture: "A screenshot or picture for your package, encoded in base64.",
        compatible: ["0.x", "1.0.x, 1.1.x"],
        email: "developer_contact@example.com"
    }

* name: The unique name for your package.
* version: The unique version of your package. The version must be compliant with http://semver.org/ Semantic Versioning 2.0.0. Placing a wildcard 'x' in your package version is not allowed.
* type: Either "theme", "template", or "module" as of Dexterous 0.x.
* description: A brief description (50-250 characters).
* picture: A thumbnail picture for your package, with a width of 150px, height of 150px.
* compatible: An array of dexterous versions your package is compatible with. You may use the wildcard 'x' to assume your package is compatible with any subversion. For example, 0.x is compatible with 0.1 and 0.2.3-alpha. You may not use the wildcard 'x' in the top-most version. For example, using ["x"] is not allowed.
* email: An email we can use to contact you if we accept your package, or if for some reason we had to deny or remove your package from the repository.

Your package may be removed or denied if it is not compatible with a version
of dexterous it is said to be compatible with. You will receive an email
whenever your package is removed or denied.



Your package must have a unique name, and the version of your package
must be unique. You cannot publish a package with the same version twice.
Finally, you must have permission to publish a package- and you must have
an account at dexcms.org in order to publish a package. You may give
other users the ability to publish versions of your package. You may not
give other users permission to publish version of somebody else's package.

You may provide optional meta-data if you wish:

    {
        url: "http://example.com/my_package_wiki",
        email_public: true
    }

* url: The url must work, or your package will be denied or removed.
* public_email: By default, your provided email address is private. We only use your email to tell you if we accept, remove, or deny your package.By using email_public, you may allow the public to contact you via your email address by setting the optional field, email_public, to true.

