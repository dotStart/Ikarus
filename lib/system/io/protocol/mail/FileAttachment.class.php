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
namespace ikarus\system\io\protocol\mail;

use ikarus\system\io\adapter\IFilesystemAdapter;
use ikarus\util\FileUtil;

/**
 * Allows to use a simple file as attachment.
 * @author                    Johannes Donath
 * @copyright                 © Copyright 2012 Evil-Co.de <http://www.evil-co.com>
 * @package                   de.ikarus-framework.core
 * @subpackage                system
 * @category                  Ikarus Framework
 * @license                   GNU Lesser Public License <http://www.gnu.org/licenses/lgpl.txt>
 * @version                   2.0.0-0001
 */
class FileAttachment implements Attachment {

	/**
	 * Stores the parent filesystem adapter.
	 * @var                        IFilesystemAdapter
	 */
	protected $adapter = null;

	/**
	 * Stores a cached version of the file's content.
	 * @var                        string
	 */
	protected $cachedContent = null;

	/**
	 * Stores a cached version of the file's size.
	 * @var                        integer
	 */
	protected $cachedFileSize = null;

	/**
	 * Stores the file's content type.
	 * @var                        string
	 */
	protected $contentType = null;

	/**
	 * Stores the file's name.
	 * @var                        string
	 */
	protected $filename = null;

	/**
	 * Stores the file's path.
	 * @var                        string
	 */
	protected $path = null;

	/**
	 * Constructs the object.
	 * @param                        IFilesystemAdapter $adapter
	 * @param                        string             $path
	 * @param                        string             $contentType
	 * @param                        string             $filename
	 */
	public function __construct (IFilesystemAdapter $adapter, $path, $contentType = null, $filename = null) {
		$this->adapter = $adapter;
		$this->path = $path;
		$this->filename = $filename;

		if ($contentType !== null) {
			$this->contentType = $contentType;
		} else {
			$this->guessContentType ();
		}
	}

	/**
	 * (non-PHPdoc)
	 * @see \ikarus\system\io\protocol\mail\Attachment::getContent()
	 */
	public function getContent () {
		if ($this->cachedContent === null) $this->cachedContent = $this->adapter->readFileContents ($this->path);

		return $this->cachedContent;
	}

	/**
	 * (non-PHPdoc)
	 * @see \ikarus\system\io\protocol\mail\Attachment::getContentType()
	 */
	public function getContentType () {
		return $this->contentType;
	}

	/**
	 * (non-PHPdoc)
	 * @see \ikarus\system\io\protocol\mail\Attachment::getFilename()
	 */
	public function getFilename () {
		if ($this->filename !== null) return $this->filename;

		return FileUtil::getFilename ($this->path);
	}

	/**
	 * (non-PHPdoc)
	 * @see \ikarus\system\io\protocol\mail\Attachment::getSize()
	 */
	public function getSize () {
		if ($this->cachedFileSize === null) $this->cachedFileSize = $this->adapter->getFilesize ($this->path);

		return $this->cachedFileSize;
	}

	/**
	 * Tries to guess the correct content type.
	 * @return                        void
	 */
	protected function guessContentType () {
		$this->contentType = MimeRegister::guessType ($this->path, $this->getContent ());
	}
}

?>