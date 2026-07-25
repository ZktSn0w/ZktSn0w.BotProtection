<?php

namespace ZktSn0w\BotProtection\Service;

use GuzzleHttp\Psr7\Request;
use Jaybizzle\CrawlerDetect\CrawlerDetect;
use Neos\Fusion\Core\Cache\ContentCache;
use Psr\Log\LoggerInterface;
use ZktSn0w\BotProtection\Interface\DetectionServiceInterface;
use Neos\Flow\Annotations as Flow;

class DetectionService implements DetectionServiceInterface
{
    #[Flow\Inject]
    protected ContentCache $contentCache;

    #[Flow\Inject]
    protected LoggerInterface $systemLogger;


    public function isBot(Request $httpRequest): bool
    {
        $crawlerDetect = new CrawlerDetect();
        if ($httpRequest->hasHeader("User-Agent")) {
            $headers = $httpRequest->getHeader("User-Agent");
            return array_any($headers, fn($header) => $crawlerDetect->isCrawler($header));
        }

        return false;
    }

    public function clearCache(string $identifier): void {
        $this->contentCache->flushByTag("zktsn0w_botprotection__" . $identifier);
    }
}
