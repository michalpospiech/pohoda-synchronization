<?php
/**
 * TPaymentTypes.php
 *
 * @author Michal Pospiech <michal@pospiech.cz>
 */

namespace MPospiech\PohodaSynchronization\Entities\Traits;


use MPospiech\PohodaSynchronization\Exceptions\OutOfRange;

trait PaymentTypes
{

	/** @var string */
	protected $paymentType;

	/** @var array */
	private static $paymentTypes = [
		'draft' => 'Příkazem',
		'cash' => 'Hotově',
		'postal' => 'Složenkou',
		'delivery' => 'Dobírkou',
		'creditcard' => 'Platební kartou',
		'advance' => 'Zálohovou fakturou',
		'encashment' => 'Inkasem',
		'cheque' => 'Šekem',
		'compensation' => 'Zápočtem'
	];

	/**
	 * Vrati typy uhrad
	 *
	 * @param bool $associative
	 * @return array
	 */
	public static function getPaymentTypes($associative = true)
	{
		if (!$associative) {
			return array_keys(self::$paymentTypes);
		}

		return self::$paymentTypes;
	}

	/**
	 * Nastavi typ uhrady
	 *
	 * @param $type
	 * @return self
	 * @throws OutOfRange
	 */
	public function setPaymentType($type)
	{
		if (!in_array($type, self::getPaymentTypes(false))) {
			throw new OutOfRange(sprintf('Unkown payment type %s', $type));
		}

		$this->paymentType = $type;

		return $this;
	}

}