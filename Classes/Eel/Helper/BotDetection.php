<?php
declare(strict_types=1);

namespace ZktSn0w\BotProtection\Eel\Helper;

use GuzzleHttp\Psr7\Request;
use Neos\Eel\ProtectedContextAwareInterface;
use Jaybizzle\CrawlerDetect\CrawlerDetect;

class BotDetection implements ProtectedContextAwareInterface {

    /**
     * Detect if its a Bot / Crawler / etc.
     *
     * @return bool
     */
    public function isBot(Request $httpRequest) {
        $crawlerDetect = new CrawlerDetect();
        if($httpRequest->hasHeader("User-Agent")) {
            $headers = $httpRequest->getHeader("User-Agent");
            return array_any($headers, fn($header) => $crawlerDetect->isCrawler($header));
        }

        return false;
    }

    /**
     * All methods are considered safe, i.e. can be executed from within Eel
     *
     * @return boolean
     */
    public function allowsCallOfMethod($methodName): bool {
        return true;
    }
}
