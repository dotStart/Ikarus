<?php
// ikarus imports
require_once(IKARUS_DIR.'lib/core.functions.php');

require_once(IKARUS_DIR.'lib/system/cache/CacheSourceManager.class.php');
require_once(IKARUS_DIR.'lib/system/database/DatabaseManager.class.php');
require_once(IKARUS_DIR.'lib/system/event/EventHandler.class.php');
require_once(IKARUS_DIR.'lib/system/language/LanguageManager.class.php');
require_once(IKARUS_DIR.'lib/system/option/Options.class.php');
require_once(IKARUS_DIR.'lib/system/session/SessionFactory.class.php');
require_once(IKARUS_DIR.'lib/system/style/StyleManager.class.php');
require_once(IKARUS_DIR.'lib/system/template/Template.class.php');

// defines
define('IKARUS_VERSION_MAJOR', 1);
define('IKARUS_VERSION_MINOR', 0);
define('IKARUS_VERSION_PATCH', 0);
define('IKARUS_VERSION_BUILD', '0001');
define('IKARUS_VERSION_EXTENDED', ' Alpha 1');
define('IKARUS_VERSION', IKARUS_VERSION_MAJOR.'.'.IKARUS_VERSION_MINOR.'.'.IKARUS_VERSION_PATCH.'-'.IKARUS_VERSION_BUILD.IKARUS_VERSION_EXTENDED);
define('IKARUS_VERSION_STABLE', false);
if (!defined('PACKAGE_ID')) define('PACKAGE_ID', 1);
define('XDEBUG', (function_exists('xdebug_is_enabled') and xdebug_is_enabled()));

/**
 * @author		Johannes Donath
 * @copyright		2010 DEVel Fusion
 * @package		com.develfusion.ikarus
 * @subpackage		system
 * @category		Ikarus Framework
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
	 * Contains the suffix for database drivers
	 * @var string
	 */
	const DATABASE_DRIVER_SUFFIX = 'DatabaseDriver';

	/**
	 * Contains the environment
	 * @var string
	 */
	const ENVIRONMENT = 'frontend';

	/**
	 * Contains the name of the file that contains all stored options from database that should used for the current application instance
	 * @var	string
	 */
	const OPTION_FILE = 'options.inc.php';

	/**
	 * Contains the dir of for templates
	 * @var string
	 */
	const TEMPLATE_DIR = 'templates/';

	/**
	 * Contains a list of additional methods
	 * With this little variable we can add additional methods such as getCronjob() to this core
	 * A little tip: Use the factory pattern
	 * Note: All method calls will redirected to defined method. See call_user_func_array syntax
	 * @var	array
	 */
	protected static $additionalMethods = array();

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
	 * Contains the current LanguageManager instance
	 * @var Language
	 */
	protected static $languageObj = null;

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
	 * Contains the active Style instance
	 * @var Style
	 */
	protected static $styleObj = null;

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
		
		// disable xdebug
		if (function_exists('xdebug_disable')/* and !DEBUG*/) xdebug_disable();

		// init components
		$this->initDatabase();
		$this->initOptions();
		$this->initCache();
		$this->initAdditionalMethods();
		$this->initLanguage();
		$this->initTemplate();
		$this->initSession();
		$this->initStyle();

		// fire event
		EventHandler::fire('IKARUS', 'finishedInit');
	}

	/**
	 * Replacement of the "__destruct()" method.
	 * Seems that under specific conditions (windows) the destructor is not called automatically.
	 * So we use the php register_shutdown_function to register an own destructor method.
	 * Flushs the output, updates the session and executes the shutdown queries.
	 */
	public static function destruct() {
		// flush ouput
		if (ob_get_level() and ini_get('output_handler'))
			ob_flush();
		else
			flush();

		// update session
		if (self::$sessionObj !== null)
			self::$sessionObj->update();

		// close cache sources
		if (self::$cacheObj !== null)
			self::$cacheObj->closeCacheSources();

		// shut down database
		if (self::$dbObj !== null)
			self::$dbObj->shutdown();
	}

	/**
	 * Reads all additional methods from cache
	 */
	protected function initAdditionalMethods() {
		self::$additionalMethods = self::$cacheObj->get(IKARUS_DIR.'cache/cache.'.PACKAGE_ID.'-additionalMethods.php', IKARUS_DIR.'lib/system/cache/CacheBuilderAdditionalMethods.class.php');
	}

	/**
	 * Initialisizes the CacheSourceManager instance
	 */
	protected function initCache() {
		// option fallback
		if (!defined('OPTION_FALLBACK_CACHE_SOURCE')) define('OPTION_FALLBACK_CACHE_SOURCE', 'Disk');

		// start CacheSourceManager
		self::$cacheObj = new CacheSourceManager(OPTION_FALLBACK_CACHE_SOURCE);
	}

	/**
	 * Initialisizes the DatabaseManager instance and adds the default database connection to connection pool
	 * @throws SystemException
	 */
	protected function initDatabase() {
		// validate configuration
		// Ok ... If the configuration file is missing a funny error will occour and the exception will not appear on screen ... shit
		if (!file_exists(IKARUS_DIR.self::CONFIGURATION_FILE)) throw new SystemException("Cannot read configuration file '".self::CONFIGURATION_FILE."'", 1000);

		// include configuration
		require_once(IKARUS_DIR.self::CONFIGURATION_FILE);

		// start DatabaseManager
		self::$dbObj = new DatabaseManager();

		// add connection
		self::$dbObj->addConnection($dbType.self::DATABASE_DRIVER_SUFFIX, $dbHostname, $dbUsername, $dbPassword, $dbDatabase);
	}

	/**
	 * Initialisizes the LanguageManager instance
	 */
	protected function initLanguage() {
		self::$languageObj = LanguageManager::getInstance();
	}

	/**
	 * Initialisizes options
	 */
	protected function initOptions() {
		// regenerate option cache if needed or include option cache
		if (!file_exists(self::$packageDir.self::OPTION_FILE)) Options::generate(self::$packageDir.self::OPTION_FILE);

		// include option file
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
	 * Initialisizes the Style instance
	 */
	protected function initStyle() {
		// get StyleManager instance
		$styleManager = StyleManager::getInstance(self::ENVIRONMENT);

		// get active style
		self::$styleObj = $styleManager->getActiveStyle();
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
	 * Returnes the current LanguageManager instance
	 * @return LanguageManager
	 */
	public static final function getLanguage() {
		return self::$languageObj;
	}

	/**
	 * Alias for IKARUS::getLanguage()
	 * @see IKARUS::getLanguage()
	 */
	public static final function getLang() {
		return self::getLanguage();
	}

	/**
	 * Returnes the current package dir
	 * @return string
	 */
	public static final function getPackageDir() {
		return self::$packageDir;
	}

	/**
	 * Returnes all package dirs
	 * @return array<string>
	 */
	public static final function getPackageDirs() {
		return self::$packageDirs;
	}

	/**
	 * Returnes the current Session instance
	 * @return Session
	 */
	public static final function getSession() {
		return self::$sessionObj;
	}

	/**
	 * Returnes the current Style instance
	 * @return Style
	 */
	public static final function getStyle() {
		return self::$styleObj;
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
	
	/**
	 * Handles errors
	 * @param	integer	$errNo
	 * @param	string	$errMessage
	 * @param	string	$errFile
	 * @param	integer	$errLine
	 * @throws	SystemException
	 */
	public static function handleError($errorNo, $errMessage, $errFile, $errLine) {
		if (error_reporting() != 0) {
			$type = 'error';
			switch ($errorNo) {
				case 2: $type = 'warning';
					break;
				case 8: $type = 'notice';
					break;
			}
			
			throw new SystemException('PHP %s in file %s (%u): %s', $type, $errFile, $errLine, $errMessage);
		}
	}
	
	/**
	 * Handles uncought exceptions
	 * @param	Exception	$ex
	 */
	public static function handleException(Exception $ex) {
		if($ex instanceof PrintableException)
			$ex->show();
		else
			print($ex);
		
		exit;
	}

	/**
	 * Allows additional method hooks
	 * @param	string	$method
	 * @param	array	$arguments
	 */
	public static function __callStatic($method, $arguments) {
		if (isset(self::$additionalMethods[$method])) {
			// call method
			return call_user_func_array(self::$additionalMethods[$method], $arguments);
		}

		// no additional method known -> error
		throw new SystemException("Method '%s' does not exist in class %s", $method, 'IKARUS');
	}

	/**
	 * Calls getXYZ methods
	 * Note: This will used by template system
	 * @param	string	$variable
	 * @return mixed
	 * @throws SystemException
	 */
	public function __get($variable) {
		// create searched method name
		$methodName = 'get'.ucfirst($variable);

		if (method_exists('IKARUS', $methodName))
			return call_user_func(array('IKARUS', $methodName));

		// method does not exist -> error
		throw new SystemException("Property '%s' does not exist in class %s", $variable, 'IKARUS');
	}
}
?>