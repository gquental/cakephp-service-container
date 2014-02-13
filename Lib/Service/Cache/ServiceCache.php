<?php
class ServiceCache {
    private $__services = array();

    /**
     * Retrieve the service from the cache
     * @param $serviceName
     * @return bool|object
     */
    public function get($serviceName) {
        if (!isset($this->__services[$serviceName])) {
            return false;
        }

        return $this->__services[$serviceName];
    }

    /**
     * Add a service to the cache
     * @param string $serviceName
     * @param object $serviceInstance
     * @return $this
     */
    public function add($serviceName, $serviceInstance) {
        $this->__services[$serviceName] = $serviceInstance;

        return $this;
    }
}