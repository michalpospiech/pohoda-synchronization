<?php
/**
 * Order.php
 *
 * @author Michal Pospiech <michal@pospiech.cz>
 */

namespace MPospiech\PohodaSynchronization\Entities\Order;


use MPospiech\PohodaSynchronization\Entities\Entity;
use MPospiech\PohodaSynchronization\Entities\Traits;
use MPospiech\PohodaSynchronization\Pohoda;

/**
 * @method Order setNumberOrder(string $numberOrder)
 * @method Order setDate(\DateTime|string $date)
 * @method Order setDateFrom(\DateTime|string $dateFrom)
 * @method Order setDateTo(\DateTime|string $dateTo)
 * @method Order setText(string $text)
 * @method Order setDiscountPercentage(float $text)
 * @method Order setPartnerIdentity(PartnerIdentity $partner)
 * @method Order addItem(Item $item)
 * @method PartnerIdentity getPartnerIdentity()
 * @method array getItems()
 */
class Order extends Entity
{

	use Traits\PaymentTypes;
	use Traits\OrderTypes;
	use Traits\RoundingDocumentTypes;

	/** @var string */
	protected $id;

	/** @var string */
	protected $numberOrder;

	/** @var \DateTime|string */
	protected $date;

	/** @var \DateTime|string */
	protected $dateFrom;

	/** @var \DateTime|string */
	protected $dateTo;

	/** @var string */
	protected $text;

	/** @var float */
	protected $discountPercentage;

	/** @var PartnerIdentity */
	protected $partnerIdentity;

	/** @var array[Item] */
	protected $items = [];

	const RECEIVED = 'receivedOrder';
	const ISSUED = 'issuedOrder';

	/**
	 * @param string|int|null $id
	 */
	public function __construct($id = null)
	{
		parent::__construct($id);
		$this->partnerIdentity = new PartnerIdentity();
	}

	/**
	 * Prida adresu do entity PartnerIdentity
	 *
	 * @param Address $address
	 * @return self
	 */
	public function addAddress(Address $address)
	{
		$this->partnerIdentity->addAddress($address);

		return $this;
	}

	/**
	 * Vrati pripraveny dataPackItem
	 *
	 * @return \DOMElement
	 */
	public function getXMLElement()
	{
		$xml = new \DOMDocument('1.0', Pohoda::XML_ENCODING);

		$dataPackItem = $xml->createElementNS($this->getXmlNamespace('dat'), 'dat:dataPackItem');
		$dataPackItem->setAttribute('version', '2.0');
		$dataPackItem->setAttribute('id', $this->getValue('id'));
		$xml->appendChild($dataPackItem);

		$order = $xml->createElementNS($this->getXmlNamespace('ord'), 'ord:order');
		$order->setAttribute('version', '2.0');
		$dataPackItem->appendChild($order);

		// ----- hlavicka -----
		$header = $xml->createElementNS($this->getXmlNamespace('ord'), 'ord:orderHeader');
		$order->appendChild($header);

		// zakladni elementy
		$headerElements = ['orderType', 'numberOrder', 'date', 'dateFrom', 'dateTo', 'text', 'discountPercentage'];
		foreach ($headerElements as $headerElement) {
			$value = $this->getValue($headerElement);
			if (!$value) {
				continue;
			}

			$el = $xml->createElementNS($this->getXmlNamespace('ord'), sprintf('ord:%s', $headerElement));
			$el->appendChild($xml->createTextNode($value));
			$header->appendChild($el);
		}

		// dodavatel/odberatel
		$header->appendChild($xml->importNode($this->getPartnerIdentity()->getXMLElement(), true));

		// typ uhrady
		$paymentType = $xml->createElement('ord:paymentType');
		$paymentIds = $xml->createElement('typ:ids');
		$paymentIds->appendChild($xml->createTextNode($this->getValue('paymentType')));
		$paymentType->appendChild($paymentIds);
		$header->appendChild($paymentType);

		// ----- detail objednavky -----
		$detail = $xml->createElementNS($this->getXmlNamespace('ord'), 'ord:orderDetail');
		$order->appendChild($detail);

		// polozky objednavky
		/** @var Item $item */
		foreach ($this->getItems() as $item) {
			$detail->appendChild($xml->importNode($item->getXMLElement(), true));
		}

		// zakonceni objednavky - summary
		$summary = $xml->createElement('ord:orderSummary');
		$roundingDocument = $xml->createElement('ord:roundingDocument');
		$roundingDocument->appendChild($xml->createTextNode($this->getValue('roundingDocument')));
		$summary->appendChild($roundingDocument);
		$order->appendChild($summary);

		return $dataPackItem;
	}

}