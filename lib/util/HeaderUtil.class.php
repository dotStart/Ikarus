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
use ikarus\system\Ikarus;

/**
 * Provides methods for sending and modifying headers
 * @author		Johannes Donath
 * @copyright		2011 Evil-Co.de
 * @package		de.ikarus-framework.core
 * @subpackage		system
 * @category		Ikarus Framework
 * @license		GNU Lesser Public License <http://www.gnu.org/licenses/lgpl.txt>
 * @version		2.0.0-0001
 */
class HeaderUtil {
	
	/**
	 * Returns true if cookies are supported
	 * Note: This will return the correct value only after the second execution
	 * @return			boolean
	 */
	public static function cookiesSupported() {
		// check for CLI
		if (php_sapi_name() == 'cli') return false;
		
		// set cookie
		static::setCookie('test', 1);
		
		// check for existing cookies
		if (static::getCookie('test') === null) return false;
		return true;
	}
	
	/**
	 * Returns cookie content
	 * @param			string			$cookieName
	 * @return			string
	 */
	public static function getCookie($cookieName) {
		if (isset($_COOKIE[Ikarus::getConfiguration()->get('global.http.cookiePrefix').$cookieName])) return $_COOKIE[Ikarus::getConfiguration()->get('global.http.cookiePrefix').$cookieName];
		return null;
	}
	
	/**
	 * Redirects the user agent.
	 * @param			string			$location
	 * @param			boolean			$prependDir
	 * @param			boolean			$sendStatusCode
	 */
	public static function redirect($location, $prependDir = true, $sendStatusCode = false) {
		if ($prependDir and Ikarus::componentAbbreviationExists('SessionManager')) $location = FileUtil::addTrailingSlash(FileUtil::unifyDirSeperator(dirname(Ikarus::getSessionManager()->getSession('ikarus')->requestURI))) . $location;
		if ($sendStatusCode) @header('HTTP/1.0 301 Moved Permanently');
		header('Location: '.$location);
	}

	/**
	 * Saves a cookie at client
	 * @param			string			$name
	 * @param			string			$value
	 * @param			integer			$expire
	 * @return			void
	 */
	public static function setCookie($name, $value = '', $expire = 0) {
		@header('Set-Cookie: '.rawurlencode(Ikarus::getConfiguration()->get('global.http.cookiePrefix').$name).'='.rawurlencode($value).($expire ? '; expires='.gmdate('D, d-M-Y H:i:s', $expire).' GMT' : '').(Ikarus::getConfiguration()->get('global.http.cookiePath') ? '; path='.Ikarus::getConfiguration()->get('global.http.cookiePath') : '').(Ikarus::getConfiguration()->get('global.http.cookieDomain') ? '; domain='.Ikarus::getConfiguration()->get('global.http.cookieDomain') : '').((isset($_SERVER['SERVER_PORT']) && $_SERVER['SERVER_PORT'] == '443') ? '; secure' : '').'; HttpOnly', false);
	}

	/**
	 * Sends no cache headers
	 * @return			void
	 */
	public static function sendNoCacheHeaders() {
		@header('Expires: Mon, 26 Jul 1997 05:00:00 GMT');
		@header('Last-Modified: '.gmdate('D, d M Y H:i:s').' GMT');
		@header('Cache-Control: no-cache, must-revalidate');
		@header('Pragma: no-cache');
	}
}
?>