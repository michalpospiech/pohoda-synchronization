<?php
/**
 * PartnerIdentity.php
 *
 * @author Michal Pospiech <michal@pospiech.cz>
 */

namespace MPospiech\PohodaSynchronization\Entities\Order;


use MPospiech\PohodaSynchronization\Entities\Entity;
use MPospiech\PohodaSynchronization\Pohoda;

/**
 * @method PartnerIdentity addAddress(Address $address)
 */
class PartnerIdentity extends Entity
{

	/** @var array */
	protected $addresses = [];

	public function getXMLElement()
	{
		$xml = new \DOMDocument('1.0', Pohoda::XML_ENCODING);

		$partnerIdentity = $xml->createElementNS($this->getXmlNamespace('ord'), 'ord:partnerIdentity');
		$xml->appendChild($partnerIdentity);

		/** @var Address $address */
		foreach ($this->addresses as $address) {
			$partnerIdentity->appendChild($xml->importNode($address->getXMLElement(), true));
		}

		return $partnerIdentity;
	}

}