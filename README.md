![PHP](https://img.shields.io/badge/PHP-8.1%2B-777BB4?logo=php&logoColor=white)
![Symfony](https://img.shields.io/badge/Symfony-6%20%7C%207%20%7C%208-black?logo=symfony)
![Cloudflare](https://img.shields.io/badge/Cloudflare-Trusted%20Proxies-F38020?logo=cloudflare&logoColor=white)
[![Latest Version on Packagist](https://img.shields.io/packagist/v/rolandverner/symfony-cloudflare.svg)](https://packagist.org/packages/rolandverner/symfony-cloudflare)
[![Total Downloads](https://img.shields.io/packagist/dt/rolandverner/symfony-cloudflare.svg)](https://packagist.org/packages/rolandverner/symfony-cloudflare)
[![License](https://img.shields.io/packagist/l/rolandverner/symfony-cloudflare.svg)](LICENSE)

# Cloudflare Trusted Proxies Bundle for Symfony (Real IP & Reverse Proxy Support)

A production-ready Symfony bundle for integrating Cloudflare with Symfony applications.
It ensures correct real client IP detection, trusted proxy configuration,
and forwarded headers handling when Symfony runs behind Cloudflare.

## Why use Cloudflare with Symfony?

When running Symfony behind Cloudflare, incorrect proxy configuration often causes:
- Wrong client IP addresses (`Request::getClientIp()`)
- Broken HTTPS / scheme detection
- Invalid security and rate-limiting behavior

This bundle automatically configures Symfony trusted proxies
using official Cloudflare IPv4 and IPv6 ranges and keeps them up to date.

## Features

- **Automatic IP Updates**: Automatically fetches and updates official Cloudflare IPv4 and IPv6 ranges for Symfony trusted proxies.
- **Caching**: IP ranges are cached using Symfony Cache to avoid overhead on every request.
- **Zero Configuration**: Works out of the box with standard Symfony environment variables (`TRUSTED_PROXIES`).
- **Flexible**: Customizable headers, caching, and merging logic.
- **Feature Toggle**: Easily enable or disable the bundle via config or environment variables.

## Installation

```bash
composer require rolandverner/symfony-cloudflare
```

After installation, register the bundle in your `config/bundles.php`:

```php
return [
    // ...
    Cloudflare\Proxy\CloudflareProxyBundle::class => ['all' => true],
];
```

After installation, you can automatically publish the configuration file to your project:

```bash
php bin/console cloudflare:install
```

## Compatibility

- PHP 8.1+
- Symfony 6.x
- Symfony 7.x
- Symfony 8.x
- Cloudflare (Free, Pro, Business, Enterprise)

## Configuration

```yaml
cloudflare_proxy:
    # Whether to enable the Cloudflare trusted proxies listener
    enabled: true

    # Mode: 'append' (default) or 'override'
    # 'append' adds Cloudflare IPs to your existing TRUSTED_PROXIES
    # 'override' replaces them entirely
    mode: append

    # Optional: custom environment variable for trusted proxies
    proxy_env: CLOUDFLARE_TRUSTED_PROXIES

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
        key: cloudflare_proxy.ips
        ttl: 86400 # 24 hours
```
cloudflare_proxy:
    enabled: true
    mode: append
    proxy_env: CLOUDFLARE_TRUSTED_PROXIES
    extra: []
    trusted_headers:
        - x-forwarded-for
        - x-forwarded-host
        - x-forwarded-proto
        - x-forwarded-port
        - forwarded
    cache:
        pool: cache.app
        key: cloudflare_proxy.ips
        ttl: 86400
```

## Environment Variables

By default, the bundle will automatically merge Cloudflare IPs with any proxies defined in your standard Symfony environment variable:

```bash
# .env
TRUSTED_PROXIES=127.0.0.1,10.0.0.1

# Optional: Disable the bundle features
CLOUDFLARE_PROXY_ENABLED=false
```

If you prefer to use a custom variable name, you can change `proxy_env` in the configuration.

## Usage

The bundle hooks into the `kernel.request` event with high priority (2000),
so it runs before the Security component.

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
