<?php
/**
 * Address.php
 *
 * @author Michal Pospiech <michal@pospiech.cz>
 */

namespace MPospiech\PohodaSynchronization\Entities\Order;


use MPospiech\PohodaSynchronization\Entities\Entity;
use MPospiech\PohodaSynchronization\Entities\Traits;
use MPospiech\PohodaSynchronization\Exceptions;
use MPospiech\PohodaSynchronization\Pohoda;
use Nette\Utils\Validators;

class Address extends Entity
{

	use Traits\AddressType;

	/** @var int */
	protected $currentType;

	const ADDRESS = 'address';
	const SHIP_ADDRESS = 'shipToAddress';

	public function __construct($type = self::ADDRESS)
	{
		parent::__construct();

		if (!in_array($type, [self::ADDRESS, self::SHIP_ADDRESS])) {
			throw new Exceptions\OutOfRange(sprintf('Unkown address type %s', $type));
		}

		$this->currentType = $type;
	}

	/**
	 * Nastavi ICO
	 *
	 * @param string $ico
	 * @param bool $force
	 * @return self
	 * @throws Exceptions\NotSupported
	 */
	public function setIco($ico, $force = false)
	{
		if ($this->currentType !== self::ADDRESS && !$force) {
			throw new Exceptions\NotSupported('Ico for a different type than the primary address is not supported or use the force parameter');
		}

		$this->ico = $ico;

		return $this;
	}

	/**
	 * Nastavi DIC
	 *
	 * @param string $dic
	 * @param bool $force
	 * @return self
	 * @throws Exceptions\NotSupported
	 */
	public function setDic($dic, $force = false)
	{
		if ($this->currentType !== self::ADDRESS && !$force) {
			throw new Exceptions\NotSupported('Dic for a different type than the primary address is not supported or use the force parameter');
		}

		$this->dic = $dic;

		return $this;
	}

	/**
	 * Nastavi mobilni telefonni cislo
	 *
	 * @param string $mobilPhone
	 * @param bool $force
	 * @return Address
	 * @throws Exceptions\NotSupported
	 */
	public function setMobilPhone($mobilPhone, $force = false)
	{
		if ($this->currentType !== self::ADDRESS && !$force) {
			throw new Exceptions\NotSupported('Mobile phone for a different type than the primary address is not supported or use the force parameter');
		}

		$this->mobilPhone = $mobilPhone;

		return $this;
	}

	/**
	 * Nastavi fax
	 *
	 * @param string $fax
	 * @param bool $force
	 * @return self
	 * @throws Exceptions\NotSupported
	 */
	public function setFax($fax, $force = false)
	{
		if ($this->currentType !== self::ADDRESS && !$force) {
			throw new Exceptions\NotSupported('Fax for a different type than the primary address is not supported or use the force parameter');
		}

		$this->fax = $fax;

		return $this;
	}

	/**
	 * Nastavi, zda se jedna o vychozi dorucovaci adresu nebo ne
	 *
	 * @param bool $is
	 * @param bool $force
	 * @return self
	 * @throws Exceptions\NotSupported
	 */
	public function setDefaultShipAddress($is = false, $force = false)
	{
		if ($this->currentType !== self::SHIP_ADDRESS && !$force) {
			throw new Exceptions\NotSupported('Default ship address only for shippment address');
		}

		$this->defaultShipAddress = $is;

		return $this;
	}

	/**
	 * Zvaliduje a nastavi e-mail
	 *
	 * @param string $email
	 * @param bool $force
	 * @return $this
	 * @throws Exceptions\UnvalidValue
	 */
	public function setEmail($email, $force = false)
	{
		if (!Validators::isEmail($email) && !$force) {
			throw new Exceptions\UnvalidValue(sprintf('Unvalid e-mail %s', $email));
		}

		$this->email = $email;

		return $this;
	}

	public function getXMLElement()
	{
		$xml = new \DOMDocument('1.0', Pohoda::XML_ENCODING);

		$address = $xml->createElementNS($this->getXmlNamespace('typ'), sprintf('typ:%s', $this->currentType));
		$xml->appendChild($address);

		// zakladni elementy
		$elements = ['company', 'division', 'name', 'city', 'street', 'zip', 'country', 'email', 'phone'];
		if ($this->currentType === self::ADDRESS) {
			$elements[] = 'ico';
			$elements[] = 'dic';
		} else {
			$elements[] = 'defaultShipAddress';
		}
		foreach ($elements as $element) {
			$value = $this->getValue($element);
			if (!$value) {
				continue;
			}

			$el = $xml->createElementNS($this->getXmlNamespace('typ'), sprintf('typ:%s', $element));
			$el->appendChild($xml->createTextNode($value));
			$address->appendChild($el);
		}

		// mobilni telefonni cislo
		if ($this->currentType === self::ADDRESS && $this->getValue('mobilPhone')) {
			$mobilPhone = $xml->createElementNS($this->getXmlNamespace('typ'), 'mobilPhone');
			$mobilPhone->appendChild($xml->createTextNode($this->getValue('mobilPhone')));
			$address->appendChild($mobilPhone);
		}

		// fax
		if ($this->currentType === self::ADDRESS && $this->getValue('fax')) {
			$fax = $xml->createElementNS($this->getXmlNamespace('typ'), 'fax');
			$fax->appendChild($xml->createTextNode($this->getValue('fax')));
			$address->appendChild($fax);
		}

		return $address;
	}

}