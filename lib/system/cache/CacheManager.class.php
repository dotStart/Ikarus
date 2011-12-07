<?php
namespace ikarus\system\cache;
use ikarus\system\exception\StrictStandardException;
use ikarus\system\Ikarus;
use ikarus\util\ClassUtil;

/**
 * Manages all cache sources
 * @author		Johannes Donath
 * @copyright		2011 DEVel Fusion
 * @package		com.develfusion.ikarus
 * @subpackage		system
 * @category		Ikarus Framework
 * @license		GNU Lesser Public License <http://www.gnu.org/licenses/lgpl.txt>
 * @version		2.0.0-0001
 */
class CacheManager {
	
	/**
	 * Contains a prefix for adapter class names
	 * @var				string
	 */
	const ADAPTER_CLASS_PREFIX = 'ikarus\\system\\cache\\adapter\\';
	
	/**
	 * Contains all active cache connections
	 * @var				array<ikarus\system\cache\adapter\ICacheAdapter>
	 */
	protected $connections = array();
	
	/**
	 * Contains the current default adapter
	 * @var				ikarus\system\cache\adapter\ICacheAdapter;
	 */
	protected $defaultAdapter = null;
	
	/**
	 * Contains predefined adapter fallbacks
	 * @var				array<string>
	 */
	protected $fallbacks = array();
	
	/**
	 * Contains a list of loaded adapters
	 * @var				array<string>
	 */
	protected $loadedAdapters = array();
	
	/**
	 * Creates a new cache connection
	 * @param			string			$adapterName
	 * @param			array			$parameters
	 * @param			string			$linkID
	 * @throws			SystemException
	 * @return			ikarus\system\cache\adapter.ICacheAdapter
	 */
	public function createConnection($adapterName, $parameters = array(), $linkID = null) {
		// validate adapter name
		if (!$this->adapterIsLoaded($adapterName)) throw new SystemException("Cannot start adapter '%s': The adapter was not loaded");
		
		// get class name
		$className = static::ADAPTER_CLASS_PREFIX.$adapterName;
		
		try {
			// create instance
			$instance = new $className($parameters);
		} Catch (SystemException $ex) {
			if (!isset($this->fallbacks[$linkID])) throw $ex;
			$instance = $this->getConnection($this->fallbacks[$linkID]);
		}
		
		if ($linkID !== null) $this->connections[$linkID] = $instance;
		return $this->connections[] = $instance;
	}
	
	/**
	 * Returns the current default adapter
	 * @return			ikarus\system\cache\adapter.ICacheAdapter
	 */
	public function getDefaultAdapter() {
		return $this->defaultAdapter;
	}
	
	/**
	 * Returns true if the given adapter is already loaded
	 * @param			string			$adapterName
	 * @return			boolean
	 */
	public function adapterIsLoaded($adapterName) {
		return array_key_exists($adapterName, $this->loadedAdapters);
	}
	
	/**
	 * Loads an adapter
	 * @param			string			$adapterName
	 * @throws			StrictStandardException
	 * @return			boolean
	 */
	public function loadAdapter($adapterName) {
		// get class name
		$className = static::ADAPTER_CLASS_PREFIX.$adapterName;
		
		// validate adapter
		if (!class_exists($className)) throw new StrictStandardException("The cache adapter class '%s' for adapter '%s' does not exist", $className, $adapterName);
		if (!ClassUtil::isInstanceOf($className, 'ikarus\system\cache\adapter\ICacheAdapter')) throw new StrictStandardException("The cache adapter class '%s' of adapter '%s' is not an implementation of ikarus\\system\\cache\\adapter\\ICacheAdapter");
		
		// check for php side support
		if (!call_user_func(array($className, 'isSupported'))) return false;
		
		// add to loaded adapter list
		$this->loadedAdapters[$adapterName] = $className;
		return true;
	}
	
	/**
	 * Loads all available adapters
	 * @return			void
	 */
	protected function loadAdapters() {
		$sql = "SELECT
				*
			FROM
				ikarus".IKARUS_N."_cache_adapter";
		$stmt = Ikarus::getDatabaseManager()->getDefaultAdapter()->prepareStatement($sql);
		$resultList = $stmt->fetchList();
		
		foreach($resultList as $result) {
			$this->loadAdapter($result->adapterClass);
		}
	}
	
	/**
	 * Sets the default cache adapter
	 * @param			ikarus\system\cache\adapter\ICacheAdapter			$handle
	 */
	public function setDefaultAdapter(adapter\ICacheAdapter $handle) {
		// set as default
		$this->defaultAdapter = $handle;
	}
	
	/**
	 * Sets a fallback for specified adapter
	 * @param			string			$linkID
	 * @param			string			$fallback
	 * @throws			SystemException
	 * @return			void
	 */
	public function setFallback($linkID, $fallback) {
		// validate linkIDs
		if (!array_key_exists($linkID, $this->connections)) throw new SystemException("Cannot create fallback: The specified linkID does not name a cache connection", $linkID);
		
		// save fallback
		$this->fallbacks[$linkID] = $fallback;
	}
	
	/**
	 * Closes all cache connections
	 * @return			void
	 */
	public function shutdown() {
		foreach($this->connections as $connection) {
			$connection->shutdown();
		}
	}
	
	/**
	 * Starts all cache connections
	 * @return			void
	 */
	protected function startAdapters() {
		$sql = "SELECT
				*,
				adapter.adapterClass
			FROM
				ikarus".IKARUS_N."_cache_source source
			LEFT JOIN
				ikarus".IKARUS_N."_cache_adapter adapter
			ON
				(source.adapterID = adapter.adapterID)";
		$stmt = Ikarus::getDatabaseManager()->getDefaultAdapter()->prepareStatement($sql);
		$resultList = $stmt->fetchList();
		
		foreach($resultList as $result) {
			$adapter = $this->createConnection($result->adapterClass, $result->adapterParameters, $result->connectionID);
			if ($result->isDefaultConnection) $this->setDefaultAdapter($adapter);
			if ($result->fallbackFor) $this->setFallback($result->connectionID, $result->fallbackFor);
		}
	}
	
	/**
	 * Starts the default adapter
	 * @return		void
	 */
	public function startDefaultAdapter() {
		$this->loadAdapters();
		$this->startAdapters();
	}
}
?>