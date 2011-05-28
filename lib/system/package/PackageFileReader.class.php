<?php
namespace ikarus\system\package;
use ikarus\system\exception\SystemException;

use ikarus\system\io\File;
use ikarus\util\StringUtil;

class PackageFileReader {
	
	/**
	 * Contains the fallback language code
	 * @var 	string
	 */
	const DEFAULT_LANGUAGE_CODE = 'en';
	
	/**
	 * Contains the magic number for our file format
	 * @var		string
	 */
	const MAGIC_NUMBER = '%IPF';
	
	/**
	 * Contains package's author
	 * @var		string
	 */
	protected $authorName = '';
	
	/**
	 * Contains author's homepage URL
	 * @var		string
	 */
	protected $authorUrl = '';
	
	/**
	 * Contains the package release date
	 * @var		string
	 */
	protected $date = '';
	
	/**
	 * Contains all packages that are required to install this package
	 * @var		array
	 */
	protected $dependencies = array();
	
	/**
	 * Contains the URL to package documentation
	 * @var		string
	 */
	protected $documentationUrl = '';
	
	/**
	 * Contains all packages that should NOT be installed to install this package
	 * @var		array
	 */
	protected $excludes = array();
	
	/**
	 * Contains a file instance
	 * @var		File
	 */
	protected $file = null;
	
	/**
	 * Contains the complete file content
	 * @var		string
	 */
	protected $fileContent = '';
	
	/**
	 * Contains a splittet version of the complete file
	 * @var		array<string>
	 */
	protected $fileContentSplit = array();
	
	/**
	 * Contains the raw information array
	 * @var		array
	 */
	protected $filePackageArray = array();
	
	/**
	 * Contains the version of current file
	 * @var		string
	 */
	protected $fileVersion = 'unknown';
	
	/**
	 * Contains a list of install instructions
	 * @var		string
	 */
	protected $instructions = array(
		'install'	=> array(),
		'update'	=> array()
	);
	
	/**
	 * Contains package's license
	 * @var		string
	 */
	protected $licenseName = '';
	
	/**
	 * Contains package's license URL
	 * @var		string
	 */
	protected $licenseUrl = '';
	
	/**
	 * Contains the package description in all language variations
	 * @var		array
	 */
	protected $packageDescription = array();
	
	/**
	 * Contains the package identifier
	 * @var		string
	 */
	protected $packageIdentifier = '';
	
	/**
	 * Contains the package name in all language variations
	 * @var		array<string>
	 */
	protected $packageName = array();
	
	/**
	 * Contains the URL of project homepage
	 * @var		string
	 */
	protected $packageUrl = '';
	
	/**
	 * Contains an alias for project homepage (e.g. project name)
	 * @var		string
	 */
	protected $packageUrlAlias = '';
	
	/**
	 * Contains a string that describes what apis are implemented
	 * @var		string
	 */
	protected $provides = '';
	
	/**
	 * Contains the identifier of parent package
	 * @var		string
	 */
	protected $plugin = '';
	
	/**
	 * Contains the project start date (e.g. day of project creation or project idea)
	 * @var 	string
	 */
	protected $projectStartDate = '';
	
	/**
	 * Contains true if this package is a standalone application
	 * @var		boolean
	 */
	protected $standalone = false;
	
	/**
	 * Contains a list of supported file versions
	 * @var		array<string>
	 */
	protected $supportedVersions = array('1.0.0');
	
	/**
	 * Contains the URL to support board
	 * @var		string
	 */
	protected $supportUrl = '';
	
	/**
	 * Contains the package version
	 * @var		string
	 */
	protected $version = '';
	
	/**
	 * Creates a new instance of type PackageFileReader
	 * @param		string		$fileName		Contains the path to filename
	 */
	public function __construct($fileName) {
		// get file
		$this->file = new File($fileName, 'r');
		$this->readFile();
		
		// get information from file header
		$this->readFileInformation();
		$this->readPackageInformation();
		$this->readPackageDependencies();
		$this->readPacakgeSystemRequirements();
		$this->readPackageExcludes();
		$this->readPackageInstallations();
		$this->readPackageInstructions();
	}
	
	/**
	 * Reads the complete file content
	 * @return		void
	 */
	protected function readFile() {
		// read file content
		while(!$this->file->eof())
			$this->fileContent .= $file->gets(1024);
		
		// split content
		$this->fileContentSplit = explode(chr(0), $this->fileContent, 2);
	}
	
	/**
	 * Reads general file information
	 * @return		void
	 * @throws		PackageFileException
	 */
	protected function readFileInformation() {
		// find version string ...
		// little file example:
		// %IPF-1.0.0
		$version = $this->fileContentSplit[0];
		
		// remove file header
		$version = substr($version, (stripos($version, '-') + 1));
		
		// validate version
		if (!in_array($version, $this->supportedVersions)) throw new PackageFileException("Unsupported Ikarus Package Format version '%s' in file %s", $version, $this->file->getFilename());
		
		// set version globally
		$this->fileVersion = $version;
		
		// get raw information array
		$this->filePackageArray = json_decode(gzinflate($this->fileContentSplit[1]));
	}
	
	/**
	 * Reads all package dependencies
	 * @return		void
	 * @throws		PackageFileException
	 */
	protected function readPackageDependencies() {
		// basic validation
		if (!isset($this->filePackageArray['dependencies']) or !is_array($this->filePackageArray['dependencies']) or !count($this->filePackageArray['dependencies'])) throw new PackageFileException("Cannot read IPF file %s: Missing dependency information", $this->file->getFilename());
		
		// get dependencies
		foreach($this->filePackageArray['dependencies'] as $dependency) {
			// validate
			if (!isset($depdendency['packageIdentifier'])) throw new PackageFileException("Cannot read IPF file %s: Corrupt dependency information", $this->file->getFilename());
			
			// add
			$this->dependencies[] = array(
				'packageIdentifier'		=> $dependency['packageIdentifier'],
				'minimalVersion'		=> (isset($dependency['minimalVersion']) ? $dependency['minimalVersion'] : null),
				'maximalVersion'		=> (isset($dependency['maximalVersion']) ? $dependency['maximalVersion'] : null),
				'file'				=> (isset($dependency['file']) ? $dependency['file'] : null)
			);
		}
	}
	
	/**
	 * Reads package excludes from file
	 * @return		void
	 * @throws		PackageFileException
	 */
	protected function readPackageExcludes() {
		// stop here if no excludes are available
		if (!isset($this->filePackageArray['excludes'])) return;
		
		// validate
		if (!is_array($this->filePackageArray['excludes']) or !count($this->filePackageArray['excludes'])) throw new PackageFileException("Cannot read IPF file %s: Corrupt exclude information!", $this->file->getFilename());
		
		// get excludes
		foreach($this->filePackageArray['excludes'] as $exclude) {
			// validate
			if (!isset($exclude['packageIdentifier'])) throw new PackageFileException("Cannot read IPF file %s: Missing exclude information!", $this->file->getFilename());
			
			// add
			$this->excludes[] = array(
				'packageIdentifier'		=>	$exclude['packageIdentifier'],
				'version'			=>	(isset($exclude['version']) ? $exclude['version'] : null),
				'startVersion'			=>	(isset($exclude['startVersion']) ? $exclude['startVersion'] : null),
				'endVersion'			=>	(isset($exclude['endVersion']) ? $exclude['endVersion'] : null)
			);
		}
	}
	
	/**
	 * Reads package information
	 * @return		void
	 * @throws		PackageFileException
	 */
	protected function readPackageInformation() {
		// validate information
		if (!isset($this->filePackageArray['information'])) throw new PackageFileException("Cannot read IPF file %s: No package information found!", $this->file->getFilename());
		if (count(array_diff(array('packageIdentifier', 'packageName', 'packageDescription', 'version'), array_keys($this->filePackageArray['information'])))) throw new PackageFileException("Cannot read IPF file %s: Missing package information!", $this->file->getFilename());
		
		// get needed information
		$this->packageIdentifier = StringUtil::trim($this->filePackageArray['information']['packageIdentifier']);
		$this->packageName = $this->filePackageArray['information']['packageName'];
		$this->packageDescription = $this->filePackageArray['information']['packageDescription'];
		$this->version = $this->filePackageArray['information']['version'];
		
		// handle file errors
		if (!is_array($this->packageDescription) or !is_array($this->packageName)) throw new PackageFileException("Cannot read IPF file %s: Corrupt package information detected!", $this->file->getFilename());
		
		// get optional information
		if (isset($this->filePackageArray['information']['standalone'])) $this->standalone = (bool) $this->filePackageArray['information']['standalone'];
		if (isset($this->filePackageArray['information']['plugin'])) $this->plugin = StringUtil::trim($this->filePackageArray['information']['plugin']);
		if (isset($this->filePackageArray['information']['projectStartDate'])) $this->projectStartDate = $this->filePackageArray['information']['projectStartDate'];
		if (isset($this->filePackageArray['information']['date'])) $this->date = $this->filePackageArray['information']['date'];
		if (isset($this->filePackageArray['information']['packageUrl'][0])) $this->packageUrl = $this->filePackageArray['information']['packageUrl'][0];
		if (isset($this->filePackageArray['information']['packageUrl'][1])) $this->packageUrlAlias = $this->filePackageArray['information']['packageUrl'][1];
		if (isset($this->filePackageArray['information']['documentationUrl'])) $this->documentationUrl = $this->filePackageArray['information']['documentationUrl'];
		if (isset($this->filePackageArray['information']['supportUrl'])) $this->supportUrl = $this->filePackageArray['information']['supportUrl'];
		if (isset($this->filePackageArray['information']['provides'])) $this->provides = $this->filePackageArray['information']['provides'];
		
		// validate author information
		if (!isset($this->filePackageArray['information']['authorName'][0])) throw new PackageFileException("Cannot read IPF file %s: Corrupt author information detected!", $this->file->getFilename());

		// read author information
		$this->authorName = $this->filePackageArray['information']['authorName'][0];
		if (isset($this->filePackageArray['information']['authorName'][1])) $this->authorAlias = $this->filePackageArray['information']['authorName'][1];
		if (isset($this->filePackageArray['information']['authorUrl'])) $this->authorUrl = $this->filePackageArray['information']['authorUrl'];
		
		// license information
		if (isset($this->filePackageArray['licenseInformation'])) {
			// validate information
			if (!isset($this->filePackageArray['licenseInformation']['licenseName']) or !isset($this->filePackageArray['licenseInformation']['licenseUrl'])) throw new PackageFileException("Cannot read IPF file %s: Corrupt license information detected!", $this->file->getFilename());
			
			// read license information
			$this->licenseName = $this->filePackageArray['licenseInformation']['licenseName'];
			$this->licenseUrl = $this->filePackageArray['licenseInformation']['licenseUrl'];
		}
	}
	
	/**
	 * Reads package installations information
	 * @return		void
	 * @throws		PackageFileException
	 */
	protected function readPackageInstallations() {
		// stop if no installations are required
		if (!isset($this->filePackageArray['installs'])) return;
		
		// validate
		if (!is_array($this->filePackageArray['installs']) or !count($this->filePackageArray['installs'])) throw new PackageFileException("Cannot read IPF file %s: Corrupt package installation information!", $this->file->getFilename());
		
		// parste installations
		foreach($this->filePackageArray['installs'] as $installation) {
			// validate
			if (!isset($installation['file']) or !isset($installation['packageIdentifier'])) throw new PackageFileException("Cannot read IPF file %s: Corrupt installation information!", $this->file->getFilename());
			
			// add
			$this->installations[] = array(
				'file'				=>	$installation['file'],
				'packageIdentifier'		=>	$installation['packageIdentifier']
			);
		}
	}
	
	/**
	 * Reads package instructions
	 * @return		void
	 * @throws		PackageFileException
	 */
	protected function readPackageInstructions() {
		// validate
		if (!isset($this->filePackageArray['instructions']) or !is_array($this->filePackageArray['instructions']) or !count($this->filePackageArray['instructions'])) throw new PackageFileException("Cannot read IPF file %s: Corrupt instruction information!", $this->file->getFilename());
		
		// parse instructions
		foreach($this->filePackageArray['instructions'] as $instructionBlock) {
			// validate
			if (!isset($instructionBlock['type']) or ($instructionBlock['type'] == 'update' and !isset($instructionBlock['fromVersion']))) throw new PackageFileException("Cannot read IPF file %s: Corrupt instructions!", $this->file->getFilename());
			if ($instructionBlock['type'] == 'install' and count($this->instructions['install'])) throw new PackageFileException("Cannot read IPF file %s: There are two or more install instruction blocks. I don't know wich i should use. To hard question for me, sorry.", $this->file->getFilename()); // !!!EASTEREGG!!!
			if ($instructionBlock['type'] == 'update' and isset($this->instructions['update'][$instructionBlock['fromVersion']])) throw new PackageFileException("Cannot read IPF file %s: There are two or more update instruction blocks for the same version.", $this->file->getFilename());
			
			// create array if needed
			if ($instructionBlock['type'] == 'update') $this->instructions['update'][$instructionBlock['fromVersion']] = array();
			
			// add actions
			foreach($instructionBlock['actions'] as $action) {
				// validate
				if (!isset($action['type']) or !isset($action['argument'])) throw new PackageFileException("Cannot read IPF file %s: Corrupt action information!", $this->file->getFilename());
				
				// go ...
				if ($instructionBlock['type'] == 'install')
					$this->instructions['install'][] = array(
						'action'	=>	$action['type'],
						'argument'	=>	$action['argument']
					);
				else
					$this->instructions['update'][$instructionBlock['fromVersion']][] = array(
						'action'	=>	$action['type'],
						'argument'	=>	$action['argument']
					);
			}
		}
	}
	
	/**
	 * Reads all system requirements
	 * @return		void
	 * @throws		PackageFileException
	 */
	protected function readPacakgeSystemRequirements() {
		// stop if no system requirements set
		if (!isset($this->filePackageArray['systemRequirements'])) return;
		
		// validate
		if (!is_array($this->filePackageArray['systemRequirements']) or !count($this->filePackageArray['systemRequirements'])) throw new PackageFileException("Cannot read IPF file %s: Corrupt system requirement information!", $this->file->getFilename());
		
		// loop throug requirements
		foreach($this->filePackageArray['systemRequirements'] as $requirement) {
			// validate
			if (!isset($requirement['type']) or !isset($requirement['what'])) throw new PackageFileException("Cannot read IPF file %s: Corrupt requirement information!", $this->file->getFilename());
			
			// add
			$this->systemRequirements[] = array(
				'type'		=>	$requirement['type'],
				'what'		=>	$requirement['what']
			);
		}
	}
	
	/**
	 * I'm to lazy to implement getter methods ...
	 * @param		string		$method
	 * @param		array		$arguments
	 * @return		mixed
	 * @throws		SystemException
	 */
	public function __call($method, $arguments) {
		if (substr($method, 0, 3) == 'get' and strlen($method) > 3) {
			$variable = StringUtil::toLowerCase($method{4}).StringUtil::substring($method, 4);
			if (property_exists($this, $variable)) return $this->{$variable};
		}
		
		throw new SystemException("Method '%s' does not exist in class %s", $method, get_class($this));
	}
}
?>