<?php

namespace Core\Domain\Entity;

use Core\Domain\Entity\Traits\MethodsMagicsTrait;
use Core\Domain\Enum\CastMemberType;
use Core\Domain\Validation\DomainValidation;
use Core\Domain\ValueObject\Uuid;
use DateTime;

class CastMember
{
    use MethodsMagicsTrait;

    public function __construct(
        protected string $name,
        protected CastMemberType $type,
        protected ?Uuid $id = null,
        protected ?DateTime $created_at = null
    ){
        $this->id = $this->id ?? Uuid::random();
        $this->created_at = $this->created_at ?? new DateTime();
        $this->validate();
    }

    public function update(string $name):void
    {
        $this->name = $name;
        $this->validate();
    }

    protected function validate()
    {
        DomainValidation::strMinLength($this->name);
        DomainValidation::strMaxLength($this->name);
    }
}