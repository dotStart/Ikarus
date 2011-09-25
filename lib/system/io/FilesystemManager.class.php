<?php
namespace ikarus\system\io;
use ikarus\system\Ikarus;
use ikarus\util\FileUtil;

/**
 * Manages filesystem actions
 * @author		Johannes Donath
 * @copyright		2011 DEVel Fusion
 * @package		com.develfusion.ikarus
 * @subpackage		system
 * @category		Ikarus Framework
 * @license		GNU Lesser Public License <http://www.gnu.org/licenses/lgpl.txt>
 * @version		2.0.0-0001
 */
class FilesystemManager {
	
	/**
	 * Contains a prefix used for adapter class names
	 * @var			string
	 */
	const FILESYSTEM_ADAPTER_CLASS_PREFIX = 'ikarus\\system\\io\\adapter\\';
	
	/**
	 * Contains a suffix used for adapter class names
	 * @var			string
	 */
	const FILESYSTEM_ADAPTER_CLASS_SUFFIX = 'FilesystemAdapter';
	
	/**
	 * Contains all available connection handles
	 * @var			array<ikarus\system\io\adapter\IFilesystemAdapter>
	 */
	protected $connections = array();
	
	/**
	 * Contains the current default adapter
	 * @var			ikarus\ystem\io\adapter\IFilesystemAdapter
	 */
	protected $defaultAdapter = null;
	
	/**
	 * Contains a list of loaded adapters
	 * @var			array<string>
	 */
	protected $loadedAdapters = array();
	
	/**
	 * Creates a new filesystem connection
	 * @param			string			$adapterName
	 * @param			array			$adapterParameters
	 * @param			string			$linkID
	 * @throws			SystemException
	 * @return			ikarus\system\io\adapter\IFilesystemAdapter
	 */
	public function createConnection($adapterName, $adapterParameters = array(), $linkID = null) {
		// validate adapter name
		if (!$this->adapterIsLoaded($adapterName)) throw new SystemException("Cannot create a new connection with filesystem adapter '%s': The adapter was not loaded");
		
		// get class name
		$className = static::FILESYSTEM_ADAPTER_CLASS_PREFIX.ucfirst($adapterName).static::FILESYSTEM_ADAPTER_CLASS_SUFFIX;
		
		// check for php side support
		if (!call_user_func(array($className, 'isSupported'))) throw new SystemException("Cannot create a new connection with filesystem adapter '%s': The adapter is not supported by php");
		
		// create instance
		$adapter = new $className($adapterParameters);
		
		// save
		if ($linkID !== null) $this->connections[$linkID] = $adapter;
		return $this->connections[] = $adapter;
	}
	
	/**
	 * Returns the connection with the given linkID
	 * @param			string			$linkID
	 * @return			ikarus\system\io\adapter\IFilesystemAdapter
	 */
	public function getConnection($linkID) {
		if (isset($this->connections[$linkID])) return $this->connections[$linkID];
		return null;
	}
	
	/**
	 * Returns the current active default adapter
	 * @return			ikarus\system\io\adapter\IFilesystemAdapter
	 */
	public function getDefaultAdapter() {
		return $this->defaultAdapter;
	}
	
	/**
	 * Sets the default adapter
	 * @param			ikarus\system\io\adapter\IFilesystemAdapter		$handle
	 * @return			void
	 */
	public function setDefaultAdapter(adapter\IFilesystemAdapter $handle) {
		$this->defaultAdapter = $handle;
	}
	
	/**
	 * Starts the default adapter
	 * @return			void
	 */
	public function startDefaultAdapter() {
		// load adapter
		$this->loadAdapter(Ikarus::getConfiguration()->get('filesystem.general.defaultAdapter'));
		
		// create new connection
		$handle = $this->createConnection(Ikarus::getConfiguration()->get('filesystem.general.defaultAdapter'), Ikarus::getConfiguration()->get('filesystem.general.adapterParameters'));
		
		// set as default
		$this->setDefaultAdapter($handle);
	}
}
?>