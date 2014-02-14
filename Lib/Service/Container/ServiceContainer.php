<?php
App::uses('ServiceException', 'ServiceContainer.Lib/Service/Exception');
App::uses('ServiceConfigException', 'ServiceContainer.Lib/Service/Exception');
App::uses('ServiceCache', 'ServiceContainer.Lib/Service/Cache');

class ServiceContainer {
    const SERVICE_IDENTIFIER = '@';

    protected $_services;
    protected $_serviceCache;

    /**
     * Start the services available in the system based in
     * the configuration file. Also it starts the service cache.
     */
    public function __construct() {
        $this->__initServices();
        $this->_serviceCache = new ServiceCache();
    }

    /**
     * Retrieves the instance of the service requested if it's
     * found in the service container configuration file
     * @param $serviceName
     * @param bool $shouldBeNew
     * @return bool|object
     * @throws ServiceException
     */
    public function getService($serviceName, $shouldBeNew = false) {
        if (!isset($this->_services[$serviceName])) {
            throw new ServiceException(array($serviceName));
        }

        if (!$shouldBeNew) {
            $serviceInstance = $this->_serviceCache->get($serviceName);

            if ($serviceInstance) {
                return $serviceInstance;
            }
        }

        $serviceEntry = $this->_services[$serviceName];

        $isVendor = (isset($serviceEntry['isVendor']) && $serviceEntry['isVendor'] === true);
        $serviceClassName = $serviceEntry['name'];
        $serviceClassPath = $serviceEntry['path'];
        $serviceClassArgs = array();

        if (isset($serviceEntry['arguments'])) {
            $serviceClassArgs = $serviceEntry['arguments'];
        }

        $serviceInstance = $this->__createInstance(
            $serviceClassName,
            $serviceClassPath,
            $serviceClassArgs,
            $isVendor
        );

        if (!$shouldBeNew) {
            $this->_serviceCache->add($serviceName, $serviceInstance);
        }

        return $serviceInstance;
    }

    private function __initServices() {
        $this->_services = Configure::read('Services');

        if (is_null($this->_services)) {
            throw new ServiceConfigException(array());
        }
    }

    private function __createInstance($className, $classPath, $classArgs, $isVendor) {
        $arguments = $this->__prepareArguments($classArgs);

        $this->__addToClassMap($className, $classPath, $isVendor);

        $reflection = new \ReflectionClass($className);

        return $reflection->newInstanceArgs($arguments);
    }

    private function __addToClassMap($className, $classPath, $isVendor) {
        if ($isVendor) {
            return App::import('Vendor', $classPath);
        }

        return App::uses($className, $classPath);
    }

    /**
     * Check the arguments of the service and prepare if one among
     * it is supposed to be a service instance and not a primitive
     * type
     * @param array $arguments
     * @return mixed
     */
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