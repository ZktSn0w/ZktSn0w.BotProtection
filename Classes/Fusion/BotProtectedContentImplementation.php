<?php

namespace ZktSn0w\BotProtection\Fusion;

use Neos\Flow\Mvc\ActionRequest;
use Neos\Fusion\FusionObjects\AbstractFusionObject;
use Neos\Flow\Annotations as Flow;
use ZktSn0w\BotProtection\Interface\DetectionServiceInterface;

class BotProtectedContentImplementation extends AbstractFusionObject {
    #[Flow\Inject]
    protected DetectionServiceInterface $detectionService;

    public function evaluate() {
        $request = $this->runtime->fusionGlobals->get('request');
        $fallback = $this->fusionValue('fallback');
        $content = $this->fusionValue('content');
        $isBot = false;

        if($request instanceof ActionRequest) {
            $httpRequest = $request->getHttpRequest();

            if($httpRequest) {
                $isBot = $this->detectionService->isBot($httpRequest);
            }
        }

        return $isBot ? $fallback : $content;
    }
}
