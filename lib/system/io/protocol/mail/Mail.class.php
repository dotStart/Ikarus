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
namespace ikarus\system\io\mail;

use ikarus\system\format\Date;
use ikarus\system\io\protocol\mime\Header;
use ikarus\system\io\protocol\mime\UserAgent;

/**
 * Represents a mail.
 * @author                    Johannes Donath
 * @copyright                 © Copyright 2012 Evil-Co.de <http://www.evil-co.com>
 * @package                   de.ikarus-framework.core
 * @subpackage                system
 * @category                  Ikarus Framework
 * @license                   GNU Lesser Public License <http://www.gnu.org/licenses/lgpl.txt>
 * @version                   2.0.0-0001
 */
class Mail {

	/**
	 * Defaul precedence value.
	 * @var                        integer
	 */
	const PRECEDENCE_NONE = -1;

	/**
	 * Precedence value for "bulk" mails.
	 * @var                        integer
	 */
	const PRECEDENCE_BULK = 0;

	/**
	 * Precedence value for "junk" mails.
	 * @var                        integer
	 */
	const PRECEDENCE_JUNK = 1;

	/**
	 * Precedence value for "list" mails.
	 * @var                        integer
	 */
	const PRECEDENCE_LIST = 2;

	/**
	 * Highest priority (1).
	 * @var                        string
	 */
	const PRIORITY_HIGHEST = 1;

	/**
	 * High priority (2).
	 * @var                        string
	 */
	const PRIORITY_HIGH = 2;

	/**
	 * Normal priority (3).
	 * @var                        string
	 */
	const PRIORITY_NORMAL = 3;

	/**
	 * Low priority (4).
	 * @var                        string
	 */
	const PRIORITY_LOW = 4;

	/**
	 * Lowest priority (5).
	 * @var                        string
	 */
	const PRIORITY_LOWEST = 5;

	/**
	 * Stores a list of additional headers.
	 * @var                        ikarus\system\io\protocol\mime\MimeHeader[]
	 */
	protected $additionalHeaders = array ();

	/**
	 * Indicates whether this mail should be marked as automatic mail.
	 * @var                        boolean
	 */
	protected $autoSubmitted = true;

	/**
	 * Stores a list of bcc recipients.
	 * @var                        ikarus\system\io\protocol\mail\Contact[]
	 */
	protected $blindCarbonCopyAddresses = array ();

	/**
	 * Stores a list of cc recipients.
	 * @var                        ikarus\system\io\protocol\mail\contact[]
	 */
	protected $carbonCopyAddresses = array ();

	/**
	 * Stores the charset.
	 * @var                        string
	 */
	protected $charset = 'UTF-8';

	/**
	 * Stores the content.
	 * @var                        Content
	 */
	protected $content = '';

	/**
	 * Stores information about the content's language.
	 * @var                        string
	 */
	protected $contentLanguage = null;

	/**
	 * Stores the content type.
	 * @var                        string
	 */
	protected $contentType = 'text/plain';

	/**
	 * Stores the timestamp for this mail.
	 * @var                        ikarus\system\format\Date
	 */
	protected $date = null;

	/**
	 * Stores the sender's name.
	 * @var                        ikarus\system\io\protocol\mail\Contact
	 */
	protected $from = null;

	/**
	 * Stores the X-Mailer header string.
	 * @var                        ikarus\system\io\protocol\mime\UserAgent
	 */
	protected $mailer = null;

	/**
	 * Stores the precedence type.
	 * @var                        integer
	 */
	protected $precedence = null;

	/**
	 * Stores the priority.
	 * @var                        string
	 */
	protected $priority = null;

	/**
	 * Stores the reply-to contact.
	 * @var                        ikarus\system\io\protocol\mail\Contact
	 */
	protected $replyTo = null;

	/**
	 * Stores the sender contact.
	 * @var                        ikarus\system\io\protocol\mail\Contact
	 */
	protected $sender = null;

	/**
	 * Stores the subject.
	 * @var                        string
	 */
	protected $subject = '';

	/**
	 * Stores the target's name.
	 * @var                        ikarus\system\io\protocol\mail\Contact
	 */
	protected $to = null;

	/**
	 * Constructs the object.
	 */
	public function __construct () {
		$this->reset ();
	}

	/**
	 * Builds all headers.
	 * @return                        string
	 */
	public function buildHeaders () {
		$header = 'From: ' . $this->from->__toString () . "\r\n";
		$header .= 'To: ' . $this->to->__toString () . "\r\n";

		// build Cc part
		foreach ($this->carbonCopyAddresses as $contact) {
			$header .= 'Cc: ' . $contact->__toString () . "\r\n";
		}

		$header .= 'MIME-Version: 1.0' . "\r\n";
		if (!$this->content->hasAttachments ()) {
			$header .= 'Content-Type: ' . $this->contentType . '; charset=' . $this->charset . "\r\n";
		} else {
			$header .= 'Content-Type: multipart/mixed; boundary=' . $this->content->getBoundary () . "\r\n";
		}
		$header .= 'Content-Language: ' . $this->contentLanguage . "\r\n";
		$header .= 'Date: ' . $this->date->format ('%a, %d %b %G %H:%M:%S %z') . "\r\n";
		$header .= 'Subject: ' . $this->subject . "\r\n";
		$header .= 'User-Agent: ' . $this->mailer->__toString () . "\r\n";
		$header .= 'X-Mailer: ' . $this->mailer->__toString () . "\r\n";

		// add precedence
		if ($this->getPrecedenceString () !== null) $header .= 'Precedence: ' . $this->getPrecedenceString () . "\r\n";

		$header .= 'X-Priority: ' . $this->getPriorityString () . "\r\n";
		$header .= 'Reply-To: ' . $this->replyTo->__toString () . "\r\n";

		// add sender
		if ($this->sender !== null) $header .= 'Sender: ' . $this->sender->__toString () . "\r\n";

		// add additional headers
		foreach ($this->additionalHeaders as $header) {
			$header .= $header->__toString () . "\r\n";
		}

		return $header;
	}

	/**
	 * Adds an additional header to the list.
	 * @param                        Header $header
	 * @return                        void
	 */
	public function addAdditionalHeader (Header $header) {
		$this->additionalHeaders[] = $header;
	}

	/**
	 * Adds a contact to the blind carbon copy contact list.
	 * @param                        Contact $contact
	 * @return                        void
	 */
	public function addBlindCarbonCopyContact (Contact $contact) {
		$this->blindCarbonCopyAddresses[] = $contact;
	}

	/**
	 * Adds a contact to the carbon copy contact list.
	 * @param                        Contact $contact
	 * @return                        void
	 */
	public function addCarbonCopyContact (Contact $contact) {
		$this->carbonCopyAddresses[] = $contact;
	}

	/**
	 * Returns the current set charset.
	 * @return                        string
	 */
	public function getCharset () {
		return $this->charset;
	}

	/**
	 * Returns the current content.
	 * @return                        ikarus\system\io\mail\Content
	 */
	public function getContent () {
		return $this->content;
	}

	/**
	 * Returns the current set content language.
	 * @return                        string
	 */
	public function getContentLanguage () {
		return $this->contentLanguage;
	}

	/**
	 * Returns the current set content type.
	 * @return                        string
	 */
	public function getContentType () {
		return $this->contentType;
	}

	/**
	 * Returns the current set date header.
	 * @return                        ikarus\system\format\Date
	 */
	public function getDate () {
		return $this->date;
	}

	/**
	 * Returns the current set from header.
	 * @return \ikarus\system\io\protocol\mail\Contact
	 */
	public function getFrom () {
		return $this->from;
	}

	/**
	 * Returns the current mailer agent string.
	 * @return                        ikarus\system\io\protocol\mime\UserAgent
	 */
	public function getMailer () {
		return $this->mailer;
	}

	/**
	 * Returns the current precedence setting.
	 * @return                        integer
	 */
	public function getPrecedence () {
		return $this->precedence;
	}

	/**
	 * Returns the precedence string.
	 * @return                        string|NULL
	 */
	public function getPrecedenceString () {
		switch ($this->precedence) {
			case static::PRECEDENCE_BULK:
				return 'bulk';
			case static::PRECEDENCE_JUNK:
				return 'junk';
			case static::PRECEDENCE_LIST:
				return 'list';
			default:
				return null;
		}
	}

	/**
	 * Returns the current priority setting.
	 * @return                        string
	 */
	public function getPriority () {
		return $this->priority;
	}

	/**
	 * Returns the current priority as header string.
	 * @return                        string
	 */
	public function getPriorityString () {
		switch ($this->priority) {
			case static::PRIORITY_HIGHEST:
				return '1 (Highest)';
			case static::PRIORITY_HIGH:
				return '2 (High)';
			case static::PRIORITY_LOW:
				return '4 (Low)';
			case static::PRIORITY_LOWEST:
				return '5 (Lowest)';
			default:
				return '3 (Normal)';
		}
	}

	/**
	 * Returns the current reply-to header.
	 * @return                        ikarus\system\io\protocol\mail\Contact
	 */
	public function getReplyTo () {
		return $this->replyTo;
	}

	/**
	 * Returns the current sender header.
	 * @return                        ikarus\system\io\protocol\mail\Contact
	 */
	public function getSender () {
		return $this->sender;
	}

	/**
	 * Returns the current subject string.
	 * @return                        string
	 */
	public function getSubject () {
		return $this->subject;
	}

	/**
	 * Returns the current recipient contact.
	 * @return                        ikarus\system\io\protocol\mail\Contact
	 */
	public function getTo () {
		return $this->to;
	}

	/**
	 * Sets a new charset.
	 * @param                        string $charset
	 * @return                        void
	 */
	public function setCharset ($charset) {
		$this->charset = $charset;
	}

	/**
	 * Sets a new content.
	 * @param                        Content $content
	 * @return                        void
	 */
	public function setContent (Content $content) {
		$this->content = $content;
	}

	/**
	 * Sets a new content language.
	 * @param                        string $language
	 * @return                        void
	 */
	public function setContentLanguage ($language) {
		$this->contentLanguage = $language;
	}

	/**
	 * Sets a new content type.
	 * @param                        string $type
	 * @return                        void
	 */
	public function setContentType ($type) {
		$this->contentType = $type;
	}

	/**
	 * Sets a new date.
	 * @param                        Date $date
	 * @return                        void
	 */
	public function setDate (Date $date) {
		$this->date = $date;
	}

	/**
	 * Sets a new from header.
	 * @param                        Contact $from
	 * @return                        void
	 */
	public function setFrom (Contact $from) {
		$this->from = $from;
	}

	/**
	 * Sets a new mailer user agent.
	 * @param                        UserAgent $agent
	 * @return                        void
	 */
	public function setMailer (UserAgent $agent) {
		$this->mailer = $agent;
	}

	/**
	 * Sets a new precendence value.
	 * @param                        integer $precedence
	 * @return                        void
	 */
	public function setPrecedence ($precedence) {
		$this->precedence = $precedence;
	}

	/**
	 * Sets a new priority.
	 * @param                        integer $priority
	 * @return                        void
	 */
	public function setPriority ($priority) {
		$this->priority = $priority;
	}

	/**
	 * Sets a new reply-to contact.
	 * @param                        Contact $contact
	 * @return                        void
	 */
	public function setReplyTo (Contact $contact) {
		$this->replyTo = $contact;
	}

	/**
	 * Sets a new sender contact.
	 * @param                        Contact $contact
	 * @return                        void
	 */
	public function setSender (Contact $contact) {
		$this->sender = $contact;
	}

	/**
	 * Sets a new subject.
	 * @param                        string $subject
	 * @return                        void
	 */
	public function setSubject ($subject) {
		$this->subject = $subject;
	}

	/**
	 * Sets a new to contact.
	 * @param                        Contact $contact
	 * @return                        void
	 */
	public function setTo (Contact $contact) {
		$this->contact = $contact;
	}

	/**
	 * Returns the mail as raw string.
	 * @return                        string
	 */
	public function __toString () {
		return $this->buildHeaders () . "\r\n" . $this->getContet ();
	}
}

?>