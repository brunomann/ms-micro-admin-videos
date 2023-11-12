<?php

namespace Core\UseCase\DTO\Category\DeleteCategory;

class DeleteCategoryInputDto
{
    public function __construct(
        public string $id
    )
    {
        
    }
}