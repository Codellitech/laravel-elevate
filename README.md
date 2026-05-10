<h1 align="center">Codelli Technologies</h1>
<h3 align="center">Laravel Elevate</h3>

<p align="center">
    <strong>The Autonomous AI Migration & Upgrade Platform for Laravel</strong>
</p>

<p align="center">
    <a href="https://packagist.org/packages/codellitech/laravel-elevate"><img src="https://img.shields.io/packagist/v/codellitech/laravel-elevate.svg?style=for-the-badge" alt="Latest Version on Packagist"></a>
    <a href="https://packagist.org/packages/codellitech/laravel-elevate"><img src="https://img.shields.io/packagist/dt/codellitech/laravel-elevate.svg?style=for-the-badge" alt="Total Downloads"></a>
    <a href="https://packagist.org/packages/codellitech/laravel-elevate"><img src="https://img.shields.io/packagist/l/codellitech/laravel-elevate.svg?style=for-the-badge" alt="License"></a>
</p>

---

## 💎 Why Laravel Elevate?

Upgrading legacy Laravel applications is no longer a manual nightmare. **Laravel Elevate** is an autonomous migration engine that bridges the gap between old versions and the modern framework. Whether you're on Laravel 5.8 or 10.x, Elevate brings you to the future in minutes.

### 🌟 Enterprise-Grade Features:
- **🚀 Target Upgrade Engine**: Select any version from Laravel 5.0 to 13.0 as your target.
- **🤖 Context-Aware AI**: The engine doesn't just refactor; it understands the breaking changes between your specific versions.
- **📦 Composer Elevation**: Automatically patches `composer.json` requirements and PHP version constraints.
- **🛡️ Safety First**: Built-in Git-based snapshots and a detailed **Elevation Report** for every run.
- **🏗️ Expansion Modules**: Inject features like WhatsApp OTP or RBAC with a single command.

---

## 🛠️ Installation

```bash
composer require codellitech/laravel-elevate
```

### System Requirements
- **PHP**: 8.2 or higher (Universal Support)
- **Laravel**: 5.0 through 13.x
- **Git**: Highly recommended for safety snapshots

---

## ⚙️ Quick Start

### 1. Initialize
Publish the configuration and add your AI keys to `.env`:

```bash
php artisan vendor:publish --tag="elevate-config"
```

```env
# Choose your provider: gemini, openai, claude, deepseek
ELEVATE_AI_PROVIDER=gemini
GEMINI_API_KEY=your_key_here

# SSL verification is AUTOMATICALLY handled for local development
```

### 2. Elevate your Project
Run the core engine and follow the interactive prompts to choose your upgrade path:
```bash
php artisan elevate
```

### 3. Review the Elevation Report
At the end of the process, Elevate generates a comprehensive report of all file refactors and dependency updates performed by the AI.

---

## 📖 Commands

| Command | Purpose |
| --- | --- |
| `php artisan elevate` | **Upgrade/Modernize**: The main engine for framework migration and code refactoring. |
| `php artisan elevate:integrate` | **Expand**: Automatically injects enterprise modules (WhatsApp OTP, etc). |
| `php artisan elevate:rollback` | **Revert**: Instantly return to the pre-elevation state via Git. |

---

## 🏢 About Codelli Technologies

**Laravel Elevate** is an open-source initiative by **Codelli Technologies**. We build state-of-the-art AI systems and enterprise architecture for the modern web.

- **Website**: [codellitech.in](https://codellitech.in)
- **Support**: [info@codellitech.in](mailto:info@codellitech.in) | [codellitech@gmail.com](mailto:codellitech@gmail.com)

---

## 📄 License
The MIT License (MIT). Please see [License File](LICENSE.md) for more information.

<p align="center">
  <br>
  Built with ❤️ for the Laravel Community.
</p>
