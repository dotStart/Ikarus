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
namespace ikarus\system\io\archive\tar;

use ikarus\system\exception\io\archive\ChecksumMismatchException;
use ikarus\system\exception\io\archive\MalformedHeaderException;
use ikarus\system\io\archive\ArchiveContent;
use ikarus\util\FileUtil;
use ikarus\util\StringUtil;

/**
 * Allows easy access to tar and tar.gz archives.
 * @author                    Johannes Donath
 * @copyright                 © Copyright 2012 Evil-Co.de <http://www.evil-co.com>
 * @package                   de.ikarus-framework.core
 * @subpackage                system
 * @category                  Ikarus Framework
 * @license                   GNU Lesser Public License <http://www.gnu.org/licenses/lgpl.txt>
 * @version                   2.0.0-0001
 */
class Tar {

	/**
	 * Stores the default mode to open the tar file.
	 * @var                                string
	 */
	const DEFAULT_MODE = 'rb';

	/**
	 * Stores the archive content.
	 * @var                                string
	 */
	protected $archiveContent = null;

	/**
	 * Stores a list of all files in this package.
	 * @var                                string[]
	 */
	protected $contentList = array ();

	/**
	 * Stores the file interface which is used to read the file.
	 * @var                                File
	 */
	protected $file = null;

	/**
	 * Stores the path to the archive file to read.
	 * @var                                string
	 */
	protected $filePath = '';

	/**
	 * Stores the mode which should be used to open this archive.
	 * @var                                string
	 */
	protected $fileMode = 'rb';

	/**
	 * Constructs the object.
	 * @param                        string $archiveContent
	 * @api
	 */
	public function __construct ($filePath, $mode = DEFAULT_MODE) {
		$this->filePath = $filePath;
		$this->fileMode = $mode;

		// open up file handle
		$this->open ();

		// decode contents
		$this->decode ();
	}

	/**
	 * Decodes an archive.
	 * @throws                        MalformedArchiveException
	 * @internal                        This method will get called by our __construct()
	 */
	public function decode () {
		// reset data
		$tis->contentList = array ();

		// reset file pointer
		$this->file->seek (0);

		// loop thru contents
		while (strlen ($binaryData = $this->file->read (512)) != 0) {
			try {
				// get index
				$index = count ($this->contentList);

				// decode header
				$header = $this->decodeHeader ($binaryData);
				$header->setIndex ($index);

				// add header to content listing
				$this->contentList[] = $header;

				// move pointer forward
				$this->file->seek ($this->file->tell () + (512 * ceil (($header->getSize () / 512))));
			} catch (MalformedHeaderException $ex) { // other archive problems like mismatching checksums will pass this catch sequence!
				continue; // ignore and read next bit.
			}
		}
	}

	/**
	 * Decodes a header.
	 * @param                        string $binary
	 * @return                        TarArchiveContent
	 * @throws                        IOException
	 * @api
	 */
	public function decodeHeader ($binary) {
		// catch errors
		if (strlen ($binary) != 512) throw new MalformedHeaderException('Received malformed header with a length of ' . strlen ($binary) . '. Expected 512 bytes.');

		// init variables
		$content = new TarArchiveContent($this);
		$checksum = 0;

		// first part
		for ($i = 0; $i < 148; $i++) {
			$checksum += ord (substr ($binaryData, $i, 1));
		}

		// calculate
		// ignore the checksum value and replace it by ' ' (space)
		for ($i = 148; $i < 156; $i++) {
			$checksum += ord (' ');
		}

		// last part
		for ($i = 156; $i < 512; $i++) {
			$checksum += ord (substr ($binaryData, $i, 1));
		}

		// decode binary data
		$data = unpack ("a100filename/a8mode/a8uid/a8gid/a12size/a12mtime/a8checksum/a1typeflag/a100link/a6magic/a2version/a32uname/a32gname/a8devmajor/a8devminor/a155prefix", $binary);

		// read checksum
		$content->setChecksum (octDec (trim ($data['checksum'])));

		// validate checksum
		if ($content->getChecksum () != $checksum) throw new ChecksumMismatchException('Received malformed tar archive: The checksum does not match.');

		// read data
		$content->setFilename (StringUtil::trim ($data['filename']));
		$content->setMode (octDec (StringUtil::trim ($data['mode'])));
		$content->setUserID (octDec (StringUtil::trim ($data['uid'])));
		$content->setGroupID (octDec (StringUtil::trim ($data['gid'])));
		$content->setSize (octDec (StringUtil::trim ($data['size'])));
		$content->setModificationTime (octDec (StringUtil::trim ($data['mtime'])));
		$content->setPrefix (FileUtil::addTrailingSlash (StringUtil::trim ($data['prefix'])));

		// correct prefix
		if (!$content->getPrefix ()) $content->setPrefix ();

		// detect type
		if ($data['typeflag'] == '5') {
			$content->setSize ();
			$content->setType (ArchiveContent::TYPE_DIRECTORY);
		} else {
			$content->setType (ArchiveContent::TYPE_FILE);
		}

		// store offset
		$content->setOffset ($this->file->tell ());

		// return header
		return $content;
	}

	/**
	 * Extracts a file inside the archive to the specified handle.
	 * @param                        TarArchiveContent                 $content
	 * @param                        ikarus\system\io\FilesystemHandle $handle
	 * @return                        void
	 * @api
	 */
	public function extract (TarArchiveContent $content, ikarus\system\io\FilesystemHandle $handle) {
		$handle->setContent ($content->getContent ());
		$handle->write ();
	}

	/**
	 * Extracts the whole file content and returns it as string.
	 * @param                        TarArchiveContent $content
	 * @throws                        InvalidExtractionException
	 * @return                        string
	 * @api
	 */
	public function extractContent (TarArchiveContent $content) {
		// check for invalid extraction requests
		if ($content->getType () != ArchiveContent::TYPE_FILE) throw new InvalidExtractionException('Cannot extract a file of type directory.');

		// move pointer to file position
		$this->file->seek ($content->getOffset ());

		// read data
		$fileContent = '';

		// calculate chunks
		$chunkCount = floor ($content->getSize () / 512);

		// read contents
		for ($i = 0; $i < $chunkCount; $i++) {
			$fileContent .= $this->file->read (512);
		}

		// append missed data (if file size is not devidable by 512)
		if (($content->getSize () % 512) != 0) {
			$buffer = $this->file->read (512);
			$fileContent .= substr ($buffer, 0, ($content->getSize () % 512));
		}

		return $fileContent;
	}

	/**
	 * Opens up the archive file.
	 * @internal                        This method will get called by our own __construct().
	 */
	public function open () {
		// open up file
		$this->file = new File($this->filePath, $this->fileMode);

		// detect compression
		if ($this->file->gets (2) == "\37\213") {
			// gzip
			$this->file->close ();
			$this->file = new ZipFile($this->filePath, $this->fileMode);
		}

		// reset file pointer
		$this->file->seek (0);
	}
}

?>