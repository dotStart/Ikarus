Requirements
============
This framework will basically run with the default configuration of PHP but there are some modules and settings that COULD break your installation or disable features.

Required server configuration
-----------------------------
lighttpd, apache or nginx (I haven't tested other servers)
PHP 5.4+
MySQL (Other SQL-Servers may work)

Required PHP configuration
--------------------------
max upload size > 2M (If you plan to install packages)

Required PHP modules
--------------------
SPL
Reflection
mhash with SHA-512 support

Optional PHP modules
--------------------
mcrypt
PDO
mysql
mysqli
zlib