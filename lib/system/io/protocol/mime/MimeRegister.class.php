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
namespace ikarus\system\io\protocol\mime;

use ikarus\pattern\Singleton;
use ikarus\system\exception\StrictStandardException;
use ikarus\system\Ikarus;

/**
 * Stores a list of common mime types.
 * @author                    Johannes Donath
 * @copyright                 © Copyright 2012 Evil-Co.de <http://www.evil-co.com>
 * @package                   de.ikarus-framework.core
 * @subpackage                system
 * @category                  Ikarus Framework
 * @license                   GNU Lesser Public License <http://www.gnu.org/licenses/lgpl.txt>
 * @version                   2.0.0-0001
 */
class MimeRegister extends Singleton {

	/**
	 * Stores a fallback mime type.
	 * @var                        RegisteredMimeType
	 */
	protected $fallback = null;

	/**
	 * Stores a list of types.
	 * @var                        MimeType[]
	 */
	protected $types = array();

	/**
	 * (non-PHPdoc)
	 * @see                        ikarus\pattern\Singleton::init()
	 */
	public function init () {
		$this->registerDefaultTypes ();
	}

	/**
	 * Returns a mime type instance.
	 * @param                        string $category
	 * @param                        string $type
	 * @throws                        InvalidMimeTypeException
	 * @return                        ikarus\system\io\protocol\mime\MimeType
	 */
	public function getType ($category, $type = null) {
		if ($type === null) $typeString = $category; else
			$typeString = $category . MimeType::TYPE_SEPERATOR . $category;

		// validate
		if (!$this->isKnownType ($typeString)) throw new InvalidMimeTypeException('Cannot find mime type "%s" in register', $typeString);

		// ok
		return $this->types[$typeString];
	}

	/**
	 * Guesses a filetype.
	 * @param                        string $filename
	 * @param                        string $content
	 * @return                        ikarus\system\io\protocol\mime\RegisteredMimeType
	 */
	public function guessType ($filename, $content) {
		$mimeType = $this->fallback;
		if (function_exists ('mime_content_type')) $mimeType = $this->getType (mime_content_type ($filename));

		if ($mimeType == $this->fallback) {
			foreach ($this->types as $type) {
				if ($type->matchesFilename ($filename)) return $type;
			}
		}

		// TODO: Add support for content matching here (or above).

		return $mimeType;
	}

	/**
	 * Checks whether a mime type has been registered or not.
	 * @param                        MimeType $type
	 * @return                        boolean
	 */
	public function isKnownType ($typeString) {
		return (array_key_exists ($typeString, $this->types));
	}

	/**
	 * Registers common types.
	 * @return                void
	 */
	protected function registerDefaultTypes () {
		// application category
		$this->registerType (new MimeType('application', 'octet-stream'), array(), null, true); // fallback

		$this->registerType (new MimeType('application', 'atom+xml'), array('atom'));
		$this->registerType (new MimeType('application', 'excel'), array('xls', 'xlt', 'xlv', 'xlw'));
		$this->registerType (new MimeType('application', 'font-woff'), array('woff'));
		$this->registerType (new MimeType('application', 'java'), array('class')); // java bytecode
		$this->registerType (new MimeType('application', 'java-byte-code'), array(), $this->getType ('application', 'java')); // alias for java bytecode
		$this->registerType (new MimeType('application', 'json'), array('json'));
		$this->registerType (new MimeType('application', 'mac-binary'), array('bin')); // mac binary
		$this->registerType (new MimeType('application', 'macbinary'), array(), $this->getType ('application', 'mac-binary')); // alias for mac binary
		$this->registerType (new MimeType('application', 'mspowerpoint'), array('pot', 'pps', 'ppt', 'ppz'));
		$this->registerType (new MimeType('application', 'msword'), array('doc', 'word')); // msword document
		$this->registerType (new MimeType('application', 'pdf'), array('pdf'));
		$this->registerType (new MimeType('application', 'postscript'), array('eps')); // postscript document
		$this->registerType (new MimeType('application', 'rdf+xml'), array('rdf'));
		$this->registerType (new MimeType('application', 'rss+xml'), array('rss'));
		$this->registerType (new MimeType('application', 'x-bzip'), array('bzip', 'bz')); // bzip1
		$this->registerType (new MimeType('application', 'x-bzip2'), array('bzip2', 'bz2')); // bzip2
		$this->registerType (new MimeType('application', 'x-deb'), array('deb')); // debian
		$this->registerType (new MimeType('application', 'x-gzip'), array('gzip', 'gz', 'tgz'));
		$this->registerType (new MimeType('application', 'x-compressed'), array('z'));
		$this->registerType (new MimeType('application', 'x-compress'), array(), $this->getType ('application', 'x-compressed'));
		$this->registerType (new MimeType('application', 'x-java-class'), array(), $this->getType ('application', 'java'));
		$this->registerType (new MimeType('application', 'x-javascript'), array('js'));
		$this->registerType (new MimeType('application', 'x-macbinary'), array(), $this->getType ('application', 'mac-binary')); // alias for mac binary
		$this->registerType (new MimeType('application', 'x-rar-compressed'), array('rar'));
		$this->registerType (new MimeType('application', 'x-shockwave-flash'), array('swf'));
		$this->registerType (new MimeType('application', 'x-tar'), array('tar'));
		$this->registerType (new MimeType('application', 'xhtml+xml'), array('xhtml'));
		$this->registerType (new MimeType('application', 'xml-dtd'), array('dtd'));
		$this->registerType (new MimeType('application', 'x-xpinstall'), array('xpi'));
		$this->registerType (new MimeType('application', 'zip'), array('zip'));

		// audio category
		$this->registerType (new MimeType('audio', 'midi'), array('mid', 'midi')); // midi audio
		$this->registerType (new MimeType('audio', 'mpeg'), array('m2a', 'mp3')); // mpeg audio
		$this->registerType (new MimeType('audio', 'wav'), array('wav'));
		$this->registerType (new MimeType('audio', 'x-aac'), array('aac'));
		$this->registerType (new MimeType('audio', 'x-midi'), array(), $this->getType ('application', 'midi'));

		// image category
		$this->registerType (new MimeType('image', 'bmp'), array('bmp', 'bm')); // BMP file format
		$this->registerType (new MimeType('image', 'gif'), array('gif')); // gif image format
		$this->registerType (new MimeType('image', 'jpeg'), array('jpeg', 'jpg', 'jpe'));
		$this->registerType (new MimeType('image', 'png'), array('png')); // png
		$this->registerType (new MimeType('image', 'svg+xml'), array('svg')); // svg
		$this->registerType (new MimeType('image', 'tiff'), array('tif', 'tiff'));
		$this->registerType (new MimeType('image', 'x-icon'), array('ico')); // icon
		$this->registerType (new MimeType('image', 'x-quicktime'), array('qti', 'qtif'));
		$this->registerType (new MimeType('image', 'x-windows-bmp'), array(), $this->getType ('image', 'bmp')); // alias for BMP
		$this->registerType (new MimeType('image', 'x-xcf'), array('xcf'));
		$this->registerType (new MimeType('image', 'xpm'), array('xpm'));

		// multipart category
		$this->registerType (new MimeType('multipart', 'mixed'), array('eml')); // emails

		// text category
		$this->registerType (new MimeType('text', 'css'), array('css')); // CSS
		$this->registerType (new MimeType('text', 'csv'), array('csv')); // CSV
		$this->registerType (new MimeType('text', 'html'), array('html', 'html5', 'htm', 'shtml')); // html
		$this->registerType (new MimeType('text', 'pascal'), array('pas')); // pascal
		$this->registerType (new MimeType('text', 'plain'), array( // plain text
			'txt', 'text', 'conf', 'log', // simple text files
			'c', 'cpp', 'c++', 'cxx', 'cc', 'h', 'hh', // C & C++
			'f', 'for', // Fortran
			'java', // Java
			'pl', // Perl
			'php', 'php4', 'php5', 'php6', // PHP
			'tcl', // TCL
			'bsh', 'sh', 'shar', // Bash
			'sdml' // misc
		));
		$this->registerType (new MimeType('text', 'richtext'), array('rt', 'rtf', 'rtx'));
		$this->registerType (new MimeType('text', 'vcard'), array('vcard'));
		$this->registerType (new MimeType('text', 'x-script.python'), array('py')); // Python
		$this->registerType (new MimeType('text', 'xml'), array('xml', 'svg')); // xml

		// video category
		$this->registerType (new MimeType('video', 'avi'), array('avi')); // AVI video container
		$this->registerType (new MimeType('video', 'mpeg'), array('m1v', 'm2v')); // mpeg video
		$this->registerType (new MimeType('video', 'msvideo'), array(), $this->getType ('video', 'msvideo')); // MS avi
		$this->registerType (new MimeType('video', 'quicktime'), array('mov', 'moov'));
		$this->registerType (new MimeType('video', 'webm'), array('webm'));
		$this->registerType (new MimeType('video', 'x-flv'), array('flv'));
		$this->registerType (new MimeType('video', 'x-matroska'), array('mvk'));
		$this->registerType (new MimeType('video', 'x-ms-wmv'), array('wmv'));
		$this->registerType (new MimeType('video', 'x-msvideo'), array(), $this->getType ('video', 'msvideo')); // alias for MS avi

		// fire event
		Ikarus::getEventManager ()->fire (new MimeRegisterDefaultTypesEvent(new MimeRegisterEventArguments($this)));
	}

	/**
	 * Registers a new mime type.
	 * @param                        MimeType $type
	 * @param                        array    $fileExtensions
	 * @param                        MimeType $parentType
	 * @return                        void
	 */
	public function registerType (MimeType $type, array $fileExtensions = array(), MimeType $parentType = null, $isFallback = false) {
		$registeredType = new RegisteredMimeType($type, $fileExtensions, $parentType);

		// fire event
		$event = new MimeRegisterTypeEvent(new RegisteredMimeTypeEventArguments($registeredType));
		Ikarus::getEventManager ()->fire ($event);

		// cancellable
		if ($event->isCancelled ()) return;

		// add type
		$this->types[$type->__toString ()] = $registeredType;
		if ($isFallback) $this->fallback = $this->types[$type->__toString ()];

		// TODO: Add support for alias types.

		// fire event
		Ikarus::getEventManager ()->fire (new MimeTypeRegisteredEvent(new RegisteredMimeTypeEventArguments($registeredType)));
	}

	/**
	 * Unregisters a type.
	 * @param                        MimeType $type
	 * @return                        void
	 * @throws                        StrictStandardException
	 */
	public function unregisterType (MimeType $type) {
		// fire event
		$event = new UnregisterMimeTypeEvent(new MimeTypeEventArguments($type));
		Ikarus::getEventManager ()->fire ($event);

		// cancelable
		if ($event->isCancelled ()) return;

		// validate
		if (!$this->isKnownType ($type)) throw new StrictStandardException("Cannot unregister unknown mime type %s", $type->__toString ());

		// unregister
		unset($this->types[$type->__toString ()]);

		// fire event
		Ikarus::getEventManager ()->fire (new MimeTypeUnregisteredEvent(new MimeTypeEventArguments($type)));
	}
}

?>