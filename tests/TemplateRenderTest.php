<?php

use NoteBrainsLab\FilamentEmailTemplates\Tests\TestCase;
use Illuminate\Support\Facades\Mail;
use NoteBrainsLab\FilamentEmailTemplates\Models\EmailTemplate;
use NoteBrainsLab\FilamentEmailTemplates\Tests\Fixtures\DummyOrderPlacedEvent;
use NoteBrainsLab\FilamentEmailTemplates\Tests\Fixtures\DummyOrder;
use NoteBrainsLab\FilamentEmailTemplates\Jobs\SendEmailTemplateJob;
use NoteBrainsLab\FilamentEmailTemplates\Mail\DynamicTemplateMail;
use NoteBrainsLab\FilamentEmailTemplates\Models\EmailTemplateException;


it('successfully renders blade syntax and sends email', function () {
    Mail::fake();

    $template = EmailTemplate::create([
        'name' => 'Test Email',
        'event_class' => DummyOrderPlacedEvent::class,
        'subject' => 'New Order: #{{ $order->id }} at {{ $storeName }}',
        'recipients' => ['admin@store.com', '{{ $order->customerEmail }}'],
        'body_html' => '<h1>Hello {{ $order->customerName }}!</h1>',
        'is_active' => true,
    ]);

    $order = new DummyOrder(9988, 'Jane Doe', 'jane@example.com');
    $event = new DummyOrderPlacedEvent($order, 'Awesome Store');

    $job = new SendEmailTemplateJob($template, $event);
    $job->handle();

    // Assert mail was sent to both static and parsed recipients
    Mail::assertSent(DynamicTemplateMail::class, function ($mail) {
        $mail->build(); // Force build the mail to test envelope/content
        
        return $mail->hasTo('admin@store.com')
            && $mail->hasTo('jane@example.com')
            && $mail->emailSubject === 'New Order: #9988 at Awesome Store'
            && str_contains($mail->bodyHtml, '<h1>Hello Jane Doe!</h1>');
    });
});

it('captures an exception if blade compilation fails', function () {
    Mail::fake();

    $template = EmailTemplate::create([
        'name' => 'Failing Email',
        'event_class' => DummyOrderPlacedEvent::class,
        'subject' => 'Bad syntax',
        'recipients' => ['admin@store.com'],
        // Trying to access non-existent property
        'body_html' => '<h1>Hello {{ $order->nonExistentProperty }}!</h1>',
        'is_active' => true,
    ]);

    $order = new DummyOrder(111, 'Err', 'err@example.com');
    $event = new DummyOrderPlacedEvent($order);

    $job = new SendEmailTemplateJob($template, $event);
    
    // We expect the job to throw an exception, but it should also log it in the database
    try {
        $job->handle();
    } catch (\Throwable $e) {}

    // Assert mail was not sent
    Mail::assertNothingSent();

    // Assert exception was logged in database
    $this->assertDatabaseHas('filament_email_template_exceptions', [
        'email_template_id' => $template->id,
        'event_class' => DummyOrderPlacedEvent::class,
    ]);

    $exception = EmailTemplateException::first();
    expect($exception->error_message)->toContain('nonExistentProperty');
});
