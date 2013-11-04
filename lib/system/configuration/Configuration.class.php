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
namespace ikarus\system\configuration;

use ikarus\system\database\QueryEditor;
use ikarus\system\exception\StrictStandardException;
use ikarus\system\exception\SystemException;
use ikarus\system\Ikarus;
use ikarus\util\ClassUtil;
use ikarus\util\DependencyUtil;

/**
 * Reads and writes configuration files
 * @author                    Johannes Donath
 * @copyright                 2011 Evil-Co.de
 * @package                   de.ikarus-framework.core
 * @subpackage                system
 * @category                  Ikarus Framework
 * @license                   GNU Lesser Public License <http://www.gnu.org/licenses/lgpl.txt>
 * @version                   2.0.0-0001
 */
class Configuration {

	/**
	 * Contains a class prefix used for option type classes
	 * @var                        string
	 */
	const OPTION_CLASS_PREFIX = 'ikarus\\system\\configuration\\type\\';

	/**
	 * Contains a class suffix used for option type classes
	 * @var                        string
	 */
	const OPTION_CLASS_SUFFIX = 'ConfigurationType';

	/**
	 * Contains the File instance for given configuration file
	 * @var                File
	 */
	protected $file = null;

	/**
	 * Contains the path to our option file
	 * @var                string
	 */
	protected $fileName = '';

	/**
	 * Contains all options
	 * @var                array<mixed>
	 */
	protected $options = null;

	/**
	 * Contains the ID of the package
	 * @var                        integer
	 */
	protected $packageID = 0;

	/**
	 * Contains a raw list of options (if needed)
	 * @var                        array
	 */
	protected $rawOptionList = array ();

	/**
	 * Creates a new instance of type Configuration
	 * @param                string $fileName
	 */
	public function __construct ($fileName, $packageID = IKARUS_ID) {
		$this->fileName = $fileName;
		$this->packageID = $packageID;
	}

	/**
	 * Recreates a new configuration file
	 * @throws                SystemException
	 * @return                void
	 */
	protected function createOptionFile () {
		// read options
		$this->readOptionsFromDatabase ();

		// delete old file
		if (Ikarus::getFilesystemManager ()->getDefaultAdapter ()->fileExists ($this->fileName)) Ikarus::getFilesystemManager ()->getDefaultAdapter ()->deleteFile ($this->fileName);

		// create file handle
		$this->file = Ikarus::getFilesystemManager ()->createFile ($this->fileName);

		// write header
		$this->file->append ("<?php\n");
		$this->file->append ("/**\n");
		$this->file->append (" * Ikarus Framework " . IKARUS_VERSION . " Configuration File\n");
		$this->file->append (" * Installation Path: " . IKARUS_DIR . "\n");
		$this->file->append (" * Please do not edit this file manually!\n");
		$this->file->append (" **/\n\n");

		// write security check
		$this->file->append ("// Security check\n");
		$this->file->append ("if (get_class(\$this) != 'ikarus\system\configuration\Configuration') die;\n\n");

		// add options
		$this->file->append ("// Options\n");

		// add each option
		foreach ($this->rawOptionList as $option) {
			$this->file->append ("\$this->rawOptionList[] = '" . preg_replace ('~(|\\\)\'~', '\\\'', serialize ($option)) . "';\n");
		}

		// write footer of file
		$this->file->append ("\n/* EOF */\n");
		$this->file->append ("?>");

		// write file
		$this->file->write ();
	}

	/**
	 * Returns the value of specified option
	 * @param                        string $optionName
	 * @api
	 */
	public function get ($optionName) {
		if (isset($this->options[$optionName])) return $this->options[$optionName];

		// throw new StrictStandardException("There is no option named '%s', $optionName);
		return null;
	}

	/**
	 * Returns the real option value
	 * @param                        string $type
	 * @param                        string $value
	 */
	protected function getRealOptionValue ($type, $value) {
		// get type class name
		$className = static::OPTION_CLASS_PREFIX . ucfirst ($type) . static::OPTION_CLASS_SUFFIX;

		// check for existing class
		if (!class_exists ($className)) throw new StrictStandardException("The class '%s' for option type '%s' is missing", $className, $type);
		if (!ClassUtil::isInstanceOf ($className, 'ikarus\system\configuration\type\IConfigurationType')) throw new StrictStandardException("The class '%s' of option type '%s' is not an implementation of ikarus\\system\\configuration\\type\\ConfigurationType", $className, $type);

		// get real variable content
		return call_user_func (array ($className, 'getRealValue'), $value);
	}

	/**
	 * Loads all options from cache or database
	 * Note: We can't call this from __construct() while using ikarus\system\io\FilesystemManager!
	 * @return                        void
	 * @internal                        This method will be called during it's init period.
	 */
	public function loadOptions () {
		try {
			$this->readOptions ();
		} Catch (StrictStandardException $ex) { // Bugfix: If an StrictStandardException occurs while reading the configuration file it will regenerate.
			throw $ex;
		} Catch (SystemException $ex) {
			$this->createOptionFile ();
		}
	}

	/**
	 * Reads the option file
	 * @throws                SystemException
	 * @return                void
	 */
	protected function readOptions () {
		// validate
		if (!file_exists ($this->fileName)) throw new SystemException("Configuration file '%s' does not exist", $this->fileName);
		if (!is_readable ($this->fileName)) throw new SystemException("Configuration file '%s' is not readable", $this->fileName);
		if (!is_file ($this->fileName)) throw new SystemException("Configuration file '%s' is not a file", $this->fileName);

		// load
		require_once ($this->fileName);

		// get options
		foreach ($this->rawOptionList as $key => $option) {
			$this->rawOptionList[$key] = $option = unserialize ($option);
			$this->options[$option->optionName] = $this->getRealOptionValue ($option->optionType, $option->optionValue);
		}
	}

	/**
	 * Reads all options from database
	 * @return                        void
	 */
	protected function readOptionsFromDatabase () {
		// generate query
		$query = new QueryEditor();
		$query->from (array ('ikarus' . IKARUS_N . '_option' => 'ioption'));

		// add dependency clauses
		DependencyUtil::generateDependencyQuery ($this->packageID, $query, 'ioption');

		$stmt = $query->prepare ();
		$resultList = $stmt->fetchList ();

		foreach ($resultList as $result) {
			$this->options[$result->optionName] = $this->getRealOptionValue ($result->optionType, $result->optionValue);
			$this->rawOptionList[] = $result;
		}
	}

	/**
	 * Queues the option file for regeneration.
	 * @return                        void
	 * @api
	 */
	public function regenerate () {
		@unlink ($this->fileName);
	}

	/**
	 * Sets a variable during runtime.
	 * Note: This method will store nothing into database.
	 * @param                        string $key
	 * @param                        mixed  $value
	 * @return                        void
	 * @api
	 */
	public function set ($key, $value) {
		$this->options[$key] = $value;
	}
}

?>