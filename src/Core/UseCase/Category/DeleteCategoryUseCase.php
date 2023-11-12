<?php

namespace Core\UseCase\Category;

use Core\Domain\Repository\CategoryRepositoryInterface;
use Core\UseCase\DTO\Category\DeleteCategory\DeleteCategoryInputDto;
use Core\UseCase\DTO\Category\DeleteCategory\DeleteCategoryOutputDto;
use Core\UseCase\DTO\Category\UpdateCategory\UpdateCategoryInputDto;
use Core\UseCase\DTO\Category\UpdateCategory\UpdateCategoryOutputDto;

class DeleteCategoryUseCase
{
    protected $repository;

    public function __construct(CategoryRepositoryInterface $repository)
    {
        $this->repository = $repository;
    }

    public function execute(DeleteCategoryInputDto $input):DeleteCategoryOutputDto
    {
        $result = $this->repository->delete($input->id);

        return new DeleteCategoryOutputDto(
            success: $result
        );
    }
}