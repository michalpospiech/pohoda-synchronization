<?php
/**
 * TRateVatTypes.php
 *
 * @author Michal Pospiech <michal@pospiech.cz>
 */

namespace MPospiech\PohodaSynchronization\Entities\Traits;


use MPospiech\PohodaSynchronization\Exceptions\OutOfRange;

trait RateVatTypes
{

	/** @var string */
	protected $rateVat = 'none';

	/** @var array */
	private static $rateVatTypes = [
		'none' => 'Bez DPH',
		'high' => 'Základní sazba',
		'low' => 'Snížená sazba',
		'third' => '3. sazba (pouze SK verze)',
		'historyHigh' => 'Historická základní sazba',
		'historyLow' => 'Historická snížená sazba',
		'historyThird' => 'Historická 3. sazba (pouze SK verze)'
	];

	/**
	 * Vrati typy sazeb DPH
	 *
	 * @param bool $associative
	 * @return array
	 */
	public static function getRateVatTypes($associative = true)
	{
		if (!$associative) {
			return array_keys(self::$rateVatTypes);
		}

		return self::$rateVatTypes;
	}

	/**
	 * Nastavi typ sazby DPH
	 *
	 * @param string $rateVat
	 * @return self
	 * @throws OutOfRange
	 */
	public function setRateVat($rateVat)
	{
		if (!in_array($rateVat, self::getRateVatTypes(false))) {
			throw new OutOfRange(sprintf('Unkown rate vat type %s', $rateVat));
		}

		$this->rateVat = $rateVat;

		return $this;
	}

}