<?php

use NoteBrainsLab\FilamentEmailTemplates\Tests\TestCase;
use Illuminate\Support\Facades\Mail;
use NoteBrainsLab\FilamentEmailTemplates\Models\EmailTemplate;
use NoteBrainsLab\FilamentEmailTemplates\Mail\DynamicTemplateMail;

it('successfully renders token syntax and sends email', function () {
    Mail::fake();

    $template = EmailTemplate::create([
        'name' => 'Test Email',
        'key' => 'test_email',
        'locale' => 'en',
        'subject' => 'New Order: ##order_id## from ##store_name##',
        'body_html' => '<h1>Hello ##customer_name##!</h1>',
        'is_active' => true,
    ]);

    $data = [
        'order_id' => 9988,
        'customer_name' => 'Jane Doe',
        'store_name' => 'Awesome Store'
    ];

    $mail = new DynamicTemplateMail('test_email', $data);

    expect($mail->subject)->toBe('New Order: 9988 from Awesome Store');
    
    Mail::to('jane@example.com')->send($mail);

    Mail::assertSent(DynamicTemplateMail::class, function ($mail) {
        return $mail->hasTo('jane@example.com')
            && $mail->subject === 'New Order: 9988 from Awesome Store';
    });
});

it('supports config tokens', function () {
    config(['app.name' => 'TokenTestStore']);

    $template = EmailTemplate::create([
        'name' => 'Config Test',
        'key' => 'config_test',
        'locale' => 'en',
        'subject' => 'Welcome to ##config.app.name##',
        'body_html' => 'Check your config.',
        'is_active' => true,
    ]);

    $mail = new DynamicTemplateMail('config_test');

    expect($mail->subject)->toBe('Welcome to TokenTestStore');
});
