Required server configuration
-----------------------------
lighttpd, apache or nginx (I haven't tested other servers)
PHP 5.3+
MySQL (Other SQL-Servers may work)

Required PHP configuration
--------------------------
safe_mode off
If you plan to use the administration to install packages you should really set a max upload sizer bigger than 2M

Required PHP modules
--------------------
SPL
Reflection
hash() SHA-256 support

Optional PHP modules
--------------------
mcrypt
PDO
mysql
mysqli