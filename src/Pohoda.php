<?php
/**
 * Pohoda.php
 *
 * @author Michal Pospiech <michal@pospiech.cz>
 */

namespace MPospiech\PohodaSynchronization;


use MPospiech\PohodaSynchronization\Entities\Entity;
use MPospiech\PohodaSynchronization\Entities\Order\Order;
use MPospiech\PohodaSynchronization\Entities\Traits;
use MPospiech\PohodaSynchronization\Exceptions\UnkownEntity;

class Pohoda
{

	use Traits\Ico;

	/** @var \DOMDocument */
	private $domDocument;

	/** @var string */
	private $version = '2.0';

	/** @var string */
	protected $id;

	/** @var array */
	protected $entities = [];

	/** @var array */
	public static $namespaces = [
		'dat' => 'http://www.stormware.cz/schema/version_2/data.xsd',
		'ord' => 'http://www.stormware.cz/schema/version_2/order.xsd',
		'typ' => 'http://www.stormware.cz/schema/version_2/type.xsd'
	];

	//const XML_ENCODING = 'windows-1250';
	const XML_ENCODING = 'utf-8';

	const ORDER = Order::class;

	/**
	 * Pohoda constructor
	 *
	 * @param string $ico
	 */
	public function __construct($ico, $id)
	{
		$this->ico = $ico;
		$this->id = $id;

		$this->domDocument = new \DOMDocument('1.0', self::XML_ENCODING);
	}

	/**
	 * Vrati nastavene ID
	 *
	 * @return string
	 */
	public function getId()
	{
		return $this->id;
	}

	/**
	 * Vytvori a pripravi entitu pro zapis hodnot
	 *
	 * @param string $entity
	 * @param string|null $id
	 * @return Entity
	 * @throws UnkownEntity
	 */
	public function createEntity($entity, $id = null)
	{
		if (!class_exists($entity)) {
			throw new UnkownEntity(sprintf('Unkown entity %s', $entity));
		}

		return new $entity($id);
	}

	/**
	 * Prida sestavenou entitu
	 *
	 * @param Entity $entity
	 */
	public function addEntity(Entity $entity)
	{
		$this->entities[] = $entity;
	}

	private function createXml($note = null)
	{
		$xml = $this->domDocument;

		$dat = $xml->createElement('dat:dataPack');
		$xml->appendChild($dat);

		foreach (self::$namespaces as $key => $url) {
			$dat->setAttribute(sprintf('xmlns:%s', $key), $url);
		}

		$dat->setAttribute('id', $this->getId());
		$dat->setAttribute('ico', $this->getIco());
		$dat->setAttribute('application', 'Pohoda synchronization');
		$dat->setAttribute('version', '2.0');
		$dat->setAttribute('note', $note);

		/** @var Entity $entity */
		foreach ($this->entities as $entity) {
			$node = $xml->importNode($entity->getXMLElement(), true);
			$dat->appendChild($node);
		}

		return $xml;
	}

	public function getXml($note = null)
	{
		$xml = $this->createXml();
		header('Content-type: text/xml');
		echo $xml->saveXML();
	}

}