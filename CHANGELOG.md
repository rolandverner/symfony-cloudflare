# Changelog

All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.0.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## [1.0.2] - 2025-12-30

### Fixed
- Fixed bundle extension alias mismatch by overriding `getContainerExtension()`. This allows using `cloudflare_proxies` as the configuration key instead of the default `cloudflare_trusted_proxies`.

## [1.0.1] - 2025-12-30

### Added
- Initial release of the Symfony Cloudflare Trusted Proxies bundle.
- Automatic fetching of Cloudflare IPv4 and IPv6 ranges.
- Caching of IP ranges with configurable TTL.
- Event subscriber to automatically set trusted proxies.
- Console commands `cloudflare:reload` and `cloudflare:view`.
- Full configuration support via `cloudflare_proxies` namespace.
- Support for `append` and `override` modes for trusted proxies.
- Support for custom environment variables for trusted proxies.
- Unit tests and GitHub Actions CI.
- Support for PHP 8.4 and PHP 8.5 (experimental).
- Support for Symfony 8.0 (experimental).
