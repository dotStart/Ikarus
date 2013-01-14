<?php
/**
 * This file is part of the Ikarus Framework.
 *
 * The Ikarus Framework is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Lesser General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * The Ikarus Framework is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU Lesser General Public License for more details.
 *
 * You should have received a copy of the GNU Lesser General Public License
 * along with the Ikarus Framework. If not, see <http://www.gnu.org/licenses/>.
 */
namespace ikarus;

/**
 * IKARUS DATABASE CONFIGURATION
 * Note: This is an example layout for database configurations
 */
$adapterName = 'GenericPDO';
$hostname = 'localhost';
$port = 3306;
$user = 'root';
$password = '';
$databaseName = 'ikarus';
$databaseParameters = 'connectionType=mysql';
$charset = 'UTF8';
define('IKARUS_N', 1);
?>