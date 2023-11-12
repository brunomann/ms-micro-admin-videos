<?php

namespace Core\UseCase\Category;

use Core\Domain\Repository\CategoryRepositoryInterface;
use Core\UseCase\DTO\Category\UpdateCategory\UpdateCategoryInputDto;
use Core\UseCase\DTO\Category\UpdateCategory\UpdateCategoryOutputDto;

class UpdateCategoryUseCase
{
    protected $repository;

    public function __construct(CategoryRepositoryInterface $repository)
    {
        $this->repository = $repository;
    }

    public function execute(UpdateCategoryInputDto $input):UpdateCategoryOutputDto
    {
        $category = $this->repository->findById($input->id);
        $category->update(name: $input->name, description: $input->description ?? $category->description);
        $categoryUpdated = $this->repository->update($category);

        return new UpdateCategoryOutputDto(
            id: $categoryUpdated->id,
            name: $categoryUpdated->name,
            description: $categoryUpdated->description,
            is_active:  $categoryUpdated->is_active,
            created_at: $categoryUpdated->createdAt()
        );
    }
}