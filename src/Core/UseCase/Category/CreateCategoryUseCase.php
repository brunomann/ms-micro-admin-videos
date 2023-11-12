<?php

namespace Core\UseCase\Category;

use Core\Domain\Entity\Category;
use Core\Domain\Repository\CategoryRepositoryInterface;
use Core\UseCase\DTO\Category\CreateCategory\{CreateCategoryOutputDto, CreateCategoryInputDto};

class CreateCategoryUseCase
{

    protected $repository;

    public function __construct(CategoryRepositoryInterface $repository)
    {
        $this->repository = $repository;
    }

    public function execute(CreateCategoryInputDto $input):CreateCategoryOutputDto
    {
        $category = new Category(
            name: $input->name,
            description: $input->description,
            is_active: $input->is_active
        );

        $categoryBd = $this->repository->insert($category);

        return new CreateCategoryOutputDto(
            id: $categoryBd->id(),
            name: $categoryBd->name,
            description: $categoryBd->description,
            is_active: $categoryBd->is_active,
            created_at: $categoryBd->createdAt()
        );
    }
}