<?php

namespace Core\UseCase\DTO\Category\UpdateCategory;

class UpdateCategoryInputDto
{
    public function __construct(
        public string $id,
        public string $name,
        public string|null $description = null,
        public bool $is_active = true
    )
    {
        
    }
}