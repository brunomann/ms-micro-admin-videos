<?php 

namespace Core\Domain\Entity;

use Core\Domain\Entity\Traits\MethodsMagicsTrait;
use Core\Domain\Validation\DomainValidation;
use Core\Domain\ValueObject\Uuid;
use DateTime;

class Genre
{
    use MethodsMagicsTrait;

    public function __construct(
        protected string $name,
        protected ?Uuid $id = null,
        protected bool $is_active = true,
        protected array $categories_id = [],
        protected ?DateTime $created_at = null,
    )
    {
        $this->id = $this->id ?? Uuid::random();
        $this->created_at = $this->created_at ?? new DateTime(date('Y-m-d H:i:s'));

        $this->validate();
    }

    public function activate()
    {
        $this->is_active = true;
    }

    public function deactivate()
    {
        $this->is_active = false;
    }

    public function update(string $name)
    {
        $this->name = $name;

        $this->validate();
    }

    protected function validate()
    {
        DomainValidation::strMinLength($this->name);
        DomainValidation::strMaxLength($this->name);
    }

    public function addCategory(string $categoryId)
    {
        array_push($this->categories_id, $categoryId);
    }

    public function removeCategory(string $categoryId)
    {
        $keyToRemove = array_search($categoryId, $this->categories_id);
        unset($this->categories_id[$keyToRemove]);
    }
}