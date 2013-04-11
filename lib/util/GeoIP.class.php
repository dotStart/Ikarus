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
namespace ikarus\util;

/**
 * Helps you to access geo ip.
 * @author		Johannes Donath
 * @copyright		2012 Evil-Co.de
 * @package		de.ikarus-framework.core
 * @subpackage		system
 * @category		Ikarus Framework
 * @license		GNU Lesser Public License <http://www.gnu.org/licenses/lgpl.txt>
 * @version		2.0.0-0001
 */
class GeoIP {

	/**
	 * Returns the country which belongs to the given address or host.
	 * @param			string			$ipAddress			IP address or hostname
	 * @return			string
	 * @api
	 */
	public static function getCountry($ipAddress) {
		/// something failed
		if (!static::isSupported()) throw new APIException('Cannot use GeoIP: It is not installed or the country database is not available');

		// return information
		return geoip_country_name_by_name($ipAddress);
	}

	/**
	 * Checks whether geoip is natively supported.
	 * @return			boolean
	 * @api
	 */
	public static function isSupported() {
		// extension loaded?
		if (!extension_loaded('geoip')) return false;

		// country edition available?
		return geoip_db_avail(GEOIP_COUNTRY_EDITION);
	}
}
?>