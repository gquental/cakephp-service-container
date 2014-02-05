<?php
App::uses('ServiceException', 'ServiceContainer.Lib/Service/Exception');
App::uses('ServiceConfigException', 'ServiceContainer.Lib/Service/Exception');

class ServiceContainer {

	protected $_services;

	public function __construct() {
        $this->__initServices();
		$this->_services = Configure::read('Services');
	}

	public function getService($serviceName) {
		if (!isset($this->_services[$serviceName])) {
			throw new ServiceException(array($serviceName));
		}

		$serviceEntry = $this->_services[$serviceName];
		$serviceClassName = $serviceEntry['name'];
		$serviceClassPath = $serviceEntry['path'];

		App::uses($serviceClassName, $serviceClassPath);

		return new $serviceClassName;
	}

    private function __initServices() {
        $this->_services = Configure:read('Services');

        if (is_null($this->_services)) {
            throw new ServiceConfigException(array());
        }
    }

}