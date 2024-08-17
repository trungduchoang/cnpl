<?php

namespace App\Repositories\S3;

use Aws\S3\S3Client;
use Aws\S3\Exception\S3Exception;

class S3ApiRepository implements S3ApiRepositoryInterface
{

    private $s3Client;

    public function __construct(S3Client $s3Client)
    {
        $this->s3Client = $s3Client;
    }


    public function getObject($bucket, $pass)
    {
        try {
            return $this->s3Client->getObject([
                'Bucket' => $bucket,
                'Key'    => $pass,
            ]);
        } catch (S3Exception $e) {
            throw new \Exception($e->getMessage(), $e->getCode());
        }
    }
}