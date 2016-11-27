<?php
/**
 * RoundingDocumentTypes.php
 *
 * @author Michal Pospiech <michal@pospiech.cz>
 */

namespace MPospiech\PohodaSynchronization\Entities\Traits;


use MPospiech\PohodaSynchronization\Exceptions\OutOfRange;

trait RoundingDocumentTypes
{

	/** @var string */
	protected $roundingDocument = 'math2one';

	/** @var array */
	private static $roundingDocumentTypes = [
		'none' => 'Doklad nezaokrouhlovat',
		'math2one' => 'Zaokrouhlení matematicky na koruny',
		'math2half' => 'Zaokrouhlení matematicky na padesátníky',
		'math2tenth' => 'Zaokrouhlení matematicky na desetníky',
		'up2one' => 'Zaokrouhlení nahoru na koruny',
		'up2half' => 'Zaokrouhlení nahoru na padesátníky',
		'up2tenth' => 'Zaokrouhlení nahoru na desetníky',
		'down2one' => 'Zaokrouhlení dolů na koruny',
		'down2half' => 'Zaokrouhlení dolů na padesátníky',
		'down2tenth' => 'Zaokrouhlení dolů na desetníky'
	];

	/**
	 * Vrati typy zaokrouhleni
	 *
	 * @param bool $associative
	 * @return array
	 */
	public static function getRoundingDocumentTypes($associative = true)
	{
		if (!$associative) {
			return array_keys(self::$roundingDocumentTypes);
		}

		return self::$roundingDocumentTypes;
	}

	/**
	 * Nastavi typ zaokrouhlovani dokladu
	 *
	 * @param string $rounding
	 * @return self
	 * @throws OutOfRange
	 */
	public function setRoundingDocument($rounding)
	{
		if (!in_array($rounding, self::getRoundingDocumentTypes(false))) {
			throw new OutOfRange(sprintf('Unkown rounding document type %s', $rounding));
		}

		$this->roundingDocument = $rounding;

		return $this;
	}

}