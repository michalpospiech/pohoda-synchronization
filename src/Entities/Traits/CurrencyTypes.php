<?php
/**
 * CurrencyTypes.php
 *
 * @author Michal Pospiech <michal@pospiech.cz>
 */

namespace MPospiech\PohodaSynchronization\Entities\Traits;


use MPospiech\PohodaSynchronization\Exceptions\OutOfRange;

trait CurrencyTypes
{

	/** @var string */
	protected $homeCurrency;

	/** @var float */
	protected $homeCurrencyValue;

	/** @var string */
	protected $foreignCurrency;

	/** @var float */
	protected $foreignCurrencyValue;

	/** @var array */
	private static $currencyTypes = [
		'unitPrice' => 'Jednotková cena',
		'price' => 'Cena položky bez DPH',
		'priceVAT' => 'DPH na položce',
		'priceSum' => 'Cena položky vč. DPH'
	];

	/**
	 * Vrati typy cen
	 *
	 * @param bool $associative
	 * @return array
	 */
	public static function getCurrencyTypes($associative = true)
	{
		if (!$associative) {
			return array_keys(self::$currencyTypes);
		}

		return self::$currencyTypes;
	}

	/**
	 * Nastavi typ domaci meny
	 *
	 * @param string $currency
	 * @param float $value
	 * @return static
	 * @throws OutOfRange
	 */
	public function setHomeCurrency($currency, $value)
	{
		if (!in_array($currency, self::getCurrencyTypes(false))) {
			throw new OutOfRange(sprintf('Unkown currency type %s', $currency));
		}

		$this->homeCurrency = $currency;
		$this->homeCurrencyValue = $value;

		return $this;
	}

	/**
	 * Nastavi typ cizi meny
	 *
	 * @param string $currency
	 * @param float $value
	 * @return static
	 * @throws OutOfRange
	 */
	public function setForeignCurrency($currency, $value)
	{
		if (!in_array($currency, self::getCurrencyTypes(false))) {
			throw new OutOfRange(sprintf('Unkown currency type %s', $currency));
		}

		$this->foreignCurrency = $currency;
		$this->foreignCurrencyValue = $value;

		return $this;
	}

}