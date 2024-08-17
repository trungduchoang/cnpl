<?php
namespace App\Repositories\Ses;

interface SesApiRepositoryInterface
{
    public function sendEmail(array $data);
}