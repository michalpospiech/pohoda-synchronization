<?php
/**
 * PohodaException.php
 *
 * @author Michal Pospiech <michal@pospiech.cz>
 */

namespace MPospiech\PohodaSynchronization\Exceptions;


class PohodaException extends \Exception
{

}

class UnkownEntity extends PohodaException
{

}

class OutOfRange extends PohodaException
{

}

class NotSupported extends PohodaException
{

}