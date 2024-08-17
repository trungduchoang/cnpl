<?php
namespace App\Repositories\Ses;

use Aws\Ses\Exception\SesException;
use Aws\Ses\SesClient;

class SesApiRepository implements SesApiRepositoryInterface
{

    private $ses;

    public function __construct(SesClient $ses)
    {
        $this->ses = $ses;
    }


    public function sendEmail(array $data)
    {
        try {
            $this->ses->sendEmail($data);
        } catch (SesException $e) {
            throw new \Exception($e->getMessage(), $e->getCode());
        }
    }
}