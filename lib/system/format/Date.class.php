<?php
/**
 * This file is part of the Ikarus Framework.
 * The Ikarus Framework is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Lesser General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 * The Ikarus Framework is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU Lesser General Public License for more details.
 * You should have received a copy of the GNU Lesser General Public License
 * along with the Ikarus Framework. If not, see <http://www.gnu.org/licenses/>.
 */
namespace ikarus\system\format;

use ikarus\system\Ikarus;

/**
 * Represents a date.
 * @author                    Johannes Donath
 * @copyright                 © Copyright 2012 Evil-Co.de <http://www.evil-co.com>
 * @package                   de.ikarus-framework.core
 * @subpackage                system
 * @category                  Ikarus Framework
 * @license                   GNU Lesser Public License <http://www.gnu.org/licenses/lgpl.txt>
 * @version                   2.0.0-0001
 */
class Date {

	/**
	 * Stores the generated timestamp.
	 * @var                        integer
	 */
	protected $timestamp = 0;

	/**
	 * Constructs the object.
	 * @param                        integer $timestamp
	 */
	public function __construct ($timestamp = null) {
		if ($timestamp == null) $timestamp = Ikarus::getTime ();
		$this->timestamp = $timestamp;
	}

	/**
	 * Formats a date/time string.
	 * @see                                strftime()
	 * @return                        string
	 */
	public function format ($format) {
		return strftime ($format, $this->timestamp);
	}

	/**
	 * Returns the current set day.
	 * @return                        integer
	 */
	public function getDay () {
		return idate ('d', $this->timestamp);
	}

	/**
	 * Returns the current set hour.
	 * @return                        integer
	 */
	public function getHour () {
		return idate ('H', $this->timestamp);
	}

	/**
	 * Returns the current set minute.
	 * @return                        integer
	 */
	public function getMinute () {
		return idate ('i', $this->timestamp);
	}

	/**
	 * Returns the current set month.
	 * @return                        integer
	 */
	public function getMonth () {
		return idate ('m', $this->timestamp);
	}

	/**
	 * Returns the current set count of seconds.
	 * @return                        integer
	 */
	public function getSecond () {
		return idate ('s');
	}

	/**
	 * Returns the current timestamp representation of this date.
	 * @return                        integer
	 */
	public function getTimestamp () {
		return $this->timestamp;
	}

	/**
	 * Returns the current set year.
	 * @return                        integer
	 */
	public function getYear () {
		return idate ('Y', $this->timestamp);
	}

	/**
	 * Indicates whether DST is active.
	 * @return                        boolean
	 */
	public function isDST () {
		return (idate ('I', $this->timestamp) == 1);
	}

	/**
	 * Indicates whether the current year is a leap year.
	 * @return                        boolean
	 */
	public function isLeapYear () {
		return (idate ('L', $this->timestamp) == 1);
	}

	/**
	 * Parses a date with a specified format.
	 * @param                        string $format
	 * @param                        string $date
	 * @return                        self
	 */
	public static function parse ($format, $date) {
		$parsed = date_parse_from_format ($format, $date);

		// validate
		if (!$parsed or !is_array ($parsed)) throw new IllegalArgumentException('Cannot parse date "%s" with format "%s".', $date, $format); // XXX: I'm not sure whether this works ...

		// create timestamp
		return (new static(mktime ($parsed['hour'], $parsed['minute'], $parsed['second'], $parsed['month'], $parsed['day'], $parsed['year'], (isset($parsed['dst']) and !empty($parsed['dst']) and ((bool)$parsed['dst'])))));
	}

	/**
	 * Sets a new day.
	 * @param                        integer $day
	 * @return                        void
	 */
	public function setDay ($day) {
		$this->timestamp = mktime ($this->getHour (), $this->getMinute (), $this->getSecond (), $this->getMonth (), $day, $this->getYear (), $this->isDST ());
	}

	/**
	 * Sets a new hour.
	 * @param                        integer $hour
	 * @return                        void
	 */
	public function setHour ($hour) {
		$this->timestamp = mktime ($hour, $this->getMinute (), $this->getSecond (), $this->getMonth (), $this->getDay (), $this->getYear (), $this->isDST ());
	}

	/**
	 * Sets a new minute.
	 * @param                        integer $minute
	 * @return                        void
	 */
	public function setMinute ($minute) {
		$this->timestamp = mktime ($this->getHour (), $minute, $this->getSecond (), $this->getMonth (), $this->getDay (), $this->getYear (), $this->isDST ());
	}

	/**
	 * Sets a new month.
	 * @param                        integer $month
	 * @return                        void
	 */
	public function setMonth ($month) {
		$this->timestamp = mktime ($this->getHour (), $this->getMinute (), $this->getSecond (), $month, $this->getDay (), $this->getYear (), $this->isDST ());
	}

	/**
	 * Sets a new second.
	 * @param                        integer $second
	 * @return                        void
	 */
	public function setSecond ($second) {
		$this->timestamp = mktime ($this->getHour (), $this->getMinute (), $second, $this->getMonth (), $this->getDay (), $this->getYear (), $this->isDST ());
	}

	/**
	 * Sets a new timestamp.
	 * @param                        integer $timestamp
	 * @return                        void
	 */
	public function setTimestamp ($timestamp) {
		$this->timestamp = $timestamp;
	}

	/**
	 * Sets a new year.
	 * @param                        integer $year
	 * @return                        void
	 */
	public function setYear ($year) {
		$this->timestamp = mktime ($this->getHour (), $this->getMinute (), $this->getSecond (), $this->getMonth (), $this->getDay (), $year, $this->isDST ());
	}

	/**
	 * Sets whether DST is active or not.
	 * @param                        boolean $dst
	 * @return                        void
	 */
	public function setDST ($dst = false) {
		$this->timestamp = mktime ($this->getHour (), $this->getMinute (), $this->getSecond (), $this->getMonth (), $this->getDay (), $this->getYear (), ($dst ? 1 : 0));
	}

	/**
	 * Returns a preferred date and time string (based on current set locale).
	 * @return                        string
	 */
	public function __toString () {
		return $this->format ('%c');
	}
}

?>