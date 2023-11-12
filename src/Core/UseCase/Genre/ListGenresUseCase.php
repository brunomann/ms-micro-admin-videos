<?php

namespace Core\UseCase\Genre;

use Core\Domain\Repository\GenreRepositoryInterface;
use Core\UseCase\DTO\Genre\List\ListGenresInputDto;
use Core\UseCase\DTO\Genre\List\ListGenresOutputDto;

// use Core\UseCase\DTO\Genre\List\ListGenresInputDto;

class ListGenresUseCase
{
    protected $repository;

    public function __construct(GenreRepositoryInterface $repository)
    {
        $this->repository = $repository;
    }

    public function execute(ListGenresInputDto $input):ListGenresOutputDto
    {
        $response = $this->repository->paginate(
            filter: $input->filter,
            order: $input->order,
            page: $input->page,
            totalPages: $input->totalPages

        );

        return new ListGenresOutputDto(
            items: $response->items(),
            total: $response->total(),
            firstPage: $response->firstPage(),
            lastPage: $response->lastPage(),
            currentPage: $response->currentPage(),
            itemPerPage: $response->itemPerPage(),
            to: $response->to(),
            from: $response->from()
        );
    }
}