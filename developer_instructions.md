# Developer Instructions: Shipping Laravel Elevate

Congratulations! Your enterprise-grade package is ready. Follow these steps to publish it to GitHub and Packagist.

## 1. Prepare for GitHub

First, initialize a git repository in your package folder and push it to GitHub.

```bash
# Navigate to the package directory
cd c:/wamp/www/laravel-packages/laravel-elevate

# Initialize git
git init

# Add all files
git add .

# Initial commit
git commit -m "feat: initial release of laravel-elevate"

# Create a new repository on GitHub (via web UI) named 'laravel-elevate'
# Then link it and push
git remote add origin https://github.com/Codellitech/laravel-elevate.git
git branch -M main
git push -u origin main
```

## 2. Tag your first release

Composer uses git tags to determine version numbers.

```bash
git tag v1.0.0
git push origin v1.0.0
```

## 3. Register on Packagist

To make it installable via `composer require codellitech/laravel-elevate`:

1.  Go to [Packagist.org](https://packagist.org/).
2.  Log in (create an account if needed).
3.  Click **Submit** and paste your GitHub repository URL: `https://github.com/Codellitech/laravel-elevate`.
4.  Packagist will automatically fetch your package details and tags.

## 4. Setup Auto-Updating (Optional but Recommended)

To ensure Packagist updates every time you push to GitHub:

1.  On your GitHub repository, go to **Settings** > **Webhooks**.
2.  Follow the Packagist instructions to add a webhook.

## 5. Local Development Testing

If you want to test the package in a local Laravel project without publishing it to Packagist first:

1.  Open the `composer.json` of your **test project**.
2.  Add a `repositories` block:

```json
"repositories": [
    {
        "type": "path",
        "url": "../laravel-packages/laravel-elevate"
    }
],
```

3.  Run:

```bash
composer require codellitech/laravel-elevate:@dev
```

## 6. Support & Maintenance

- **Adding new AI Providers**: Create a new class in `src/AI/Drivers` implementing `AIDriver` and register it in `AIManager.php`.
- **Adding new Modules**: Create a new class in `src/Integrations` and update `IntegrateCommand.php`.

---
Built with ❤️ by [Codelli Technologies](https://codellitech.in)
