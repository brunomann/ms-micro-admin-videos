<?php

namespace Core\Domain\Entity\Traits;

use DomainException;
use Exception;

trait MethodsMagicsTrait
{
    public function __get($property)
    {
        if(isset($this->{$property})){
            return $this->{$property};
        }
        
        $className = get_class($this);
        throw new Exception("Property {$property} not exists in class {$className}");
    }

    public function id():string
    {
        return (string) $this->id;
    }

    public function createdAt():string
    {
        return $this->created_at->format('Y-m-d H:i:s');
    }
}
