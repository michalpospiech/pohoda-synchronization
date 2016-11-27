<?php
/**
 * AddressType.php
 *
 * @author Michal Pospiech <michal@pospiech.cz>
 */

namespace MPospiech\PohodaSynchronization\Entities\Traits;
use Nette\Utils\Strings;


/**
 * @method $this setCompany(string $company)
 * @method $this setDivision(string $division)
 * @method $this setName(string $name)
 * @method $this setCity(string $city)
 * @method $this setStreet(string $street)
 * @method $this setIco(string $ico)
 * @method $this setDic(string $dic)
 * @method $this setCountry(string $country)
 * @method $this setPhone(string $phone)
 * @method $this setMobilPhone(string $mobilPhone)
 * @method $this setFax(string $fax)
 * @method $this setEmail(string $email)
 */
trait AddressType
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

	/** @var int|string */
	protected $zip;

	/** @var string */
	protected $ico;

	/** @var string */
	protected $dic;

	/** @var string */
	protected $country;

	/** @var string */
	protected $phone;

	/** @var string */
	protected $mobilPhone;

	/** @var string */
	protected $fax;

	/** @var string */
	protected $email;

	/** @var bool */
	protected $defaultShipAddress = false;

	/**
	 * Nastavi PSC
	 *
	 * @param string|int $zip
	 * @return self
	 */
	public function setZip($zip)
	{
		if (is_int($zip)) {
			$this->zip = $zip;

			return $this;
		}

		$this->zip = Strings::replace($zip, '~\s+~i', '');

		return $this;
	}

}