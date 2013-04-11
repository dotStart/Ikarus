<?php
/**
 * This file is part of the Ikarus Framework.
 *
 * The Ikarus Framework is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Lesser General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * The Ikarus Framework is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU Lesser General Public License for more details.
 *
 * You should have received a copy of the GNU Lesser General Public License
 * along with the Ikarus Framework. If not, see <http://www.gnu.org/licenses/>.
 */
namespace ikarus\system\io\protocol\mail;
use ikarus\util\StringUtil;

/**
 * Stores the content of a mail.
 * @author		Johannes Donath
 * @copyright		© Copyright 2012 Evil-Co.de <http://www.evil-co.com>
 * @package		de.ikarus-framework.core
 * @subpackage		system
 * @category		Ikarus Framework
 * @license		GNU Lesser Public License <http://www.gnu.org/licenses/lgpl.txt>
 * @version		2.0.0-0001
 */
class Content {
	
	/**
	 * Stores a prefix for boundaries.
	 * @var			string
	 */
	const BOUNDARY_PREFIX = '----=_Part_';
	
	/**
	 * Stores a list of attachments.
	 * @var			Attachment[]
	 */
	protected $attachments = array();
	
	/**
	 * Stores a boundary which is used to seperate all parts from each other.
	 * @var			string
	 */
	protected $boundary = null;
	
	/**
	 * Stores a message.
	 * @var			string
	 */
	protected $message = '';
	
	/**
	 * Stores the parent mail.
	 * @var			Mail
	 */
	protected $parent = null;
	
	/**
	 * Constructs the object.
	 * @param			string			$message
	 */
	public function __construct(Mail $parent, $message = '') {
		$this->parent = $parent;
		$this->message = $message;
	}
	
	/**
	 * Adds a new attachment.
	 * @param			Attachment			$attachment
	 * @return			void
	 */
	public function addAttachment(Attachment $attachment) {
		$this->attachments[] = $attachment;
	}
	
	/**
	 * Builds a content string.
	 * @return			string
	 */
	public function build() {
		$buffer = '';
		
		// add first part (if needed)
		if ($this->hasAttachments()) {
			$buffer .= '--'.$this->getBoundary()."\r\n";
			$buffer .= 'Content-Type: '.$this->parent->getContentType().'; charset='.$this->parent->getCharset()."\r\n";
			$buffer .= "\r\n";
		}
		
		// add message
		$buffer .= $this->message;
		
		// add attachments
		foreach($this->attachments as $attachment) {
			$buffer .= '--'.$this->getBoundary()."\r\n";
			$buffer .= 'Content-Type: '.$attachment->getContentType();
			$buffer .= 'Content-Transfer-Encoding: base64'."\r\n";
			$buffer .= "\r\n";
			
			$buffer .= base64_encode($attachment->getContent());
			$buffer .= "\r\n";
		}
		
		// append end boundary if needed
		if ($this->hasAttachments()) $buffer .= '--'.$this->getBoundary().'--';
	}
	
	/**
	 * Returns a list of attachments.
	 * @return			ikarus\system\io\protocol\mail\Attachment[]
	 */
	public function getAttachments() {
		return $this->attachments;
	}
	
	/**
	 * Returns the boundary which is used to seperate all parts.
	 * @return			string
	 */
	public function getBoundary() {
		if ($this->boundary === null) $this->boundary = static::BOUNDARY_PREFIX.StringUtil::getRandomID();
		return $this->boundary;
	}
	
	/**
	 * Returns the message.
	 * @return			string
	 */
	public function getMessage() {
		return $this->message;
	}
	
	/**
	 * Indicates whether this content has at least one attachment.
	 * @return			boolean
	 */
	public function hasAttachments() {
		return (count($this->attachments) > 0);
	}
	
	/**
	 * sets a new message.
	 * @param			string			$message
	 * @return			void
	 */
	public function setMessage($message) {
		$this->message = $message;
	}
	
	/**
	 * Returns the content as string.
	 * @return			string
	 */
	public function __toString() {
		return $this->build();
	}
}
?>