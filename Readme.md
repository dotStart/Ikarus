# Ikarus Framework 2.0
The Ikarus Framework 2.0 is a PHP based OOP-Framework which uses advanced PHP 5.3 features such as namespaces or SPL. It defines a huge number of guidelines for application developers to keep the code quality on a high level.

# API 2.0
In API 2.0 we've several changes to version 1.0. Note: Most of extensions and applications for the 1.0 API will not work! Packages that are already packaged to *.tar files can't be installed over the normal way. But in developer mode there's a way to load them as "developer package" if you extract the contents to a special folder in system (Please read the developer manual on the developer package form).

# IPF 1.0
With version 2.0 we've introduced a new package format: The IPF (Ikarus Package File). This file format contains all needed information about the package in one file without using extra files (such as package.xml in IF 1.0). All contents of the IPF format are compressed with gzip and the basic package information is stored in JSON.

# For developers
Please note that we're currently under heavy development! Many things in our API **WILL CHANGE AND BREAK EVERYTHING YOU'VE ALREADY DEVELOPED FOR THE CURRENT API!**