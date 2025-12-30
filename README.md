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

After installation, you can automatically publish the configuration file to your project:

```bash
php bin/console cloudflare:install
```

## Configuration

You can customize the bundle in `config/packages/cloudflare_proxies.yaml`:

```yaml
cloudflare_proxies:
    # Mode: 'append' (default) or 'override'
    # 'append' adds Cloudflare IPs to your existing TRUSTED_PROXIES
    # 'override' replaces them entirely
    mode: append

    # Optional: custom environment variable for trusted proxies
    proxies_env: CLOUDFLARE_TRUSTED_PROXIES

    # Additional proxies to trust (e.g., your local load balancer)
    extra: []
    # Example:
    # extra:
    #     - 10.0.0.1
    #     - 172.16.0.0/12

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
# Install the default configuration file
php bin/console cloudflare:install

# Force reload Cloudflare IPs into cache
php bin/console cloudflare:reload

# View currently cached IP ranges
php bin/console cloudflare:view
```

> [!TIP]
> While the bundle refreshes the cache automatically on request, it is recommended to set up a cron job to keep the cache warm:
> ```bash
> 0 */12 * * * php /path/to/your/project/bin/console cloudflare:reload > /dev/null 2>&1
> ```

## Credits

This bundle is inspired by [monicahq/laravel-cloudflare](https://github.com/monicahq/laravel-cloudflare).

## License

MIT
