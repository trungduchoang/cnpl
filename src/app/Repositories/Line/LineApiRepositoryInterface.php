<?php
namespace App\Repositories\Line;

interface LineApiRepositoryInterface
{
    public function verifyChannelAccessToken(string $channelAccessToken): bool;
    public function getChannelAccessToken(string $channelId, string $channelAccessToken): string;
    public function sendMessageMulticast(array $userNames, array $messages, string $channelAccessToken): void;
}