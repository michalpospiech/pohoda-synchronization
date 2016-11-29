<?php
/**
 * Item.php
 *
 * @author Michal Pospiech <michal@pospiech.cz>
 */

namespace MPospiech\PohodaSynchronization\Entities\Order;


use MPospiech\PohodaSynchronization\Entities\Entity;
use MPospiech\PohodaSynchronization\Entities\Traits;
use MPospiech\PohodaSynchronization\Exceptions;
use MPospiech\PohodaSynchronization\Pohoda;

/**
 * @method Item setText(string $text)
 * @method Item setQuantity(float $quantity)
 * @method Item setDelivered(float $delivered)
 * @method Item setUnit(string $unit)
 * @method Item setCoefficient(float $coefficient)
 * @method Item setPayVat(bool $payVat)
 * @method Item setDiscountPercentage(float $percentage)
 * @method Item setNote(string $note)
 * @method Item setCode(string $code)
 */
class Item extends Entity
{

	use Traits\RateVatTypes;
	use Traits\CurrencyTypes;

	/** @var int */
	protected $id;

	/** @var string:90 */
	protected $text;

	/** @var float */
	protected $quantity;

	/** @var float */
	protected $delivered = 0;

	/** @var string */
	protected $unit;

	/** @var float */
	protected $coefficient = 1.0;

	/** @var bool */
	protected $payVat = false;

	/** @var float */
	protected $discountPercentage = 0;

	/** @var string:90 */
	protected $note;

	/** @var string:64 */
	protected $code;

	const VAT_NONE = 'none';
	const VAT_HIGH = 'high';
	const VAT_LOW = 'low';
	const VAT_THIRD = 'third';
	const VAT_HISTORY_HIGH = 'historyHigh';
	const VAT_HISTORY_LOW = 'historyLow';
	const VAT_HISTORY_THIRD = 'historyThird';

	const CURRENCY_UNIT = 'unitPrice';
	const CURRENCY_PRICE = 'price';
	const CURRENCY_PRICE_VAT = 'priceVAT';
	const CURRENCY_PRICE_SUM = 'priceSum';

	/**
	 * @param int $id
	 * @param bool $force
	 * @return self
	 * @throws Exceptions\NotSupported
	 */
	public function setId($id, $force = false)
	{
		if (!$force) {
			throw new Exceptions\NotSupported('ID is not supported for export or use force parameter');
		}

		$this->id = $id;

		return $this;
	}

	/**
	 * @return \DOMElement
	 */
	public function getXMLElement()
	{
		$xml = new \DOMDocument('1.0', Pohoda::XML_ENCODING);

		$orderItem = $xml->createElementNS($this->getXmlNamespace('ord'), 'ord:orderItem');
		$xml->appendChild($orderItem);

		$elements = ['text', 'quantity', 'delivered', 'unit', 'coefficient', 'payVat', 'discountPercentage', 'note', 'code'];
		foreach ($elements as $element) {
			$value = $this->getValue($element);
			if (!$value) {
				continue;
			}

			$el = $xml->createElementNS($this->getXmlNamespace('ord'), sprintf('ord:%s', $element));
			$el->appendChild($xml->createTextNode($value));
			$orderItem->appendChild($el);
		}

		// cena v domaci mene
		if ($this->homeCurrency && $this->homeCurrencyValue) {
			$homeCurrency = $xml->createElementNS($this->getXmlNamespace('ord'), 'ord:homeCurrency');
			$value = $xml->createElementNS($this->getXmlNamespace('typ'), sprintf('typ:%s', $this->getValue('homeCurrency')));
			$value->appendChild($xml->createTextNode($this->getValue('homeCurrencyValue')));
			$homeCurrency->appendChild($value);
			$orderItem->appendChild($homeCurrency);
		}

		// cena v zahranicni mene
		if ($this->foreignCurrency && $this->foreignCurrencyValue) {
			$foreignCurrency = $xml->createElementNS($this->getXmlNamespace('ord'), 'ord:foreignCurrency');
			$value = $xml->createElementNS($this->getXmlNamespace('typ'), sprintf('typ:%s', $this->getValue('foreignCurrency')));
			$value->appendChild($xml->createTextNode($this->getValue('foreignCurrencyValue')));
			$foreignCurrency->appendChild($value);
			$orderItem->appendChild($foreignCurrency);
		}

		return $orderItem;
	}

}