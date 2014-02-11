<?php
App::uses('ServiceException', 'ServiceContainer.Lib/Service/Exception');
App::uses('ServiceConfigException', 'ServiceContainer.Lib/Service/Exception');

class ServiceContainer {
    const SERVICE_IDENTIFIER = '@';

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
        $serviceClassArgs = array();

        if (isset($serviceEntry['arguments'])) {
            $serviceClassArgs = $serviceEntry['arguments'];
        }

        return $this->__createInstance(
            $serviceClassName,
            $serviceClassPath,
            $serviceClassArgs
        );
    }

    private function __initServices() {
        $this->_services = Configure::read('Services');

        if (is_null($this->_services)) {
            throw new ServiceConfigException(array());
        }
    }

    private function __createInstance($className, $classPath, $classArgs) {
        $arguments = $this->__prepareArguments($classArgs);

        App::uses($className, $classPath);

        $reflection = new \ReflectionClass($className);

        return $reflection->newInstanceArgs($arguments);
    }

    private function __prepareArguments($arguments) {
        foreach ($arguments as $key => $value) {
            if (substr($value, 0, 1) === self::SERVICE_IDENTIFIER) {
                $serviceName = str_replace(self::SERVICE_IDENTIFIER, '', $value);
                $arguments[$key] = $this->getService($serviceName);
            }
        }

        return $arguments;
    }

}