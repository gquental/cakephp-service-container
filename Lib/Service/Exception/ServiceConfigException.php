<?php
App::uses('CakeException', 'Error');

class ServiceConfigException extends CakeException {

	protected $_messageTemplate = 'The service config file was not found.';
}