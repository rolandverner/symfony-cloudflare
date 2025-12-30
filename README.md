# Cloudflare Trusted Proxies Bundle for Symfony

This bundle automatically fetches Cloudflare's IP ranges (IPv4 and IPv6) and configures Symfony's trusted proxies. This ensures that `Request::getClientIp()` and other forwarded headers (like `X-Forwarded-Proto`) work correctly when your application is behind Cloudflare.

## Features

- **Automatic IP Updates**: Fetches the latest IP ranges directly from Cloudflare.
- **Caching**: IP ranges are cached using Symfony Cache to avoid overhead on every request.
- **Zero Configuration**: Works out of the box with standard Symfony environment variables (`TRUSTED_PROXIES`).
- **Flexible**: Customizable headers, caching, and merging logic.

## Installation

```bash
composer require rolandverner/symfony-cloudflare
```

## Configuration

The bundle works automatically, but you can customize it in `config/packages/cloudflare_proxies.yaml`:

```yaml
cloudflare_proxies:
    # Mode: 'append' (default) or 'override'
    # 'append' adds Cloudflare IPs to your existing TRUSTED_PROXIES
    # 'override' replaces them entirely
    mode: append

    # Optional: custom environment variable for trusted proxies
    proxies_env: CLOUDFLARE_TRUSTED_PROXIES

    # Additional proxies to trust (e.g., your local load balancer)
    extra:
        - 10.0.0.1
        - 172.16.0.0/12

    # Trusted headers configuration
    trusted_headers:
        - x-forwarded-for
        - x-forwarded-host
        - x-forwarded-proto
        - x-forwarded-port
        - forwarded

    # Cache configuration
    cache:
        pool: cache.app
        key: cloudflare_proxies_ips
        ttl: 86400 # 24 hours
```

## Usage

The bundle hooks into the `kernel.request` event with high priority (2000), so it runs before the Security component.

### Commands

You can manually reload or view the cached IP ranges:

```bash
# Force reload Cloudflare IPs into cache
php bin/console cloudflare:reload

# View currently cached IP ranges
php bin/console cloudflare:view
```

## Credits

This bundle is inspired by [monicahq/laravel-cloudflare](https://github.com/monicahq/laravel-cloudflare).

## License

MIT
