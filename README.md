# Filament Email Templates Designer

[![Latest Version on Packagist](https://img.shields.io/packagist/v/notebrainslab/filament-email-templates.svg?style=flat-square)](https://packagist.org/packages/notebrainslab/filament-email-templates)
[![License](https://img.shields.io/github/license/notebrainslab/filament-email-templates.svg?style=flat-square)](LICENSE.md)
[![PHP](https://img.shields.io/badge/PHP-%5E8.2-blue?style=flat-square)](https://php.net)
[![Filament](https://img.shields.io/badge/Filament-v4%20%7C%20v5-orange?style=flat-square)](https://filamentphp.com)

A sleek, powerful, and professional visual email designer for **Filament v4 and v5**. Build beautiful, pixel-perfect responsive email layouts using an integrated [Unlayer](https://unlayer.com) drag-and-drop editor and integrate them into your own Laravel Mailables with ease.

---

## ✨ Features

- 🎨 **Visual Drag-and-Drop Editor** — Integrated Unlayer editor for professional design without writing HTML/CSS.
- 🌙 **Dark Mode Support** — Fully reactive dark/light theme that syncs with your Filament panel's theme toggle in real time.
- 🏷️ **Smart Merge Tags** — Dynamic variable support (e.g., `{{user_name}}`) with robust injection logic for both the subject and body.
- 🧩 **Mailable Integration** — Use the `HasEmailTemplate` trait to power any standard Laravel Mailable from your visual templates.
- 📂 **Key-Based Management** — Organize your library with unique programmatic keys (e.g. `auth.welcome`, `order.failed`).
- 🛡️ **Resilient Rendering** — Automatic HTML cleanup and formatting to ensure designs look great in all mail clients.
- 🧭 **Fully Configurable Navigation** — Customize the navigation group, icon, sort order, and badge visibility from your Panel Provider.
- 🧹 **Clean Architecture** — Design-first, no magic event listeners or forced overrides.

---

## 🚀 Installation

Install the package via Composer:

```bash
composer require notebrainslab/filament-email-templates
```

Run the install command to publish migrations and config:

```bash
php artisan filament-email-templates:install
```

Run migrations:

```bash
php artisan migrate
```

---

## ⚙️ Configuration

### 1. Register the Plugin

Add the plugin to your Filament Panel Provider (e.g. `app/Providers/Filament/AdminPanelProvider.php`):

```php
use NoteBrainsLab\FilamentEmailTemplates\FilamentEmailTemplatesPlugin;

public function panel(Panel $panel): Panel
{
    return $panel
        ->plugins([
            FilamentEmailTemplatesPlugin::make(),
        ]);
}
```

### 2. Plugin Options

All options are optional — the plugin works out of the box with sensible defaults.

```php
FilamentEmailTemplatesPlugin::make()
    ->navigationGroup('Content')           // Default: 'Email Templates'
    ->navigationIcon('heroicon-o-envelope-open') // Default: heroicon-o-envelope-open
    ->navigationSort(5)                    // Default: 1
    ->navigationBadge(true),              // Default: true — shows template count badge
```

| Method | Type | Default | Description |
|---|---|---|---|
| `navigationGroup(string)` | `string` | `'Email Templates'` | The sidebar group label |
| `navigationIcon(string)` | `string` | `heroicon-o-envelope-open` | Heroicon name for the nav item |
| `navigationSort(int)` | `int` | `1` | Sort order within the nav group |
| `navigationBadge(bool)` | `bool` | `true` | Show or hide the record-count badge |

### 3. Unlayer Project ID (Recommended)

To enable the **Dark Theme** and **Image Uploads** in the Unlayer editor, you must set a valid Unlayer Project ID. Without it, the editor runs in anonymous demo mode and custom appearances are disabled.

1. Create a free account at [unlayer.com](https://unlayer.com) and create a Project.
2. Copy the **Project ID** from your project settings page (it's the number in the URL: `console.unlayer.com/emails/YOUR_PROJECT_ID/...`).
3. Add it to your `.env`:

```env
UNLAYER_PROJECT_ID=YOUR_PROJECT_ID
```

> **Note:** Even without a Project ID, the editor works fine for designing and saving templates. The only limitation is that dark mode isn’t available. For now, I’ve used a placeholder Project ID to enable dark mode. That said, I recommend creating a free developer account and using your own Project ID for proper configuration and long-term use.

---

## 💡 Usage

### 1. Design Your Template

Navigate to **Email Templates** in your Filament panel and click **New Email Template**.

- **Name** — Internal human-readable label.
- **Template Key** — A unique programmatic identifier (e.g., `order.success`, `auth.welcome`). This is what you'll reference in your Mailables.
- **Subject** — The email subject line. Blade syntax and `{{placeholders}}` are both supported.
- **Design** — Use the full-featured Unlayer drag-and-drop editor to build your layout. Merge tags are available in the editor toolbar.

### 2. Use in a Laravel Mailable

Add the `HasEmailTemplate` trait to any standard Laravel Mailable:

```php
namespace App\Mail;

use Illuminate\Mail\Mailable;
use NoteBrainsLab\FilamentEmailTemplates\Traits\HasEmailTemplate;

class OrderConfirmation extends Mailable
{
    use HasEmailTemplate;

    public function __construct(public $order)
    {
        // Reference the 'key' you set in the Filament panel
        $this->templateKey = 'order.success';

        // Pass the variables your design uses as {{placeholders}}
        $this->templateVariables = [
            'user_name'   => $this->order->customer_name,
            'order_total' => $this->order->total,
        ];
    }
}
```

Then send it as any standard Mailable:

```php
Mail::to($user)->send(new OrderConfirmation($order));
```

### 3. Using Placeholders

In the Unlayer editor, add `{{variable_name}}` placeholders as text anywhere in your design. The trait will replace them safely at send time.

```
Hello {{user_name}}, your order of ${{order_total}} has been confirmed!
```

The subject line also supports placeholders and full Blade syntax:

```
Your Order #{{order_total}} is Confirmed — Thanks {{user_name}}!
```

---

## 🌙 Dark Mode

The Unlayer editor fully syncs with Filament's dark mode toggle:

- The **editor chrome** (toolbars, panels, dropdowns) switches between `modern_dark` and `modern_light` themes.
- The **canvas background** updates between `#161616` (dark) and `#f9f9f9` (light) dynamically.
- Both the initial page load and runtime theme toggles are handled automatically — no page refresh needed.

> Dark mode requires a valid `UNLAYER_PROJECT_ID` to work.

---

## 📋 Requirements

| Dependency | Version |
|---|---|
| PHP | `^8.2` |
| Laravel | `^11.0` or `^12.0` |
| Filament | `^4.0` or `^5.0` |

---

## 📄 License

The MIT License (MIT). Please see the [License File](LICENSE.md) for more information.