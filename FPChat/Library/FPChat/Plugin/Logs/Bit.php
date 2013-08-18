<?php

namespace FPChat\Plugin\Logs;
use Shanty\Mongo\Document;

class Bit extends Document
{
	protected static $_db = 'logs';
	protected static $_collection = 'bits';

	protected static $_requirements = array(
		'timestamp' => array('Required', 'Validator:Digits'),
		'type' => 'Required',
		'user' => array('Document', 'AsReference')
	);
}
