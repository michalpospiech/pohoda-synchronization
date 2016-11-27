<?php
/**
 * TOrderTypes.php
 *
 * @author Michal Pospiech <michal@pospiech.cz>
 */

namespace MPospiech\PohodaSynchronization\Entities\Traits;


use MPospiech\PohodaSynchronization\Exceptions\OutOfRange;

trait OrderTypes
{

	/** @var string */
	protected $orderType = self::RECEIVED;

	/** @var array */
	private static $orderTypes = [
		self::RECEIVED => 'Přijatá objednávka',
		self::ISSUED => 'Vydaná objednávka'
	];

	/**
	 * Vrati typy objednavek
	 *
	 * @param bool $associative
	 * @return array
	 */
	public static function getOrderTypes($associative = true)
	{
		if (!$associative) {
			return array_keys(self::$orderTypes);
		}

		return self::$orderTypes;
	}

	/**
	 * Nastavi typ objednavky
	 *
	 * @param $type
	 * @return self
	 * @throws OutOfRange
	 */
	public function setOrderType($type)
	{
		if (!in_array($type, self::getOrderTypes(false))) {
			throw new OutOfRange(sprintf('Unkown order type %s', $type));
		}

		$this->orderType = $type;

		return $this;
	}

}