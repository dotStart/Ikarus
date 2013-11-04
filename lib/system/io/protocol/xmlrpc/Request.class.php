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
namespace ikarus\system\io\protocol\xmlrpc;

use ikarus\system\io\file\XMLDocument;
use ikarus\system\io\http\RequestBuilder;

/**
 * Builds a XMLRPC request.
 * @author                    Johannes Donath
 * @copyright                 © Copyright 2012 Evil-Co.de <http://www.evil-co.com>
 * @package                   de.ikarus-framework.core
 * @subpackage                system
 * @category                  Ikarus Framework
 * @license                   GNU Lesser Public License <http://www.gnu.org/licenses/lgpl.txt>
 * @version                   2.0.0-0001
 */
class Request {

	/**
	 * Stores the request's method name.
	 * @var                        string
	 */
	protected $methodName = '';

	/**
	 * Stores a list of method parameters.
	 * @var                        mixed[]
	 */
	protected $parameters = array ();

	/**
	 * Constructs the request.
	 * @param                        string $methodName
	 */
	public function __construct ($methodName) {
		$this->methodName = $methodName;
	}

	/**
	 * Appends a parameter based on its value type.
	 * @param                        mixed   $value
	 * @param                        XMLNode $parent
	 */
	protected function appendParameter ($value, $parent) {
		$type = $this->detectType ($value);
		$content = $doc->createElement ($type);

		if ($value !== null) {
			if ($type == static::TYPE_STRUCT) {
				$struct = $parent->createElement ('struct');

				foreach ($value as $key => $val) {
					// create member element
					$member = $struct->createElement ('member');

					// name element
					$name = $member->createElement ('name');
					$name->setValue ($key);
					$member->appendChild ($name);

					// cerate value
					$par = $member->createElement ('value');
					$member->appendChild ($par);

					// append data
					$this->appendParameter ($key, $val, $par);
				}

				$content->appendChild ($struct);
			} else {
				$content->setValue ($value);
			}
		}

		$parent->append ($content);
	}

	/**
	 * Builds a new request.
	 * @return                        ikarus\system\io\http\RequestBuilder
	 */
	public function buildQuery () {
		$doc = new XMLDocument('methodCall');
		$doc->setVersion ('1.0');
		$doc->setEncoding ('UTF-8');

		// append method name
		$methodName = $doc->createElement ('methodName');
		$methodName->setValue ($this->methodName, true);
		$doc->appendChild ($methodName);

		// append parameters
		$parameters = $doc->createElement ('params');
		$doc->appendChild ($parameters);

		foreach ($this->parameters as $name => $value) {
			$param = $doc->createElement ('param');
			$parameters->appendChild ($doc);

			$parent = $doc->createElement ('value');
			$param->appendChild ($parent);

			$this->appendParameter ($name, $value, $parent);
		}

		// build XMLRPC request
		$request = new RequestBuilder();
		$request->setType (RequestBuilder::TYPE_POST);
		$request->setPostData ($doc->__toString ());
		$request->setContentType ('text/xml');

		// return request
		return $request;
	}

	/**
	 * Returns a set parameter's value.
	 * @param                        string $name
	 * @throws                        IndexOutOfBoundExceptions
	 * @return                        mixed
	 */
	public function getParameter ($name) {
		if (!array_key_exists ($name, $this->parameters)) throw new IndexOutOfBoundExceptions('The parameter "%s" is not defined.');

		return $this->parameters[$name];
	}

	/**
	 * Stores a parameter.
	 * @param                        string $name
	 * @param                        mixed  $value
	 */
	public function setParameter ($name, $value) {
		$this->parameters[$name] = $value;
	}
}

?>