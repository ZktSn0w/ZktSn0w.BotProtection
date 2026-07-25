<?php
declare(strict_types=1);

namespace ZktSn0w\BotProtection\Eel\Helper;

use GuzzleHttp\Psr7\Request;
use Neos\Eel\ProtectedContextAwareInterface;
use Jaybizzle\CrawlerDetect\CrawlerDetect;
use Neos\Flow\Annotations as Flow;
use ZktSn0w\BotProtection\Interface\DetectionServiceInterface;

class BotDetection implements ProtectedContextAwareInterface {

    #[Flow\Inject]
    protected DetectionServiceInterface $detectionService;
    /**
     * Detect if its a Bot / Crawler / etc.
     *
     * @return bool
     */
    public function isBot(Request $httpRequest) {
        return $this->detectionService->isBot($httpRequest);
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
