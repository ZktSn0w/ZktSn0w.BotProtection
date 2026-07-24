# ZktSn0w.BotProtection

Neos package that detects bots/crawlers via User-Agent and lets Fusion render fallback content instead of the real page body.

## How it works

- `ZktSn0w\BotProtection\Eel\Helper\BotDetection::isBot()` reads the `User-Agent` header from the current HTTP request and checks it against [jaybizzle/crawler-detect](https://github.com/JayBizzle/Crawler-Detect).
- Exposed in Eel as `ZktSn0w.BotProtection` (see `Configuration/Settings.Neos.Fusion.yaml`).
- `prototype(ZktSn0w.BotProtection:BotProtectedContent)` (in `Resources/Private/Fusion/BotProtectedContent.fusion`) wraps content and renders `fallback` for bots, `content` otherwise.

## Usage

```fusion
body = ZktSn0w.BotProtection:BotProtectedContent {
  content = ZktSn0w.Inertia:InertiaBody
  fallback = "Sorry no content for you!"
}
```

## Wiring into a package

This package's Fusion is **not** picked up automatically just by setting `Neos.Neos.fusion.autoInclude` — that setting only applies to Neos.Neos's node-based `FusionView`. For plain `Neos\Fusion\View\FusionView` controllers (e.g. `RootCaseController`), the package must be added explicitly to the consuming controller's `fusionPathPatterns`, e.g. in `ZktSn0w.Page`'s `Configuration/Settings.yaml`:

```yaml
ZktSn0w:
  Page:
    fusion:
      fusionPathPatterns:
        - 'resource://ZktSn0w.BotProtection/Private/Fusion'
```

## Testing

Bot UA (gets fallback):
```
curl -A "Mozilla/5.0 (compatible; Googlebot/2.1; +http://www.google.com/bot.html)" http://localhost/rootcase/index
```

Regular browser UA (gets real content):
```
curl -A "Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/120.0.0.0 Safari/537.36" http://localhost/rootcase/index
```
