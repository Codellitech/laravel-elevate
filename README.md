<h1 align="center">Codelli Technologies</h1>
<h3 align="center">Laravel Elevate</h3>

<p align="center">
    <strong>Autonomous AI-Driven Modernization & Enterprise Expansion Platform</strong>
</p>

<p align="center">
    <a href="https://packagist.org/packages/codellitech/laravel-elevate"><img src="https://img.shields.io/packagist/v/codellitech/laravel-elevate.svg?style=for-the-badge" alt="Latest Version on Packagist"></a>
    <a href="https://packagist.org/packages/codellitech/laravel-elevate"><img src="https://img.shields.io/packagist/dt/codellitech/laravel-elevate.svg?style=for-the-badge" alt="Total Downloads"></a>
    <a href="https://packagist.org/packages/codellitech/laravel-elevate"><img src="https://img.shields.io/packagist/l/codellitech/laravel-elevate.svg?style=for-the-badge" alt="License"></a>
</p>

---

## 💎 Why Laravel Elevate?

Maintaining legacy Laravel applications is expensive, time-consuming, and risky. **Laravel Elevate** changes the game by bringing autonomous AI to your terminal. It doesn't just scan; it **refactors**, **upgrades**, and **expands** your application with enterprise-grade precision.

### 🌟 Features that Win Trust:
- **🤖 AI-First Engine**: Uses state-of-the-art LLMs (Gemini 1.5 Pro, GPT-4, Claude 3) to rewrite code intelligently.
- **⚡ Universal Bridge**: Instantly upgrade code from Laravel 5.x to 12.x without breaking a sweat.
- **🛡️ Safety Snapshot**: Automatic Git-based rollback system. If the AI makes a mistake, one command brings everything back.
- **🏗️ Enterprise Modules**: Automatically inject complex features like WhatsApp OTP, RBAC, and Audit Trails in seconds.
- **🔍 Deep Insights**: Comprehensive project scanning for backend, frontend, and infrastructure stacks.

---

## 🛠️ Installation

Elevate your project in seconds:

```bash
composer require codellitech/laravel-elevate
```

### System Requirements
- **PHP**: 8.2 or higher
- **Laravel**: 5.0 through 12.x (Universal Support)
- **Git**: Required for safety snapshots

---

## ⚙️ Quick Start

### 1. Configure your AI
Publish the configuration and add your API keys to `.env`:

```bash
php artisan vendor:publish --tag="elevate-config"
```

```env
# Choose your brain: gemini, openai, claude, deepseek, ollama
ELEVATE_AI_PROVIDER=gemini
GEMINI_API_KEY=your_key_here

# NOTE: SSL verification is AUTOMATICALLY disabled on 'local' 
# environments to ensure a smooth developer experience.
```

### 2. Run the Modernizer
Experience the magic of AI-driven refactoring:
```bash
php artisan elevate
```

### 3. Inject Features
Need WhatsApp OTP? Don't code it, **Elevate** it:
```bash
php artisan elevate:integrate whatsapp-otp
```

---

## 📖 Available Commands

| Command | Purpose |
| --- | --- |
| `php artisan elevate` | **Modernize**: Scans and refactors legacy code to modern standards. |
| `php artisan elevate:integrate` | **Expand**: Automatically injects enterprise modules and integrations. |
| `php artisan elevate:rollback` | **Safety**: Instantly reverts the codebase to the pre-elevation state. |

---

## 🔒 Security & Peace of Mind

We know your codebase is your most valuable asset. That's why Elevate follows a **Safety-First** philosophy:
1. **Snapshots**: Every run creates a new Git branch.
2. **Dry Run**: Use `--dry-run` to see exactly what the AI will change before it touches a single line.
3. **Reasoning**: After every modification, the AI provides a reasoning report explaining *why* it made the changes.

---

## 🏢 About Codelli Technologies

**Laravel Elevate** is an open-source initiative by **Codelli Technologies**. We specialize in high-end enterprise software, AI systems engineering, and DevOps architecture.

- **Website**: [codellitech.in](https://codellitech.in)
- **Support**: [info@codellitech.in](mailto:info@codellitech.in)
- **Phone**: +91 91772 01462

---

## 📄 License
The MIT License (MIT). Please see [License File](LICENSE.md) for more information.

<p align="center">
  <br>
  Built with ❤️ for the Laravel Community.
</p>
