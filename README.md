# ZktSn0w.BotProtection

Neos package that detects bots/crawlers via User-Agent and lets Fusion render fallback content instead of the real page body.

## How it works

- `ZktSn0w\BotProtection\Eel\Helper\BotDetection::isBot()` reads the `User-Agent` header from the current HTTP request and checks it against [jaybizzle/crawler-detect](https://github.com/JayBizzle/Crawler-Detect).
- Exposed in Eel as `ZktSn0w.BotProtection` (see `Configuration/Settings.Neos.Fusion.yaml`).
- `prototype(ZktSn0w.BotProtection:BotProtectedContent)` (in `Resources/Private/Fusion/BotProtectedContent.fusion`) wraps content and renders `fallback` for bots, `content` otherwise.

## Usage

```fusion
body = ZktSn0w.BotProtection:BotProtectedContent {
  content = // Add your content
  fallback = // The fallback content the bot should see
}
```

## Testing

Bot UA (gets fallback):
```
curl -A "Mozilla/5.0 (compatible; Googlebot/2.1; +http://www.google.com/bot.html)" http://your-domain/path/to/page
```

Regular browser UA (gets real content):
```
curl -A "Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/120.0.0.0 Safari/537.36" http://your-domain/path/to/page
```
