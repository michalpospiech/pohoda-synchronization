<?php
/**
 * TIco.php
 *
 * @author Michal Pospiech <michal@pospiech.cz>
 */

namespace MPospiech\PohodaSynchronization\Entities\Traits;


trait Ico
{

	/** @var string */
	protected $ico;

	/**
	 * Vrati nastaveno ICO
	 *
	 * @return string
	 */
	public function getIco()
	{
		return $this->ico;
	}

}