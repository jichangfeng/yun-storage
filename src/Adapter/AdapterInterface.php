<?php

namespace YunStorage\Adapter;

/**
 * Interface for adapters managing instances.
 *
 * @author Changfeng Ji <jichf@qq.com>
 */
interface AdapterInterface {

    /**
     * Gets the storage client, return the actual storage object
     *
     * @return \OSS\OssClient|\Qcloud\Cos\Client
     */
    public function client();

    /**
     * Creates bucket
     *
     * @param string $bucket bucket name
     * @return object
     */
    public function createBucket($bucket);

    /**
     * Checks if a bucket exists
     *
     * @param string $bucket bucket name
     * @return bool
     */
    public function doesBucketExist($bucket);

    /**
     * Deletes bucket
     *
     * @param string $bucket bucket name
     * @return object
     */
    public function deleteBucket($bucket);

    /**
     * Lists the Bucket
     *
     * @return array
     */
    public function listBuckets();

    /**
     * Uploads the $content object
     *
     * @param string $bucket bucket name
     * @param string $object object name
     * @param string $content The content object
     * @return object
     */
    public function putObject($bucket, $object, $content);

    /**
     * Checks if the object exists
     *
     * @param string $bucket bucket name
     * @param string $object object name
     * @return bool
     */
    public function doesObjectExist($bucket, $object);

    /**
     * Deletes a object
     *
     * @param string $bucket bucket name
     * @param string $object object name
     * @return object
     */
    public function deleteObject($bucket, $object);

    /**
     * Deletes multiple objects in a bucket
     *
     * @param string $bucket bucket name
     * @param array $objects object list
     * @return object
     */
    public function deleteObjects($bucket, $objects);

    /**
     * Gets Object content
     *
     * @param string $bucket bucket name
     * @param string $object object name
     * @return string
     */
    public function getObject($bucket, $object);

    /**
     * Lists the bucket's object keys
     *
     * @param string $bucket bucket name
     * @param string $prefix object key prefix
     * @return array
     */
    public function listObjectKeys($bucket, $prefix);
}
