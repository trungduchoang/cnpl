<?php
namespace App\Repositories\S3;

interface S3ApiRepositoryInterface
{
   public function getObject($bucket, $pass);
}