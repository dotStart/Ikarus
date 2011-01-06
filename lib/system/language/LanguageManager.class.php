<?php
// ikarus imports
require_once(IKARUS_DIR.'lib/system/io/File.class.php');
require_once(IKARUS_DIR.'lib/system/language/Language.class.php');

/**
 * Provides methods for localisations
 * @author		Johannes Donath
 * @copyright		2010 DEVel Fusion
 * @package		com.develfusion.ikarus
 * @subpackage		system
 * @category		Ikarus Framework
 * @license		GNU Lesser Public License <http://www.gnu.org/licenses/lgpl.txt>
 * @version		1.0.0-0036
 */
class LanguageManager {

	/**
	 * Contains the dir where we'll store our cache files
	 * @var	string
	 */
	const LANGUAGE_CACHE_DIR = 'language/';

	/**
	 * Contains an instance of LanguageManager
	 * Note: LanguageManager uses the factory pattern
	 * @var LanguageManager
	 */
	protected static $instance = null;

	/**
	 * Contains all language variables for the current language
	 * @var array<string>
	 */
	protected $items = array();

	/**
	 * Contains the Language object for the current choosen language
	 * @var Language
	 */
	protected $language = null;

	/**
	 * Contains the current language code
	 * @var string
	 */
	protected $languageCode = '';

	/**
	 * Contains the current languageID
	 * @var integer
	 */
	protected $languageID = 0;

	/**
	 * Contains supported charsets
	 * @var array
	 */
	public static $supportedCharsets = array(
		// latin1
		'ISO-8859-1' => array(
			'multibyte' => false,
			'languages' => array('en', 'de', 'de-informal', 'es', 'it', 'nl', 'pt', 'pt-BR', 'sv', 'da', 'no', 'fi')
		),

		// latin2
		'ISO-8859-2' => array(
			'multibyte' => false,
			'languages' => array('en', 'de', 'de-informal', 'bs', 'hr', 'sr', 'sk', 'pl', 'cs', 'hu', 'ro')
		),

		// greek
		'ISO-8859-7' => array(
			'multibyte' => false,
			'languages' => array('en', 'el')
		),

		// hebrew
		'ISO-8859-8' => array(
			'multibyte' => false,
			'languages' => array('en', 'he')
		),

		// latin5 (turkish)
		'ISO-8859-9' => array(
			'multibyte' => false,
			'languages' => array('en', 'de', 'de-informal', 'tr')
		),

		// japanese
		'EUC-JP' => array(
			'multibyte' => true,
			'languages' => array('en', 'ja')
		),

		'SJIS' => array(
			'multibyte' => true,
			'languages' => array('ja')
		),

		// chinese (traditional)
		//'BIG-5' => array(
			// 'multibyte' => true,
			// 'languages' => array('en', 'zh-TW')
		// ),

		// chinese (simlified)
		'CP936' => array(
			'multibyte' => true,
			'languages' => array('en', 'zh-CN')
		),

		// chinese (simplified)
		'EUC-CN' => array(
			'multibyte' => true,
			'languages' => array('en', 'zh-CN')
		),

		// russian
		'KOI8-R' => array(
			'multibyte' => false,
			'languages' => array('en', 'ru')
		),

		'Windows-1251' => array(
			'multibyte' => false,
			'languages' => array('en', 'ru')
		),

		// korean
		'EUC-KR' => array(
			'multibyte' => true,
			'languages' => array('en', 'ko')
		)
	);

	/**
	 * If this is set to true we'll have to format dates localized
	 * @var boolean
	 */
	public static $dateFormatLocalized = false;

	/**
	 * Creates a new instance of LanguageManager
	 * @param	integer	$languageID
	 * @deprecated
	 */
	protected function __construct($languageID = 0) {
		$this->languageID = $languageID;

		// try to load cache
		try {
			if (XDEBUG) xdebug_disable();
			$this->loadCache();
			if (XDEBUG) xdebug_enable();
		} catch (SystemException $ex) {
			// language not found
			$this->findPreferredLanguage();
		}

		// Call init method
		$this->init();
	}

	/**
	 * Returnes true if the cache file for the given language needs a rebuild
	 * @param	Language	$language
	 * @return boolean
	 */
	protected function cacheFileNeedsRebuild($language = null) {
		if ($language === null) $language = $this->language;

		// try to find file
		if (!file_exists(IKARUS_DIR.self::LANGUAGE_CACHE_DIR.'language.'.$language->languageID.'.php')) return true;

		// check for readable files
		if (!is_readable(IKARUS_DIR.self::LANGUAGE_CACHE_DIR.'language.'.$language->languageID.'.php')) return true;

		return false;
	}

	/**
	 * Sets the preferred language as active language
	 * @throws SystemException
	 */
	protected function findPreferredLanguage() {
		// get available languages
		$languages = self::getAvailableLanguages();

		foreach($languages as $languageCode => $language) {
			if ($language->isDefault()) $defaultLanguage = $language;
		}
		
		// validate
		if (!isset($defaultLanguage) and !count($languages)) throw new SystemException("No languages available");
		
		// WORKAROUND
		// Set first language if default language isn't available
		$defaultLanguage = $languages[0];

		// get preferred language
		$this->setActiveLanguage($this->getPreferredLanguage($languages, $defaultLanguage));
	}

	/**
	 * Removes additional identifications (Such as -informal from de-informal) for language codes from given language code
	 * @param	string	$languageCode
	 * @return string
	 */
	public static function fixLanguageCode($languageCode) {
		return preg_replace('/-[a-z0-9]+/', '', $languageCode);
	}
	
	/**
	 * Gets all available languages from database
	 * @param	integer	$packageID
	 * @return array<Language>
	 */
	public static function getAvailableLanguages($packageID = PACKAGE_ID) {
		return IKARUS::getCache()->get(IKARUS_DIR.'cache/cache.'.$packageID.'-languages.php', IKARUS_DIR.'lib/system/cache/CacheBuilderLanguages.class.php');
	}

	/**
	 * Alias for LanguageManager::get()
	 * @see LanguageManager::get()
	 * @deprecated
	 */
	public function getDynamicVariable() {
		return call_user_func_array(array($this, 'get'), func_get_args());
	}
	
	/**
	 * Returnes a LanguageManager instance
	 * @return LanguageManager
	 */
	public static function getInstance() {
		if (self::$instance === null)
			self::$instance = new LanguageManager();

		return self::$instance;
	}

	/**
	 * Returnes the preferred language
	 * @param	array<Language>	$availableLanguages
	 * @param	Language		$defaultLanguage
	 * @return Language
	 */
	protected function getPreferredLanguage($availableLanguages, $defaultLanguage) {
		// check for existing http accept language header
		if (isset($_SERVER['HTTP_ACCEPT_LANGUAGE']) and $_SERVER['HTTP_ACCEPT_LANGUAGE']) {
			// get acceptLanguages header
			$acceptedLanguages = explode(',', str_replace('_', '-', strtolower($_SERVER['HTTP_ACCEPT_LANGUAGE'])));

			// loop through accepted languages
			foreach ($acceptedLanguages as $acceptedLanguage) {
				// loop through available languages
				foreach ($availableLanguages as $availableLanguageCode => $language) {
					$fixedCode = strtolower(self::fixLanguageCode($availableLanguageCode));

					if ($fixedCode == $acceptedLanguage or $fixedCode == preg_replace('%^([a-z]{2}).*$%i', '$1', $acceptedLanguage))
						return $language;
				}
			}
		}

		// return default language
		return $defaultLanguage;
	}

	/**
	 * Inits the language engine
	 */
	protected function init() {
		if (!defined('LANGUAGE_CODE')) {
			define('LANGUAGE_CODE', self::fixLanguageCode($this->data['languageCode']));
			if ((OPTION_CHARSET == 'UTF-8' || self::$supportedCharsets[CHARSET]['multibyte']) && extension_loaded('mbstring')) {
				define('USE_MBSTRING', true);
				mb_internal_encoding(OPTION_CHARSET);
				if (function_exists('mb_regex_encoding')) mb_regex_encoding(OPTION_CHARSET);
				if (OPTION_CHARSET == 'UTF-8') mb_language('uni');
			}
			else {
				define('USE_MBSTRING', false);
			}
		}

		$this->setLocale();
	}

	/**
	 * Loads the language cache for the specified language
	 * @param	$language
	 * @throws SystemException
	 */
	protected function loadCache($language = null) {
		// create language instance if needed
		if ($language === null) $language = new Language($this->languageID);

		// validate language
		if (!$language->languageID) throw new SystemException("Invalid language passed to %s", 'loadCache');

		// get cache
		if ($this->cacheFileNeedsRebuild($language))
			$this->items = $this->rebuildCacheFile($language);
		else
			$this->items = $this->loadCacheFile($language);
	}

	/**
	 * Loads a language cache file
	 * @param	Language	$language
	 * @throws SystemException
	 */
	protected function loadCacheFile($language = null) {
		if ($language === null) $language = $this->language;

		// try to find file
		if (!file_exists(IKARUS_DIR.self::LANGUAGE_CACHE_DIR.'language.'.$language->languageID.'.php')) throw new SystemException("Cannot load language cache file '%s'", IKARUS_DIR.self::LANGUAGE_CACHE_DIR.'language.'.$language->languageID.'.php');

		// load file
		include_once(IKARUS_DIR.self::LANGUAGE_CACHE_DIR.'language.'.$language->languageID.'.php');
	}

	/**
	 * Rebuilds a cache file
	 * @param	Language	$language
	 * @throws Language
	 */
	protected function rebuildCacheFile($language = null) {
		if ($language === null) $language = $this->language;

		// delete old file
		if (file_exists(IKARUS_DIR.self::LANGUAGE_CACHE_DIR.'language.'.$language->languageID.'.php')) @unlink(IKARUS_DIR.self::LANGUAGE_CACHE_DIR.'language.'.$language->languageID.'.php');

		// get data
		$sql = "SELECT
				itemName,
				itemValue
			FROM
				ikarus".IKARUS_N."_language
			WHERE
				packageID = ".PACKAGE_ID."
			AND
				languageID = ".$language->languageID;
		$result = IKARUS::getDatabase()->sendQuery($sql);

		// create file
		$file = new File(IKARUS_DIR.self::LANGUAGE_CACHE_DIR.'language.'.$language->languageID.'.php');
		$file->write("<?php\n/**\n * Ikarus Language Cache File\n * Generated on ".gmdate('r')."\n **/\n\n");

		while($row = IKARUS::getDatabase()->fetchArray($result)) {
			$file->write("\t\$this->items['".$row['itemName']."'] = '".StringUtil::replace("'", "\'", $row['itemValue'])."';\n");
		}

		$file->write("\n/** EOF **/\n?>");
		$file->close();

		$this->loadCache($language);
	}

	/**
	 * Sets a language as active
	 * @param	Language	$language
	 * @throws SystemException
	 */
	public function setActiveLanguage($language) {
		// set properties
		$this->languageID = $language->languageID;
		$this->language = $language;

		// try to load cache
		$this->loadCache();
	}

	/**
	 * Calls the php setlocale method and defines the PAGE_DIRECTION constant
	 */
	protected function setLocale() {
		// set page direction
		if (!defined('PAGE_DIRECTION')) define('PAGE_DIRECTION', $this->get('ikarus.global.pageDirection'));

		// set php locales
		setlocale(LC_COLLATE, $this->get('ikarus.global.locale.unix').'.'.OPTION_CHARSET, $this->get('ikarus.global.locale.unix').'.'.StringUtil::replace('ISO-', 'ISO', OPTION_CHARSET), $this->get('ikarus.global.locale.unix'), $this->get('ikarus.global.locale.win'));
		setlocale(LC_CTYPE, $this->get('ikarus.global.locale.unix').'.'.OPTION_CHARSET, $this->get('ikarus.global.locale.unix').'.'.StringUtil::replace('ISO-', 'ISO', OPTION_CHARSET), $this->get('ikarus.global.locale.unix'), $this->get('ikarus.global.locale.win'));

		// get dateFormatLocalized option
		if (setlocale(LC_TIME, $this->get('ikarus.global.locale.unix').'.'.OPTION_CHARSET, $this->get('ikarus.global.locale.unix').'.'.StringUtil::replace('ISO-', 'ISO', OPTION_CHARSET), $this->get('ikarus.global.locale.unix'), $this->get('ikarus.global.locale.win')) !== false)
				self::$dateFormatLocalized = true;
	}
}
?>