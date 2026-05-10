# Laravel Elevate

[![Latest Version on Packagist](https://img.shields.io/packagist/v/codellitech/laravel-elevate.svg?style=flat-square)](https://packagist.org/packages/codellitech/laravel-elevate)
[![Total Downloads](https://img.shields.io/packagist/dt/codellitech/laravel-elevate.svg?style=flat-square)](https://packagist.org/packages/codellitech/laravel-elevate)
[![License](https://img.shields.io/packagist/l/codellitech/laravel-elevate.svg?style=flat-square)](https://packagist.org/packages/codellitech/laravel-elevate)

**Laravel Elevate** is an enterprise-grade autonomous modernization and upgrade platform. Powered by state-of-the-art AI, it automatically refactors legacy code, upgrades Laravel versions, and integrates advanced enterprise features with zero manual effort.

---

## 🚀 Key Features

- **AI-Driven Modernization**: Automatically refactor legacy PHP/Laravel syntax to modern standards.
- **Deep Project Scanning**: Intelligent analysis of backend, frontend, and infrastructure stacks.
- **Enterprise Integrations**: One-command injection of complex modules (e.g., WhatsApp OTP, RBAC).
- **Universal Compatibility**: Supports Laravel versions from **5.0 all the way to 12.0+**.
- **Safety First**: Built-in Git-based snapshot and rollback engine for non-destructive upgrades.
- **Multi-AI Support**: Works with OpenAI, Claude, Gemini, DeepSeek, Groq, Ollama, and more.

---

## 📦 Installation

Install the package via Composer:

```bash
composer require codellitech/laravel-elevate
```

### Requirements

- **PHP**: ^8.2 (Required for AI and modern CLI features)
- **Laravel**: ^5.0 | ^6.0 | ^7.0 | ^8.0 | ^9.0 | ^10.0 | ^11.0 | ^12.0

---

## ⚙️ Configuration

### 1. Publish Configuration
```bash
php artisan vendor:publish --tag="elevate-config"
```

### 2. Configure Environment (.env)
Add your preferred AI provider keys to your `.env` file:

```env
# Primary AI Provider (openai, gemini, claude, deepseek, etc.)
ELEVATE_AI_PROVIDER=gemini

# Google Gemini (Highly Recommended)
GEMINI_API_KEY=your_gemini_key_here

# OpenAI
OPENAI_API_KEY=your_openai_key_here

# Anthropic Claude
ANTHROPIC_API_KEY=your_anthropic_key_here

# DeepSeek
DEEPSEEK_API_KEY=your_deepseek_key_here

# Local Development (WAMP/Windows)
# If you get SSL certificate errors on local, set this to false:
ELEVATE_SSL_VERIFY=false
```

---

## 🎮 Usage

### Modernize Your Application
Analyze your entire codebase and apply AI-driven refactoring:
```bash
php artisan elevate
```
*Use `--dry-run` to see proposed changes without applying them.*

### Integrate Enterprise Modules
Inject pre-built, production-ready modules into your app:
```bash
php artisan elevate:integrate
```
Available modules include:
- `whatsapp-otp`: Automated WhatsApp OTP authentication system.
- `rbac`: Role-Based Access Control (Coming Soon).
- `audit-trails`: System-wide activity logging (Coming Soon).

### Safety & Rollback
If you are unhappy with the AI changes, you can instantly revert to the pre-elevation state:
```bash
php artisan elevate:rollback
```

---

## 🛡️ Security & Safety
- **Git Snapshots**: Before any modification, Elevate creates a temporary Git branch (`elevate-backup-TIMESTAMP`).
- **Non-Destructive**: AI only modifies files within your configured `paths` (default: `app`, `routes`, `config`, etc.).
- **Excluded Paths**: Directories like `vendor` and `node_modules` are automatically ignored.

---

## 🤝 Support & Contribution

Built with ❤️ by **[Codelli Technologies](https://codellitech.in)**.

- **Website**: [codellitech.in](https://codellitech.in)
- **Email**: [info@codellitech.in](mailto:info@codellitech.in)
- **Support**: +91 91772 01462

For bugs and feature requests, please [open an issue on GitHub](https://github.com/Codellitech/laravel-elevate/issues).

---

## 📄 License
The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
