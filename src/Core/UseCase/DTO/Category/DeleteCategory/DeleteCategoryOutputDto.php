<?php

namespace Core\UseCase\DTO\Category\DeleteCategory;

class DeleteCategoryOutputDto
{
    public function __construct(
        public bool $success
    )
    {
        
    }
}