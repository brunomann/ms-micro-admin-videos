<?php

namespace Core\UseCase\Category;

use Core\Domain\Repository\CategoryRepositoryInterface;
use Core\UseCase\DTO\Category\ListCategory\{ListCategoryInputDto, ListCategoryOutputDto};

class ListCategoryUseCase
{
    protected $repository;

    public function __construct(CategoryRepositoryInterface $repository)
    {
        $this->repository = $repository;
    }

    public function execute(ListCategoryInputDto $input):ListCategoryOutputDto
    {
        $categoryBd = $this->repository->findById($input->id);
        
        return new ListCategoryOutputDto(
            id: $categoryBd->id(),
            name: $categoryBd->name,
            description: $categoryBd->description,
            is_active: $categoryBd->is_active,
            created_at: $categoryBd->createdAt()

        );
    }
}