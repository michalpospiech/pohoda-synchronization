<?php
/**
 * PartnerIdentity.php
 *
 * @author Michal Pospiech <michal@pospiech.cz>
 */

namespace MPospiech\PohodaSynchronization\Entities\Order;


use MPospiech\PohodaSynchronization\Entities\Entity;
use MPospiech\PohodaSynchronization\Pohoda;
use Nette\Utils\Strings;

/**
 * @method PartnerIdentity setCompany(string $company)
 * @method PartnerIdentity setDivision(string $division)
 * @method PartnerIdentity setName(string $name)
 * @method PartnerIdentity setCity(string $city)
 * @method PartnerIdentity setStreet(string $street)
 * @method PartnerIdentity setIco(string $ico)
 * @method PartnerIdentity setDic(string $dic)
 */
class PartnerIdentity extends Entity
{

	/** @var string */
	protected $company;

	/** @var string */
	protected $division;

	/** @var string */
	protected $name;

	/** @var string */
	protected $city;

	/** @var string */
	protected $street;

	/** @var int */
	protected $zip;

	/** @var string */
	protected $ico;

	/** @var string */
	protected $dic;

	/**
	 * @param string|int $zip
	 * @return PartnerIdentity
	 */
	public function setZip($zip)
	{
		if (is_int($zip)) {
			$this->zip = $zip;
			return $this;
		}

		$zip = Strings::replace($zip, '~\s+~', '');
		$this->zip = $zip;

		return $this;
	}

	public function getXMLElement()
	{
		$xml = new \DOMDocument('1.0', Pohoda::XML_ENCODING);

		$address = $xml->createElementNS($this->getXmlNamespace('typ'), 'typ:address');
		$xml->appendChild($address);

		$elements = ['company', 'division', 'name', 'city', 'street', 'zip', 'ico', 'dic'];
		foreach ($elements as $element) {
			$value = $this->getValue($element);
			if (!$value) {
				continue;
			}

			$el = $xml->createElementNS($this->getXmlNamespace('typ'), sprintf('typ:%s', $element));
			$el->appendChild($xml->createTextNode($value));
			$address->appendChild($el);
		}

		return $address;
	}

}