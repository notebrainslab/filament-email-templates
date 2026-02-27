<?php

namespace NoteBrainsLab\FilamentEmailTemplates\Tests\Fixtures;

class DummyOrder
{
    public int $id;
    public string $customerName;
    public string $customerEmail;

    public function __construct(int $id, string $name, string $email)
    {
        $this->id = $id;
        $this->customerName = $name;
        $this->customerEmail = $email;
    }
}
