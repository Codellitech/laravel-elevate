# Laravel Elevate

**Enterprise-grade autonomous Laravel modernization and upgrade platform.**

## Installation

```bash
composer require codellitech/laravel-elevate
```

## Requirements

- **PHP**: ^8.2 (Required for AI and modern CLI features)
- **Laravel**: ^5.0 | ^6.0 | ^7.0 | ^8.0 | ^9.0 | ^10.0 | ^11.0 | ^12.0

## Configuration

Publish the configuration file:

```bash
php artisan vendor:publish --tag=elevate-config
```

Configure your AI provider in `.env`:

```env
ELEVATE_AI_PROVIDER=gemini
GEMINI_API_KEY=your-api-key
```

## Usage

### Modernize Application

Run the main modernization engine:

```bash
php artisan elevate
```

### Integrate Features

Automatically install enterprise features:

```bash
php artisan elevate:integrate whatsapp-otp
```

### Rollback Changes

If anything goes wrong, you can rollback to a previous state:

```bash
php artisan elevate:rollback
```

## Features

- **AI-First Architecture**: Supports OpenAI, Anthropic, Gemini, Ollama, and more.
- **Deep Project Scanning**: Analyzes PHP, Laravel, Composer, Frontend stacks, and Infrastructure.
- **Automated Upgrades**: Upgrades Laravel versions and modernizes syntax.
- **Frontend Modernization**: Bootstrap to Tailwind, Vue 2 to 3, Mix to Vite.
- **Enterprise Integrations**: WhatsApp OTP, RBAC, Audit Trails, and more.
- **Safety First**: Git-based snapshots and rollback manifests.

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information. This package is free to use for anyone.

## Credits & Support

Developed and maintained by **Codelli Technologies**.

- **Website**: [codellitech.in](https://codellitech.in)
- **Email**: info@codellitech.in
- **Phone**: +919177201462
- **Address**: Hyderabad, India

---
Built with ❤️ by [Codelli Technologies](https://codellitech.in)
