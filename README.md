# ZktSn0w.BotProtection

Extensible Neos package that detects bots/crawlers via User-Agent.
Provides Fusion prototypes, Eel helpers, and a swappable PHP service layer.

## Architecture

```
Eel Helper (BotDetection)
    └─ injects DetectionServiceInterface
         └─ default: DetectionService (jaybizzle/crawler-detect)
              └─ wired via Configuration/Objects.yaml
```

### Interface

`ZktSn0w\BotProtection\Interface\DetectionServiceInterface` defines the contract:

- `isBot(Request $httpRequest): bool` — inspect the HTTP request, return true for bots
- `clearCache(string $identifier): void` — flush cached content by tag

Swap the implementation by overriding `Objects.yaml` — no changes needed in Fusion or Eel.

### Default service

`ZktSn0w\BotProtection\Service\DetectionService` implements the interface.

It checks every `User-Agent` header value in the request using [jaybizzle/crawler-detect](https://github.com/JayBizzle/Crawler-Detect) fot Bots / Crawlers.

### Wiring

`Configuration/Objects.yaml`:

```yaml
ZktSn0w\BotProtection\Interface\DetectionServiceInterface:
  className: 'ZktSn0w\BotProtection\Service\DetectionService'
```

### Eel helper

`ZktSn0w.BotProtection` exposes `isBot()` in Fusion. The helper injects `DetectionServiceInterface` — it delegates detection, no direct dependency on crawler-detect.

### Fusion prototype

`ZktSn0w.BotProtection:BotProtectedContent` is backed by a PHP implementation class (`BotProtectedContentImplementation`). It injects `DetectionServiceInterface`, reads the HTTP request from Fusion globals, and renders:

- `content` for regular visitors
- `fallback` for bots/crawlers

Cache is **dynamic** — the entry identifier flips between `"is-bot"` and `"not-a-bot"` based on detection result, so both variants are cached independently.

## Usage

### Fusion prototype

```fusion
body = ZktSn0w.BotProtection:BotProtectedContent {
  identifier = "my-content-block"
  content = // real content for normal visitors
  fallback = // stripped-down content for bots
}
```

`identifier` (optional) generates a custom named cache tag `zktsn0w_botprotection__my-content-block`. Use it to manually flush specific blocks:

```php
$detectionService->clearCache("my-content-block");
```

### Eel helper directly

```fusion
isBot = ${ZktSn0w.BotProtection.isBot(request.httpRequest)}
```

## Extending

### Custom detection service

Implement the interface:

```php
use ZktSn0w\BotProtection\Interface\DetectionServiceInterface;

class MyDetectionService implements DetectionServiceInterface
{
    public function isBot(Request $httpRequest): bool
    {
        // your custom logic
    }

    public function clearCache(string $identifier): void
    {
        // flush your cache
    }
}
```

Override the wiring in your own `Objects.yaml`:

```yaml
ZktSn0w\BotProtection\Interface\DetectionServiceInterface:
  className: 'Vendor\Site\Service\MyDetectionService'
```

Eel helper and Fusion prototype will pick up your service automatically — both inject the interface.

## Testing Examples

Bot UA (gets fallback):
```
curl -A "Mozilla/5.0 (compatible; Googlebot/2.1; +http://www.google.com/bot.html)" http://your-domain/path/to/page
```

Regular browser UA (gets real content):
```
curl -A "Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/120.0.0.0 Safari/537.36" http://your-domain/path/to/page
```
