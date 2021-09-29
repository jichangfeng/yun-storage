<?php

namespace YunStorage\Adapter;

use \OSS\OssClient;

/**
 * Interface for adapters managing instances.
 *
 * @author Changfeng Ji <jichf@qq.com>
 */
class AliyunOssAdapter implements AdapterInterface {

    /**
     *
     * @var array
     */
    private $config = [
        'accessKeyId' => '',
        'accessKeySecret' => '',
        'endpoint' => ''
    ];

    /**
     *
     * @var \OSS\OssClient
     */
    private $client;

    /**
     * 
     * @param array $config are defined below:
     * $config = array(
     *      'accessKeyId  => The AccessKeyId from OSS or STS
     *      'accessKeySecret'    => The AccessKeySecret from OSS or STS
     *      'endpoint' => The domain name of the datacenter,For example: oss-cn-hangzhou.aliyuncs.com
     * )
     */
    public function __construct($config) {
        $this->config = $config;
        if (empty($this->config['accessKeyId']) || empty($this->config['accessKeySecret']) || empty($this->config['endpoint'])) {
            throw new \Exception("阿里云存储缺少配置参数");
        }
        $this->client = new OssClient($this->config['accessKeyId'], $this->config['accessKeySecret'], $this->config['endpoint']);
    }

    /**
     * Gets the storage client, return the actual storage object
     *
     * @return \OSS\OssClient|\Qcloud\Cos\Client
     */
    public function client() {
        return $this->client;
    }

    /**
     * Creates bucket
     *
     * @param string $bucket bucket name
     * @return object
     */
    public function createBucket($bucket) {
        return $this->client->createBucket($bucket);
    }

    /**
     * Checks if a bucket exists
     *
     * @param string $bucket bucket name
     * @return bool
     */
    public function doesBucketExist($bucket) {
        return $this->client->doesBucketExist($bucket);
    }

    /**
     * Deletes bucket
     *
     * @param string $bucket bucket name
     * @return object
     */
    public function deleteBucket($bucket) {
        return $this->client->deleteBucket($bucket);
    }

    /**
     * Lists the Bucket
     *
     * @return array
     */
    public function listBuckets() {
        $bucketListInfo = $this->client->listBuckets();
        $bucketList = $bucketListInfo->getBucketList();
        $buckets = [];
        foreach ($bucketList as $bucket) {
            $buckets[] = $bucket->getName();
        }
        return $buckets;
    }

    /**
     * Uploads the $content object.
     *
     * @param string $bucket bucket name
     * @param string $object object name
     * @param string $content The content object
     * @return object
     */
    public function putObject($bucket, $object, $content) {
        return $this->client->putObject($bucket, $object, $content);
    }

    /**
     * Checks if the object exists
     *
     * @param string $bucket bucket name
     * @param string $object object name
     * @return bool
     */
    public function doesObjectExist($bucket, $object) {
        return $this->client->doesObjectExist($bucket, $object);
    }

    /**
     * Deletes a object
     *
     * @param string $bucket bucket name
     * @param string $object object name
     * @return object
     */
    public function deleteObject($bucket, $object) {
        return $this->client->deleteObject($bucket, $object);
    }

    /**
     * Deletes multiple objects in a bucket
     *
     * @param string $bucket bucket name
     * @param array $objects object list
     * @return object
     */
    public function deleteObjects($bucket, $objects) {
        return $this->client->deleteObjects($bucket, $objects);
    }

    /**
     * Gets Object content
     *
     * @param string $bucket bucket name
     * @param string $object object name
     * @return string
     */
    public function getObject($bucket, $object) {
        return $this->client->getObject($bucket, $object);
    }

    /**
     * Lists the bucket's object keys
     *
     * @param string $bucket bucket name
     * @param string $prefix object key prefix
     * @return array
     */
    public function listObjectKeys($bucket, $prefix) {
        $keys = [];
        $nextMarker = '';
        while (true) {
            $options = array(
                'delimiter' => '',
                'prefix' => $prefix,
                'marker' => $nextMarker,
            );
            $listObjectInfo = $this->client->listObjects($bucket, $options);
            $nextMarker = $listObjectInfo->getNextMarker();
            $listObject = $listObjectInfo->getObjectList();
            if (!empty($listObject)) {
                foreach ($listObject as $objectInfo) {
                    $keys[] = $objectInfo->getKey();
                }
            }
            if ($listObjectInfo->getIsTruncated() !== "true") {
                break;
            }
        }
        return $keys;
    }

}
