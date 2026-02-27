<?php

use NoteBrainsLab\FilamentEmailTemplates\Tests\TestCase;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Queue;
use NoteBrainsLab\FilamentEmailTemplates\Models\EmailTemplate;
use NoteBrainsLab\FilamentEmailTemplates\Tests\Fixtures\DummyOrderPlacedEvent;
use NoteBrainsLab\FilamentEmailTemplates\Tests\Fixtures\DummyOrder;
use NoteBrainsLab\FilamentEmailTemplates\Jobs\SendEmailTemplateJob;


it('dispatches the email job when matching event is fired', function () {
    Queue::fake();

    // Create a template matching our dummy event
    $template = EmailTemplate::create([
        'name' => 'Order Confirmation',
        'event_class' => DummyOrderPlacedEvent::class,
        'subject' => 'Thank you for your order, {{ $order->customerName }}!',
        'recipients' => ['{{ $order->customerEmail }}'],
        'body_html' => '<p>Order ID is: {{ $order->id }}</p>',
        'is_active' => true,
    ]);

    // Dispatch a dummy event
    $order = new DummyOrder(1234, 'John Doe', 'john@example.com');
    event(new DummyOrderPlacedEvent($order));

    // Assert that the job was pushed to the queue
    Queue::assertPushed(SendEmailTemplateJob::class, function (SendEmailTemplateJob $job) use ($template) {
        return $job->template->id === $template->id;
    });
});

it('does not dispatch if template is inactive', function () {
    Queue::fake();

    // Inactive template
    EmailTemplate::create([
        'name' => 'Inactive Template',
        'event_class' => DummyOrderPlacedEvent::class,
        'subject' => 'Inactive',
        'recipients' => ['test@test.com'],
        'is_active' => false,
    ]);

    $order = new DummyOrder(1234, 'John Doe', 'john@example.com');
    event(new DummyOrderPlacedEvent($order));

    Queue::assertNotPushed(SendEmailTemplateJob::class);
});
