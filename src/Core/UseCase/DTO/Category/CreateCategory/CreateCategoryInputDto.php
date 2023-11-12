<?php

namespace Core\UseCase\DTO\Category\CreateCategory;

class CreateCategoryInputDto
{
    public function __construct(
        public string $name,
        public string $description = '',
        public bool $is_active = true,
    )
    {
        
    }
}