<?php

namespace Core\UseCase\Genre;

use Core\Domain\Repository\GenreRepositoryInterface;
use Core\UseCase\DTO\Genre\Delete\DeleteGenreOutputDto;
use Core\UseCase\DTO\Genre\GenreInputDto;
use Core\UseCase\DTO\Genre\GenreOutputDto;
use Core\UseCase\DTO\Genre\List\ListGenresInputDto;
use Core\UseCase\DTO\Genre\List\ListGenresOutputDto;

// use Core\UseCase\DTO\Genre\List\ListGenresInputDto;

class DeleteGenreUseCase
{
    protected $repository;

    public function __construct(GenreRepositoryInterface $repository)
    {
        $this->repository = $repository;
    }

    public function execute(GenreInputDto $input):DeleteGenreOutputDto
    {
        $success = $this->repository->delete($input->id);

        return new DeleteGenreOutputDto(
            success: $success
        );
    }
}