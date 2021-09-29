# Overview

Yun storage provides a layer that mediates between a user or configured storage frontend and one or several storage backends.

# Supported back-end storage
- [Aliyun OSS](https://www.aliyun.com/product/oss)
- [Tencent COS](https://cloud.tencent.com/product/cos)

# Run environment
- PHP 5.6+

# Install

### Composer

Execute the following command to get the latest version of the package:

```terminal
composer require jichangfeng/yun-storage
```

# Usage

#### Initialize

```php
try {
    //Make a storage manager instance.
    $storage = new \YunStorage\StorageManager();
    //
    //Register Aliyun OSS Storage Adapter
    //Note: The first registered storage adapter will be the default.
    $storage->registerAdapter('oss', [
        'accessKeyId' => '',
        'accessKeySecret' => '',
        'endpoint' => ''
    ]);
    //
    //Register Tencent COS Storage Adapter
    $storage->registerAdapter('cos', [
        'accessKeyId' => '',
        'accessKeySecret' => '',
        'region' => '',
        'appid' => ''
    ]);
    //
    //Set the default storage adapter name. Supported: "oss", "cos"
    $storage->setDefaultAdapter('oss');
    //
    //If your application interacts with default storage adapter.
    $storage->putObject($bucket, $object, $content);
    //
    //If your application interacts with multiple storage adapters,
    //you may use the 'adapter' method to work on a particular storage adapter.
    $storage->adapter('oss')->putObject($bucket, $object, $content);
    $storage->adapter('cos')->putObject($bucket, $object, $content);
    //
    //Directly call the storage object at the back-end of the storage adapter
    $storage->adapter()->client();
    $storage->adapter('oss')->client()->listObjects($bucket, $options);
    $storage->adapter('cos')->client()->listObjects($arg);
} catch (\Exception $e) {
    echo $e->getMessage();
}
```

#### Method

```php
try {
    //Creates bucket
    $storage->createBucket($bucket);
    //
    //Checks if a bucket exists
    $storage->doesBucketExist($bucket);
    //
    //Deletes bucket
    $storage->deleteBucket($bucket);
    //
    //Lists the Bucket
    $storage->listBuckets();
    //
    //Uploads the $content object.
    $storage->putObject($bucket, $object, $content);
    //
    //Checks if the object exists
    $storage->doesObjectExist($bucket, $object);
    //
    //Deletes a object
    $storage->deleteObject($bucket, $object);
    //
    //Deletes multiple objects in a bucket
    $storage->deleteObjects($bucket, $objects);
    //
    //Gets Object content
    $storage->getObject($bucket, $object);
    //
    //Lists the bucket's object keys
    $storage->listObjectKeys($bucket, $prefix);
    //
    // Gets the storage client, return the actual storage object
    $storage->adapter()->client();
} catch (\Exception $e) {
    echo $e->getMessage();
}
```

#### Example

```php
<?php
try {
    // Assuming $storage is initialized
    $bucket = 'yun-storage-example';
    $object = 'aa-bb/cc-dd/2021-09-26/ee-ff.json';
    $object2 = 'aa-bb/cc-dd/2021-09-26/ee-ff-2.json';
    $content = '{"type":"text", "data":{"msg":"some message"}}';
    $content2 = '{"type":"text", "data":{"msg":"other message"}}';
    $prefix = 'aa-bb/cc-dd';
    echo 'createBucket: ' . PHP_EOL;
    print_r($storage->createBucket($bucket));
    echo PHP_EOL;
    echo 'doesBucketExist: ' . PHP_EOL;
    print_r($storage->doesBucketExist($bucket));
    echo PHP_EOL;
    echo 'listBuckets: ' . PHP_EOL;
    print_r($storage->listBuckets($bucket));
    echo PHP_EOL;
    echo 'putObject: ' . PHP_EOL;
    print_r($storage->putObject($bucket, $object, $content));
    echo PHP_EOL;
    echo 'putObject2: ' . PHP_EOL;
    print_r($storage->putObject($bucket, $object2, $content2));
    echo PHP_EOL;
    echo 'doesObjectExist: ' . PHP_EOL;
    print_r($storage->doesObjectExist($bucket, $object));
    echo PHP_EOL;
    echo 'getObject: ' . PHP_EOL;
    print_r($storage->getObject($bucket, $object));
    echo PHP_EOL;
    echo 'getObject2: ' . PHP_EOL;
    print_r($storage->getObject($bucket, $object2));
    echo PHP_EOL;
    echo 'listObjectKeys: ' . PHP_EOL;
    print_r($storage->listObjectKeys($bucket, $prefix));
    echo PHP_EOL;
    echo 'deleteObject: ' . PHP_EOL;
    print_r($storage->deleteObject($bucket, $object));
    echo PHP_EOL;
    echo 'deleteObject2: ' . PHP_EOL;
    print_r($storage->deleteObject($bucket, $object2));
    echo PHP_EOL;
    echo 'deleteObjects: ' . PHP_EOL;
    print_r($storage->deleteObjects($bucket, [$object, $object2]));
    echo PHP_EOL;
    echo 'deleteBucket: ' . PHP_EOL;
    print_r($storage->deleteBucket($bucket));
    echo PHP_EOL;
} catch (\Exception $e) {
    echo 'exception: ' . $e->getCode() . ' - ' . $e->getMessage();
    echo PHP_EOL;
}
```