<?php
App::uses('CakeException', 'Error');

class ServiceException extends CakeException {

	protected $_messageTemplate = 'Service %s was not found.';
}