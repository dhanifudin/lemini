# Installation Guide

This document outlines the steps to install and configure the necessary libraries for the project.

## 1. Install Gemini PHP Client

We use the `google-gemini-php/laravel` package to interact with the Gemini API. Install it using Composer:

```bash
composer require google-gemini-php/laravel
```

## 2. Configure Gemini Environment Variables

Add the following keys to your `.env` file. Refer to `GEMINI.md` for appropriate values.

```dotenv
GEMINI_API_KEY=your-api-key
GEMINI_MODEL=text-embedding-004
GEMINI_MODEL_TEXT=gemini-1.5-pro
GEMINI_TIMEOUT=12
GEMINI_MAX_OUTPUT_TOKENS=512
GEMINI_SAFETY_CATEGORY_BLOCK=harassment,hate,sexual,medical,legal
```

## 3. Install Filament

Filament is used for the admin panel. Install it using Composer:

```bash
composer require filament/filament:"^4.0"
```

After installation, run the setup command:

```bash
php artisan filament:install --panels
```

## 4. Create an Admin User

To access the Filament admin panel, you need a user account. Create one with this command and follow the prompts:

```bash
php artisan make:filament-user
```

After completing these steps, you can access the admin panel at the `/admin` URL of your application.
