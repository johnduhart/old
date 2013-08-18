<?php

namespace FPChat\Plugin\Logs;
use Shanty\Mongo\Document;

class User extends Document
{
	protected static $_db = 'logs';
	protected static $_collection = 'users';

	protected static $_requirements = array(
		'userid' => array('Required', 'Validator:Digits'),
		'username' => 'Required'
	);
}
