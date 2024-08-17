<?php
namespace App\Repositories\Xid;

interface XidTempRepositoryInterface
{
    public function create(array $data): void;
    public function get(array $data): ?object;
    public function delete(string $cookie): void;
}