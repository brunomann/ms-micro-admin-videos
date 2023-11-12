<?php 

namespace Core\UseCase\Category;

use Core\Domain\Repository\CategoryRepositoryInterface;
use Core\UseCase\DTO\Category\ListCategories\ListCategoriesInputDto;
use Core\UseCase\DTO\Category\ListCategories\ListCategoriesOutputDto;

class ListCategoriesUseCase
{
    protected $repository;

    public function __construct(CategoryRepositoryInterface $repository)
    {
        $this->repository = $repository;
    }

    public function execute(ListCategoriesInputDto $input):ListCategoriesOutputDto
    {
        $listCategoriesBd = $this->repository->paginate(
            filter: $input->filter,
            order: $input->order,
            page: $input->page,
            totalPages: $input->totalPages);

        return new ListCategoriesOutputDto(
            items: $listCategoriesBd->items(),
            total: $listCategoriesBd->total(),
            firstPage: $listCategoriesBd->firstPage(),
            lastPage: $listCategoriesBd->lastPage(),
            currentPage: $listCategoriesBd->currentPage(),
            itemPerPage: $listCategoriesBd->itemPerPage(),
            to: $listCategoriesBd->to(),
            from: $listCategoriesBd->from(),
        );
    }
}