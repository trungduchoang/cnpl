<?php

namespace App\Repositories\Xid;


interface XidTokenRepositoryInterface
{
    public function create(array $data): void;
    public function get(string $cookie, int $projectId): object;
}