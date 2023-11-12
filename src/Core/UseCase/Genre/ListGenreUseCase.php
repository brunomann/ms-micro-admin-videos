<?php

namespace Core\UseCase\Genre;

use Core\Domain\Repository\GenreRepositoryInterface;
use Core\UseCase\DTO\Genre\GenreInputDto;
use Core\UseCase\DTO\Genre\GenreOutputDto;
// use Core\UseCase\DTO\Genre\List\ListGenresInputDto;

class ListGenreUseCase
{
    protected $repository;

    public function __construct(GenreRepositoryInterface $repository)
    {
        $this->repository = $repository;
    }

    public function execute(GenreInputDto $input):GenreOutputDto
    {
        $genre = $this->repository->findById($input->id);

        return new GenreOutputDto(
            id: (string) $genre->id,
            name: $genre->name,
            is_active: $genre->is_active,
            created_at: $genre->created_at,
        );
    }
}