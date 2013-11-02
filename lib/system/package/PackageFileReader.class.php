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
namespace ikarus\system\package;
use ikarus\system\event\ExceptionEventArguments;
use ikarus\system\event\package\PackageContentsReadEvent;
use ikarus\system\event\package\PackageContentVerificationFailedEvent;
use ikarus\system\event\package\PackageContentVerificationSucceededEvent;
use ikarus\system\event\package\PackageDecompressionFailedEvent;
use ikarus\system\event\package\PackageFileReaderArguments;
use ikarus\system\event\package\PackageVerificationFailedEvent;
use ikarus\system\event\package\PackageVerificationSucceededEvent;
use ikarus\system\exception\package\ContentVerificationFailedException;
use ikarus\system\exception\package\UnsupportedPackageVersionException;
use ikarus\system\exception\package\WrongMagicStringException;
use ikarus\system\Ikarus;
use ikarus\system\io\archive\tar\Tar;
use ikarus\util\CompressionUtil;
use ikarus\util\FileUtil;

/**
 * Reads ikarus package files
 * @author                    Johannes Donath
 * @copyright                 2011 Evil-Co.de
 * @package                   de.ikarus-framework.core
 * @subpackage                system
 * @category                  Ikarus Framework
 * @license                   GNU Lesser Public License <http://www.gnu.org/licenses/lgpl.txt>
 * @version                   2.0.0-0001
 */
class PackageFileReader {

	/**
	 * Contains the magic number for our file format
	 * @var                        string
	 */
	const MAGIC_NUMBER = '%IPF';

	/**
	 * Contains the current file version.
	 * @var                        string
	 */
	const FILE_VERSION = '2.0.0';

	/**
	 * Contains the complete path to package file.
	 * @var                        string
	 */
	protected $fileName = null;

	/**
	 * Contains decompressed file contents.
	 * @var                        string
	 */
	protected $fileContents = null;

	/**
	 * Contains package file's version string.
	 * @var                        string
	 */
	protected $fileVersion = '2.0.0';

	/**
	 * Contains decoded json string.
	 * @var                        PackageInformation
	 */
	protected $package = null;

	/**
	 * Stores the file part of a package.
	 * @var                        string
	 */
	protected $packageContent = null;

	/**
	 * Contains unprocessed file contents.
	 * @var                        string
	 */
	protected $rawFileContents = null;

	/**
	 * Contains a list of supported file versions.
	 * @var                        string
	 */
	protected static final $supportedFileVersions = array(static::FILE_VERSION);

	/**
	 * Constructs the object.
	 * @param                        string $fileName
	 */
	public function __construct ($fileName) {
		$this->fileName = $fileName;
		$this->rawFileContents = Ikarus::getFilesystemManager ()->getDefaultAdapter ()->readFileContents ($fileName);

		// fire event
		Ikarus::getEventManager ()->fire (new PackageContentsReadEvent(new PackageFileReaderEventArguments($this)));

		// parse file
		$this->verifyPackage ();
		$this->extractInformation ();

		// validate contents
		$this->verifyContents ();
	}

	/**
	 * Extracts all information from file.
	 * @return                        void
	 */
	protected function extractInformation () {
		// decompress file
		try {
			$this->fileContents = CompressionUtil::decompress ($this->rawFileContents);
		} catch (Exception $ex) {
			// fire event
			Ikarus::getEventManager ()->fire (new PackageDecompressionFailedEvent(new ExceptionEventArguments($ex)));

			// throw exception
			throw $ex;
		}

		// split contents
		list($packageMetadata, $packageContent) = explode (chr (0), $this->fileContents, 2);

		// decode contents
		$this->package = PackageInformation::decode ($packageMetadata);

		// store package contents
		$this->packageContent = $packageContent;
	}

	/**
	 * Returns the raw package content.
	 * @return                        string
	 */
	public function getRawPackage () {
		return $this->packageContent;
	}

	/**
	 * Returns a tar instance for the package content.
	 * @return                        ikarus\system\io\archive\tar\Tar;
	 */
	public function getContent () {
		// write contents to file
		$filename = FileUtil::getTemporaryFilename ();

		// write contents
		Ikarus::getFilesystemManager ()->getDefaultAdapter ()->createFile ($filename, $this->packageContent);

		// get tar handle
		return (new Tar($filename));
	}

	/**
	 * Verifies package contents with pre-generated checksums.
	 * @return                        void
	 * @throws                        ContentVerificationFailedException
	 */
	protected function verifyContents () {
		try {
			// check checksum
			$checksum = HashUtil::getChecksum ($this->package->getRawPackage ());
			if ($checksum != $this->package->getChecksum ()) throw new ContentVerificationFailedException('Content checksum does not match package checksum');

			// fire event
			Ikarus::getEventManager ()->fire (PackageContentVerificationSucceededEvent (new PackageFileReaderEventArguments($this)));
		} catch (Exception $ex) {
			// fire event
			Ikarus::getEventManager ()->fire (PackageContentVerificationFailedEvent (new ExceptionEventArguments($ex)));

			// throw exception
			throw $ex;
		}
	}

	/**
	 * Verifies a package file.
	 * @return                        void
	 * @throws                        WrongMagicStringException
	 * @throws                        UnsupportedPackageVersionException
	 * @throws                        Exception
	 */
	protected function verifyPackage () {
		try {
			// get magic number
			$magicNumber = substr ($this->rawFileContents, 0, (stripos ($this->rawFileContents, chr (0)) - 1));

			// extract information
			list($magicString, $packageVersion) = explode ('-', $magicNumber);

			// validate
			if ($magicString != static::MAGIC_NUMBER) throw new WrongMagicStringException('Detected wrong magic string "%s"', $magicString);
			if (!in_array ($packageVersion, static::$supportedFileVersions)) throw new UnsupportedPackageVersionException('Detected unsupported package version "%s"', $packageVersion);

			// save version
			$this->fileVersion = $packageVersion;

			// remove magicNumber from compressed information
			$this->rawFileContents = substr ($this->rawFileContents, stripos ($this->rawFileContents, chr (0)));

			// fire event
			Ikarus::getEventManager ()->fire (new PackageVerificationSucceededEvent(new PackageFileReaderEventArguments($this)));
		} catch (Exception $ex) {
			// fire event
			Ikarus::getEventManager ()->fire (new PackageVerificationFailedEvent(new ExceptionEventArguments($ex)));

			// throw exception
			throw $ex;
		}
	}
}

?>
