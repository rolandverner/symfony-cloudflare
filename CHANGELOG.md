# Changelog

All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.0.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## [1.0.0] - 2025-12-31

### Added
- Initial release of the `CloudflareProxyBundle`.
- Automatic fetching and caching of Cloudflare IP ranges (IPv4/IPv6).
- Feature toggle via `enabled` option and `CLOUDFLARE_PROXY_ENABLED` environment variable.
- Type-safe `ProxyMode` enum (append vs override).
- Interface-based design for all core services for better testability.
- `cloudflare:install` command to publish configuration.
- `cloudflare:reload` and `cloudflare:view` commands for IP management.
- Full configuration support via `config/packages/cloudflare_proxy.yaml`.

### Technical Features
- Support for **PHP 8.1 - 8.5**.
- Support for **Symfony 6.4, 7.x, and 8.x**.
- High-priority event subscriber (2000) for early request processing.
- Strict typing and `readonly` property promotion.
- Comprehensive PHPUnit test suite.
