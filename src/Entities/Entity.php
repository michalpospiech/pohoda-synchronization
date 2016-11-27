<?php
/**
 * Entity.php
 *
 * @author Michal Pospiech <michal@pospiech.cz>
 */

namespace MPospiech\PohodaSynchronization\Entities;


use MPospiech\PohodaSynchronization\Entities\Traits;
use MPospiech\PohodaSynchronization\Exceptions;
use MPospiech\PohodaSynchronization\Pohoda;
use Nette\Utils\Strings;

/**
 * @method $this setId(string $id)
 */
abstract class Entity
{

	/**
	 * @return \DOMElement
	 */
	abstract public function getXMLElement();

	/**
	 * @param string|int|null $id
	 */
	public function __construct($id = null)
	{
		if ($id && property_exists($this, 'id')) {
			$this->setId($id);
		}
	}

	/**
	 * Vrati pripravenou textovou podobu hodnoty
	 *
	 * @param string $property
	 * @return string|null
	 */
	protected function getValue($property)
	{
		try {
			$value = $this->{'get' . $property}();

			if (is_scalar($value)) {
				return $value;
			} else if ($value instanceof \DateTime) {
				return $value->format('Y-m-d');
			}

			return null;
		} catch (Exceptions\PohodaException $exception) {
			return null;
		}
	}

	public function __call($name, $arguments)
	{
		$type = null;
		$property = null;

		// set<property> method
		if (preg_match('~set([A-Z][a-zA-Z0-9]*)~i', $name, $property)) {
			$type = 'set';
			$property = Strings::firstLower($property[1]);
		}
		// get<property> method
		else if (preg_match('~get([A-Z][a-zA-Z0-9]*)~i', $name, $property)) {
			$type = 'get';
			$property = Strings::firstLower($property[1]);
		}
		// add<property> method
		else if (preg_match('~add([A-Z][a-zA-Z0-9]*)~i', $name, $property)) {
			$type = 'add';
			$property = Strings::firstLower($property[1]);
		}

		if ($type && $type === 'set') {
			if ($property && property_exists($this, $property) && array_key_exists(0, $arguments)) {
				$this->$property = $arguments[0];

				return $this;
			} else {
				if (!$property || !property_exists($this, $property)) {
					throw new Exceptions\OutOfRange(sprintf('Unkown property by method %s', $name));
				}

				if (!array_key_exists(0, $arguments)) {
					throw new Exceptions\PohodaException(sprintf('Undefined value'));
				}
			}
		} else if ($type && $type === 'get') {
			if ($property && property_exists($this, $property)) {
				return $this->$property;
			} else {
				throw new Exceptions\OutOfRange(sprintf('Unkown property by method %s', $name));
			}
		} else if ($type && $type === 'add') {
			if (property_exists($this, $property . 's')) {
				$property = $property . 's';
			} else {
				throw new Exceptions\OutOfRange(sprintf('Unkown property by method %ss', $name));
			}

			if (!is_array($this->$property)) {
				throw new Exceptions\PohodaException(sprintf('Property %s is not array', $property));
			}

			$this->{$property}[] = $arguments[0];

			return $this;
		} else {
			throw new Exceptions\OutOfRange(sprintf('Unkown method %s', $name));
		}

		return $this;
	}

	/**
	 * Vrati url XML namespace dle jeho nazvu
	 *
	 * @param string $name
	 * @return string
	 * @throws Exceptions\OutOfRange
	 */
	public function getXmlNamespace($name)
	{
		if (!$name) {
			return $name;
		}

		if (!array_key_exists($name, Pohoda::$namespaces)) {
			throw new Exceptions\OutOfRange(sprintf('Unkown namespace %s', $name));
		}

		return Pohoda::$namespaces[$name];
	}

}