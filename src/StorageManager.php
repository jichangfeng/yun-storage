<?php

namespace YunStorage;

use YunStorage\Adapter\AliyunOssAdapter;
use YunStorage\Adapter\TencentCosAdapter;

/**
 * Storage manager instances.
 * 
 * @method \OSS\OssClient|\Qcloud\Cos\Client client() Gets the storage client, return the actual storage object
 * @method object createBucket($bucket) Creates bucket
 * @method bool doesBucketExist($bucket) Checks if a bucket exists
 * @method bool deleteBucket($bucket) Deletes bucket
 * @method array listBuckets() Lists the Bucket
 * @method object putObject($bucket, $object, $content) Uploads the $content object
 * @method string doesObjectExist($bucket, $object) Checks if the object exists
 * @method object deleteObject($bucket, $object) Deletes a object
 * @method object deleteObjects($bucket, $objects) eletes multiple objects in a bucket
 * @method string getObject($bucket, $object) Gets Object content
 * @method array listObjectKeys($bucket, $prefix) Lists the bucket's object keys
 *
 * @author Changfeng Ji <jichf@qq.com>
 */
class StorageManager {

    /**
     * Storage Adapters Config
     * 
     * @var array
     */
    private $adapterConfig = [];

    /**
     * Default Storage Adapter
     * 
     * @var string
     */
    private $adapterDefault = '';

    /**
     * The array of resolved storage adapters.
     * 
     * @var array
     */
    private $adapters;

    /**
     * Register the storage adapter
     * 
     * Note: The first registered storage adapter will be the default.
     * 
     * @param string $name The storage adapter name. Supported: "oss", "cos"
     * @param array $config The storage adapter config
     * 
     *     When the $name is oss,  the $config are defined below:
     *        [
     *            'accessKeyId' => '',
     *            'accessKeySecret' => '',
     *            'endpoint' => ''
     *        ]
     * 
     *     When the $name is cos,  the $config are defined below:
     *        [
     *            'accessKeyId' => '',
     *            'accessKeySecret' => '',
     *            'region' => '',
     *            'appid' => ''
     *        ]
     * 
     */
    public function registerAdapter($name, $config) {
        if (!$this->adapterDefault) {
            $this->adapterDefault = $name;
        }
        $this->adapterConfig[$name] = $config;
    }

    /**
     * Set the default storage adapter name.
     *
     * @param string $name The storage adapter name. Supported: "oss", "cos"
     */
    public function setDefaultAdapter($name) {
        return $this->adapterDefault = $name;
    }

    /**
     * Get the default storage adapter name.
     *
     * @return string The default storage adapter name
     */
    public function getDefaultAdapter() {
        return $this->adapterDefault;
    }

    /**
     * Get a storage adapter instance.
     * 
     * @param  string|null  $name The storage adapter name
     * @return \YunStorage\Adapter\AdapterInterface
     */
    public function adapter($name = null) {
        $name = $name ?: $this->getDefaultAdapter();
        if (!isset($this->adapterConfig[$name])) {
            throw new InvalidArgumentException("Storage adapter [{$name}] does not have a configure.");
        }
        return $this->adapters[$name] = $this->get($name);
    }

    /**
     * Attempt to get the storage adapter.
     *
     * @param  string  $name The storage adapter name
     * @return \YunStorage\Adapter\AdapterInterface
     */
    private function get($name) {
        return $this->adapters[$name] ?? $this->resolve($name);
    }

    /**
     * Resolve the given storage.
     *
     * @param  string  $name The storage adapter name
     * @return \YunStorage\Adapter\AdapterInterface
     *
     * @throws \InvalidArgumentException
     */
    private function resolve($name) {
        if (!isset($this->adapterConfig[$name])) {
            throw new InvalidArgumentException("Storage adapter [{$name}] does not have a configure.");
        }
        $config = $this->adapterConfig[$name];
        $driverMethod = 'create' . ucfirst($name) . 'Adapter';

        if (method_exists($this, $driverMethod)) {
            return $this->{$driverMethod}($config);
        } else {
            throw new InvalidArgumentException("Storage adapter [{$name}] is not supported.");
        }
    }

    /**
     * Create an instance of the oss storage adapter.
     *
     * @param  array  $config
     * @return \YunStorage\Adapter\AliyunOssAdapter
     */
    private function createOssAdapter(array $config) {
        return new AliyunOssAdapter($config);
    }

    /**
     * Create an instance of the cos storage adapter.
     *
     * @param  array  $config
     * @return \YunStorage\Adapter\TencentCosAdapter
     */
    private function createCosAdapter(array $config) {
        return new TencentCosAdapter($config);
    }

    /**
     * Dynamically call the default driver instance.
     *
     * @param  string  $method
     * @param  array  $parameters
     * @return mixed
     */
    public function __call($method, $parameters) {
        return $this->adapter()->$method(...$parameters);
    }

}
