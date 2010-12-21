<?php
// ikarus imports
require_once(IKARUS_DIR.'lib/core.functions.php');

require_once(IKARUS_DIR.'lib/system/cache/CacheSourceManager.class.php');
require_once(IKARUS_DIR.'lib/system/database/DatabaseManager.class.php');
require_once(IKARUS_DIR.'lib/system/event/EventHandler.class.php');
require_once(IKARUS_DIR.'lib/system/option/Options.class.php');
require_once(IKARUS_DIR.'lib/system/session/SessionFactory.class.php');
require_once(IKARUS_DIR.'lib/system/template/Template.class.php');

// defines
define('IKARUS_VERSION_MAJOR', 1);
define('IKARUS_VERSION_MINOR', 0);
define('IKARUS_VERSION_PATCH', 0);
define('IKARUS_VERSION_BUILD', 0001);
define('IKARUS_VERSION_EXTENDED', ' Alpha 1');
define('IKARUS_VERSION', IKARUS_VERSION_MAJOR.'.'.IKARUS_VERSION_MINOR.'.'.IKARUS_VERSION_PATCH.'-'.IKARUS_VERSION_BUILD.IKARUS_VERSION_EXTENDED);
define('IKARUS_VERSION_STABLE', false);

/**
 * @author		Johannes Donath
 * @copyright	2010 DEVel Fusion
 * @package		com.develfusion.ikarus
 * @subpackage	system
 * @category	Ikarus Framework
 * @license		GNU Lesser Public License <http://www.gnu.org/licenses/lgpl.txt>
 * @version		1.0.0-0001 (Codename: Avior)
 */
class IKARUS {

	/**
	 * Contains the name of the configuration file that should used for the current application instance
	 * @var	string
	 */
	const CONFIGURATION_FILE = 'config.inc.php';

	/**
	 * Contains the name of the file that contains all stored options from database that should used for the current application instance
	 * @var	string
	 */
	const OPTION_FILE = 'options.inc.php';

	/**
	 * Contains the current CacheSourceManager instance
	 * @var	CacheSourceManager
	 */
	protected static $cacheObj = null;

	/**
	 * Contains the current DatabaseManager instance
	 * @var	DatabaseManager
	 */
	protected static $dbObj = null;

	/**
	 * Contains the application dir of the current application
	 * @var string
	 */
	protected static $packageDir = '';

	/**
	 * Contains a list of all application dirs
	 * @var array<string>
	 */
	protected static $packageDirs = array();

	/**
	 * Contains the current Session instance
	 * @var	Session
	 */
	protected static $sessionObj = null;

	/**
	 * Contains the current Template instance
	 * @var	Template
	 */
	protected static $tplObj = null;

	/**
	 * Contains the current User instance
	 * @var User
	 */
	protected static $userObj = null;

	/**
	 * Starts IKARUS
	 * @param	array<string>	$packageDirs	Contains a list of all application dirs
	 */
	public function __construct($packageDirs = array(IKARUS_DIR), $packageDir = IKARUS_DIR) {
		// handle parameters
		self::$packageDirs = $packageDirs;
		self::$packageDir = $packageDir;

		// init components
		$this->initDatabase();
		$this->initOptions();
		$this->initCache();
		$this->initTemplate();
		$this->initSession();

		// fire event
		EventHandler::fire('IKARUS', 'finishedInit');
	}

	/**
	 * Initialisizes the CacheSourceManager instance
	 */
	protected function initCache() {
		// option fallback
		if (!defined('OPTION_CACHE_SOURCE')) define('OPTION_CACHE_SOURCE', 'Disk');

		// start CacheSourceManager
		self::$cacheObj = new CacheSourceManager(OPTION_CACHE_SOURCE);
	}

	/**
	 * Initialisizes the DatabaseManager instance and adds the default database connection to connection pool
	 * @throws SystemException
	 */
	protected function initDatabase() {
		// validate configuration
		if (!file_exists(IKARUS_DIR.self::CONFIGURATION_FILE)) throw new SystemException("Cannot read configuration file '".self::CONFIGURATION_FILE."'", 1000);

		// include configuration
		require_once(IKARUS_DIR.self::CONFIGURATION_FILE);

		// start DatabaseManager
		self::$dbObj = new DatabaseManager();

		// add connection
		self::$dbObj->addConnection($dbType, $dbHostname, $dbUsername, $dbPassword, $dbDatabase);
	}

	/**
	 * Initialisizes options
	 */
	protected function initOptions() {
		// regenerate option cache if needed or include option cache
		if (!file_exists(self::$packageDir.self::OPTION_FILE))
			Options::generate(self::$packageDir.self::OPTION_FILE);
		else
			require_once(self::$packageDir.self::OPTION_FILE);
	}

	/**
	 * Initialisizes all session related instances (Such as User instance and Session instance)
	 */
	protected function initSession() {
		// get SessionFactory instance
		$sessionFactory = SessionFactory::getInstance();

		// get session object
		self::$sessionObj = $sessionFactory->getSession();
		self::$userObj = self::$sessionObj->getUser();
	}

	/**
	 * Initialisizes the Template instance
	 */
	protected function initTemplate() {
		// start Template
		self::$tplObj = new Template(ArrayUtil::appendSuffix(self::$packageDirs, self::TEMPLATE_DIR));
	}

	/**
	 * Returnes the current CacheSourceManager instance
	 * @return CacheSourceManager
	 */
	public static final function getCache() {
		return self::$cacheObj;
	}

	/**
	 * Returnes the current DatabaseManager instance
	 * @return	DatabaseManager
	 */
	public static final function getDatabase() {
		return self::$dbObj;
	}

	/**
	 * Alias for IKARUS::getDatabase()
	 * @see IKARUS::getDatabase()
	 */
	public static final function getDB() {
		return self::getDatabase();
	}

	/**
	 * Returnes the current package dir
	 */
	public static final function getPackageDir() {
		return self::$packageDir;
	}

	/**
	 * Returnes all package dirs
	 */
	public static final function getPackageDirs() {
		return self::$packageDirs;
	}

	/**
	 * Returnes the current Session instance
	 */
	public static final function getSession() {
		return self::$sessionObj;
	}

	/**
	 * Returnes the current Template instance
	 * @return Template
	 */
	public static final function getTemplate() {
		return self::$tplObj;
	}

	/**
	 * Alias for IKARUS::getTemplate()
	 * @see IKARUS::getTemplate()
	 */
	public static final function getTPL() {
		return self::getTemplate();
	}

	/**
	 * Returnes the current User instance
	 */
	public static final function getUser() {
		return self::$userObj;
	}
}
?>