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

/**
 * Stores a raw (string) attachment with it's content type.
 * @author                    Johannes Donath
 * @copyright                 © Copyright 2012 Evil-Co.de <http://www.evil-co.com>
 * @package                   de.ikarus-framework.core
 * @subpackage                system
 * @category                  Ikarus Framework
 * @license                   GNU Lesser Public License <http://www.gnu.org/licenses/lgpl.txt>
 * @version                   2.0.0-0001
 */
class RawAttachment implements Attachment {

	/**
	 * Stores the attachment's content.
	 * @var                        string
	 */
	protected $content = '';

	/**
	 * Stores the attachment's content type.
	 * @var                        string
	 */
	protected $contentType = 'application/octet-stream';

	/**
	 * Stores the attachment's file name.
	 * @var                        string
	 */
	protected $filename = 'attachment';

	/**
	 * Constructs the object.
	 * @param                        string $filename
	 * @param                        string $content
	 * @param                        string $contentType
	 */
	public function __construct ($filename, $content, $contentType = null) {
		$this->content = $content;
		$this->filename = $filename;
		if ($contentType !== null) $this->contentType = $contentType;
	}

	/**
	 * (non-PHPdoc)
	 * @see \ikarus\system\io\protocol\mail\Attachment::getContent()
	 */
	public function getContent () {
		return $this->content;
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
		return $this->filename;
	}

	/**
	 * (non-PHPdoc)
	 * @see \ikarus\system\io\protocol\mail\Attachment::getSize()
	 */
	public function getSize () {
		return strlen ($this->content);
	}
}

?>