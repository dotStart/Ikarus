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
namespace ikarus\system\language;

use ikarus\data\DatabaseObject;
use ikarus\system\Ikarus;

/**
 * @author                    Johannes Donath
 * @copyright                 2011 Evil-Co.de
 * @package                   de.ikarus-framework.core
 * @subpackage                system
 * @category                  Ikarus Framework
 * @license                   GNU Lesser Public License <http://www.gnu.org/licenses/lgpl.txt>
 * @version                   2.0.0-0001
 */
class Language extends DatabaseObject {

	/**
	 * @see ikarus\data\DatabaseObject::$identifierField
	 */
	protected static $identifierField = 'languageID';

	/**
	 * @see ikarus\data\DatabaseObject::$tableName
	 */
	protected static $tableName = 'ikarus1_language';

	/**
	 * Contains all language variables for this language
	 * @var                        array<string>
	 */
	protected $languageVariables = array ();

	/**
	 * Loads the language cache for this language
	 * @return                        void
	 * @api
	 */
	public function loadCache () {
		// performance check
		if (!$this->hasContent) return;

		// load resource
		Ikarus::getCacheManager ()->getDefaultAdapter ()->createResource ('language-' . $this->languageID, 'language-' . $this->languageID, 'ikarus\\system\\cache\\builder\\CacheBuilderLanguage');

		// get information
		$this->languageVariables = Ikarus::getCacheManager ()->getDefaultAdapter ()->get ('language-' . $this->languageID);
	}

	/**
	 * Returns the compiled value of a language variable
	 * @param                        string  $variableName
	 * @param                        boolean $disableCompilation
	 * @return                        string
	 * @api
	 */
	public function get ($variableName) {
		// return variable name if variable doesn't exist
		if (!isset($this->languageVariables[$variableName])) return $variableName;

		// get content
		return $this->languageVariables[$variableName]->variableContent;
	}

	/**
	 * Returns the whole list of variables.
	 * @return                        array<string>
	 * @api
	 */
	public function getVariables () {
		return $this->languageVariables;
	}
}

?>