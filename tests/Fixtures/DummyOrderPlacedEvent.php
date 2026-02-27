<?php

namespace NoteBrainsLab\FilamentEmailTemplates\Tests\Fixtures;

class DummyOrderPlacedEvent
{
    public DummyOrder $order;
    public string $storeName;

    public function __construct(DummyOrder $order, string $storeName = 'Test Store')
    {
        $this->order = $order;
        $this->storeName = $storeName;
    }
}
