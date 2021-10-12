<?php

namespace YunStorage\Adapter;

use \Qcloud\Cos\Client;

/**
 * Interface for adapters managing instances.
 *
 * @author Changfeng Ji <jichf@qq.com>
 */
class TencentCosAdapter implements AdapterInterface {

    /**
     *
     * @var array
     */
    private $config = [
        'accessKeyId' => '',
        'accessKeySecret' => '',
        'region' => '',
        'schema' => '',
        'appid' => ''
    ];

    /**
     *
     * @var \Qcloud\Cos\Client
     */
    private $client;

    /**
     * 
     * @param array $config are defined below:
     * $config = array(
     *      'accessKeyId  => The AccessKeyId
     *      'accessKeySecret'    => The AccessKeySecret
     *      'region' => The region,
     *      'schema' => The schema,
     *      'appid' => The appid
     * )
     */
    public function __construct($config) {
        $this->config = $config;
        if (empty($this->config['accessKeyId']) || empty($this->config['accessKeySecret']) || empty($this->config['region']) || empty($this->config['appid'])) {
            throw new \Exception("腾讯云存储缺少配置参数");
        }
        $this->client = new Client([
            'region' => $this->config['region'],
            'schema' => isset($this->config['schema']) && $this->config['schema'] ? $this->config['schema'] : 'http',
            'credentials' => [
                'secretId' => $this->config['accessKeyId'],
                'secretKey' => $this->config['accessKeySecret']
            ]]
        );
    }

    /**
     * Gets the storage client, return the actual storage object
     *
     * @return \YunStorage\Adapter\AdapterInterface
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
        $bucket .= '-' . $this->config['appid'];
        return $this->client->CreateBucket([
                    'Bucket' => $bucket
        ]);
    }

    /**
     * Checks if a bucket exists
     *
     * @param string $bucket bucket name
     * @return bool
     */
    public function doesBucketExist($bucket) {
        $bucket .= '-' . $this->config['appid'];
        return $this->client->doesBucketExist($bucket);
    }

    /**
     * Deletes bucket
     *
     * @param string $bucket bucket name
     * @return object
     */
    public function deleteBucket($bucket) {
        $bucket .= '-' . $this->config['appid'];
        return $this->client->deleteBucket([
                    'Bucket' => $bucket
        ]);
    }

    /**
     * Lists the Bucket
     *
     * @return array
     */
    public function listBuckets() {
        $bucketListInfo = $this->client->listBuckets();
        $bucketList = $bucketListInfo['Buckets'];
        $buckets = [];
        if (isset($bucketList[0]['Bucket']) && isset($bucketList[0]['Bucket'][0]['Name'])) {
            $appidLen = strlen($this->config['appid']);
            foreach ($bucketList[0]['Bucket'] as $bucket) {
                $buckets[] = substr($bucket['Name'], 0, ($appidLen + 1) * -1);
            }
            return $buckets;
        }
        return [];
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
        $bucket .= '-' . $this->config['appid'];
        return $this->client->putObject([
                    'Bucket' => $bucket,
                    'Key' => $object,
                    'Body' => $content
        ]);
    }

    /**
     * Checks if the object exists
     *
     * @param string $bucket bucket name
     * @param string $object object name
     * @return bool
     */
    public function doesObjectExist($bucket, $object) {
        $bucket .= '-' . $this->config['appid'];
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
        $bucket .= '-' . $this->config['appid'];
        return $this->client->deleteObject([
                    'Bucket' => $bucket,
                    'Key' => $object
        ]);
    }

    /**
     * Deletes multiple objects in a bucket
     *
     * @param string $bucket bucket name
     * @param array $objects object list
     * @return object
     */
    public function deleteObjects($bucket, $objects) {
        $bucket .= '-' . $this->config['appid'];
        $list = [];
        foreach ($objects as $object) {
            $list[] = ['Key' => $object];
        }
        return $this->client->deleteObjects([
                    'Bucket' => $bucket,
                    'Objects' => $list
        ]);
    }

    /**
     * Gets Object content
     *
     * @param string $bucket bucket name
     * @param string $object object name
     * @return string
     */
    public function getObject($bucket, $object) {
        $bucket .= '-' . $this->config['appid'];
        $result = $this->client->getObject([
            'Bucket' => $bucket,
            'Key' => $object
        ]);
        $size = $result['Body']->getSize();
        if ($size === 0) {
            return '';
        }
        return $result['Body']->read($size);
    }

    /**
     * Lists the bucket's object keys
     *
     * @param string $bucket bucket name
     * @param string $prefix object key prefix
     * @return array
     */
    public function listObjectKeys($bucket, $prefix) {
        $bucket .= '-' . $this->config['appid'];
        $keys = [];
        $nextMarker = '';
        while (true) {
            $result = $this->client->listObjects([
                'Bucket' => $bucket,
                'Marker' => $nextMarker,
                'MaxKeys' => 1000,
                'Prefix' => $prefix
            ]);
            if (isset($result['Contents'])) {
                foreach ($result['Contents'] as $rt) {
                    $keys[] = $rt['Key'];
                }
            }
            $nextMarker = $result['NextMarker'];
            if (!$result['IsTruncated']) {
                break;
            }
        }
        return $keys;
    }

    /**
     * Uploads a local file
     *
     * @param string $bucket bucket name
     * @param string $object object name
     * @param string $localfile local file path
     * @return object
     */
    public function uploadFile($bucket, $object, $localfile) {
        $bucket .= '-' . $this->config['appid'];
        return $this->client->upload($bucket, $object, fopen($localfile, 'rb'));
    }

    /**
     * Downloads to local file
     *
     * @param string $bucket bucket name
     * @param string $object object name
     * @param string $localfile local file path
     * @return object
     */
    public function downloadFile($bucket, $object, $localfile) {
        $bucket .= '-' . $this->config['appid'];
        return $this->client->download($bucket, $object, $localfile);
    }

}
