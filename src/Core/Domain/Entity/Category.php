<?php

namespace Core\Domain\Entity;

use Core\Domain\Entity\Traits\MethodsMagicsTrait;
use Core\Domain\Exception\EntityValidationException;
use Core\Domain\Validation\DomainValidation;
use Core\Domain\ValueObject\Uuid;
use DateTime;
use Exception;

class Category
{
    use MethodsMagicsTrait;
    
    public function __construct(
        protected Uuid|string $id = '',
        protected string $name  = '',
        protected string $description = '',
        protected bool $is_active = true,
        protected DateTime|string $created_at = '',
    ){
        $this->id = $this->id ? new Uuid($id) : Uuid::random();
        $this->created_at = $this->created_at ? new DateTime($this->created_at) : new DateTime('now');
        $this->validate();
    }

    public function activate():void
    {
        $this->is_active = true;
    }

    public function disable():void
    {
        $this->is_active = false;
    }

    public function update(string $name, string $description)
    {
        $this->name = $name ?? $this->description;
        $this->description = $description ?? $this->description;
        $this->validate();
    }

    protected function validate()
    {
        DomainValidation::strMinLength($this->name);
        DomainValidation::strMaxLength($this->name);
        DomainValidation::canNullMaxLength($this->description);
    }
}