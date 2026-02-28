# Filament Email Templates Manager
A powerful and simple Email Template Management plugin for Filament.

This plugin allows you to seamlessly manage beautifully designed email themes and templates using an integrated Unlayer editor, and easily send them via Laravel Mailables utilizing a simple Trait.

## ✨ Features
- **Design Themes:** Create reusable design shells (themes) utilizing Unlayer.
- **Dynamic Templates:** Create standard templates based on your themes and inject content.
- **Variable Placeholders:** Utilize simple `{{variable_name}}` syntax in subjects and body content.
- **Seamless Integration:** Use the `HasEmailTemplate` Trait in any Laravel Mailable to automatically fetch and compile the content.
- Integrated **Unlayer** editor for visual email design.
- Database storage for themes and templates.

## 🚀 Installation

Install the package via composer:

```bash
composer require notebrainslab/filament-email-templates
```

Run the install command. This command will publish migrations and configurations, and offer to run migrations:

```bash
php artisan filament-email-templates:install
```

If you chose not to run migrations during install, you can do so manually:

```bash
php artisan migrate
```

## 🛠 Configuration

Publish the config file (optional, if you skipped installation command):

```bash
php artisan vendor:publish --tag="filament-email-templates-config"
```

Register the Plugin in your Filament Panel Provider (usually `app/Providers/Filament/AdminPanelProvider.php`):

```php
use NoteBrainsLab\FilamentEmailTemplates\FilamentEmailTemplatesPlugin;

public function panel(Panel $panel): Panel
{
    return $panel
        // ...
        ->plugins([
            FilamentEmailTemplatesPlugin::make()
                ->navigationGroup('Email Templates')
                ->navigationIcon('heroicon-o-envelope')
                ->navigationSort(5),
        ]);
}
```

## 💡 Usage

### 1. Create a Theme
Navigate to the **Email Templates > Themes** section of your Filament Panel.
Themes act as the overall shell or layout for your emails.
1. Give your theme a name and set an Unlayer design.
2. Inside the Unlayer Designer, add a **"Custom HTML"** block.
3. Place the exact token `{{body_content}}` inside this HTML block. This acts as the placeholder where your template content will be injected.

### 2. Create a Template
Navigate to the **Email Templates > Settings** (or the template resource).
1. Provide a **Name** and a **Unique Key** (e.g., `welcome_user`). This key will be used to reference the template in your code.
2. Select a base Theme.
3. Write your **Subject** and **Body Content**.
4. You can use variables by utilizing the syntax `{{variable_name}}` (e.g., `Hello {{user_name}}!`). These will be dynamically replaced.

### 3. Integrate with Laravel Mail
Generate a standard Laravel Mailable class:
```bash
php artisan make:mail WelcomeEmail
```

Use the `HasEmailTemplate` Trait inside your Mailable. You do not need to define `build()`, `envelope()`, or `content()` methods. The Trait handles the rendering automatically.

```php
namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use NoteBrainsLab\FilamentEmailTemplates\Traits\HasEmailTemplate;

class WelcomeEmail extends Mailable
{
    use Queueable, SerializesModels, HasEmailTemplate;

    public function __construct(public $user)
    {
        // 1. Define the unique template key from the Filament Panel
        $this->templateKey = 'welcome_user';
        
        // 2. Define the exact array of variables for the {{...}} placeholders
        $this->templateVariables = [
            'user_name' => $this->user->name,
            'order_id'  => 5678,
        ];
    }
}
```

Then, trigger your email anywhere in your application normally:
```php
Mail::to($user->email)->send(new WelcomeEmail($user));
```

## License
The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
