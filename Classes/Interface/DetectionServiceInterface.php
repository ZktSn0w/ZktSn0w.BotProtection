<?php

namespace ZktSn0w\BotProtection\Interface;

use GuzzleHttp\Psr7\Request;

interface DetectionServiceInterface {

    public function isBot(Request $httpRequest): bool;

    public function clearCache(string $identifier): void;
}
