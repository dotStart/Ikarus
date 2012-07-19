# Ikarus Framework 2.0
The Ikarus Framework 2.0 is a PHP based OOP-Framework which uses advanced PHP 5.4 features. It defines a huge number of guidelines for application developers to keep the code quality on a high level.

## New Features in 2.0
### IPF 1.0
With version 2.0 we've introduced a new package format: The IPF (Ikarus Package File). This file format contains all needed information about the package in one file without using extra files (such as package.xml in IF 1.0). All contents of the IPF format are compressed with gzip and the basic package information is stored in JSON.

### Filesystem API
In Version 2.0 we've introduced a new filesystem API which allows users to store their files in different ways (This allows you to update CDNs automatically without any need of scripting things).

### New Application-Support
Additionally Ikarus now allows you to create applications of every type you could imagine.

### Angular JS
Since version 2.0 we're using Angular JS to create a new user experience on a high level. All pages are served via JS which allows live-updating pages.

### Generic Database-Adapters
Ikarus now supports generic database adapters (Like PDO). Additionally we created a new system to support prepared statements for every SQL-Database without using native interfaces.

### Advanced Events
The event system has been redesigned to support more advanced and complex listeners. You're now able to listen to parent events which are called every time the child event is called.

### Encrypted error reports
Version 2.0 lifts the support up to a new level. It provides error information in hidden error messages which allows all supporters to get information about problems without any security issue.

### More debug information
Debugging is now more easy. The system shows you as much information as it can get. Additionally all error codes are now generated automatically by the framework. Every message has it's own error code which will never ever overlap with 3rd party error codes in the framework.

### Extension-Support
We added some APIs which allow you to hook into system methods like our autoloader. This allows you to create more advanced applications on top of Ikarus.

### Fully customizable
Nearly all components of the framework are dynamic. You are able to replace any component you want without any trouble. As of version 2.0 we're using namespaces which allows you to use exactly the same class naming without getting trouble.

### Live-Development
As we created IPF 1.0 we thought about the development. You're now able to load packages which aren't packaged. Additionally you could execute automatic unit tests, sign your packages and finally deploy them automatically with a signle click in your Ikarus Development Menu.

### Dynamic request dispatcher
There are now ways to create new route types and shadow existing controllers (This is usefull if you have to overwrite existing routes and replace the original controller with your own version).

### One-File-Installer
Ikarus will be shipped as one-file Package (only an install.php) which is done with PHP's `__halt_compiler()` and the PHAR-Module (Additionally there's a legacy version for those who haven't PHAR installed).

### Sign and encrypt your packages
Version 2.0 allows you to sign and/or encrypt your packages. You've an organisation which deploys tons of applications and extensions and you want to give your users a feeling of security? No problem! Just sign your packages with your own Ikarus Developer Key.
Or maybe you want to encrypt your packages to make it heavier for the evil guys out there to install your package without any permission? This is not a problem, too. Just encrypt them!

### Guided Package-Creation
All of the new advanced steps (Such as unit testing, signing and encrypting) are done via our new development console which allows you to easily manage your creations!

**Note:** This is only a short list of things we love. There's an unbelievable amount of API methods and features in Ikarus and they're waiting for you to get discovered!

## For developers
First: All API methods **could** change in this stage of development. Please don't rely on our framework at this state of development. We'll notify our users if it's ready to be used.

### Contribute
There are two ways to contribute to Ikarus. You could either create pull requests to get your modifications merged or (if you want to contribute a lot of things) join our team.

**Note:** We've some code guidlines for our framework. You'll find them [here][Code Guidelines]

#### Pull requests
1. Fork our repository
2. Create your own branch (optional)
3. Make your modifications
4. Create a pull request

#### Get involved
Just send us an email to team@ikarus-framework.de with some references and a nice text (We want to know who you are before we invite you to join us).

### Get the system rollin'
**Note:** This is just a temporary way to install Ikarus. We'll release an installer soon (It will use PHAR which makes it very nifty).

1. Clone the repository
2. Copy the `contrib/databaseConfig.default.php` to `config.inc.php` and edit it as you need.
3. Import the database layout to your database instance (It relies in `contrib/installer/install.sql`
4. See "[Getting Started]"

## License
This program is free software: you can redistribute it and/or modify
it under the terms of the [GNU Lesser General Public License] as published by
the Free Software Foundation, either version 3 of the License, or
(at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the [GNU Lesser General Public License]
along with this program.  If not, see <http://www.gnu.org/licenses/>.

[Code Guidelines]: Project-Ikarus/blob/200/CodeGuidelines.md
[Getting Started]: Project-Ikarus/blob/200/GettingStarted.md
[GNU Lesser General Public License]: http://www.gnu.org/licenses/lgpl.txt