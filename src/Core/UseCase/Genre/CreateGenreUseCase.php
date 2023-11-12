<?php

namespace Core\UseCase\Genre;

use Core\Domain\Entity\Genre;
use Core\Domain\Exception\NotFoundException;
use Core\Domain\Repository\CategoryRepositoryInterface;
use Core\Domain\Repository\GenreRepositoryInterface;
use Core\UseCase\DTO\Genre\Create\GenreCreateInputDto;
use Core\UseCase\DTO\Genre\Create\GenreCreateOutputDto;
use Core\UseCase\Interfaces\TransactionInterface;

class CreateGenreUseCase
{
    protected $repository;
    protected $transaction;
    protected $categoryRepository;

    public function __construct(GenreRepositoryInterface $repository, TransactionInterface $transaction, CategoryRepositoryInterface $categoryRepository)
    {
        $this->repository = $repository;
        $this->transaction = $transaction;
        $this->categoryRepository = $categoryRepository;
    }

    public function execute(GenreCreateInputDto $input):GenreCreateOutputDto
    {
        try{
            $genre = new Genre(
                name: $input->name,
                is_active: $input->is_active,
                categories_id: $input->categoriesId,
            );
            $this->validateGateroriesId($input->categoriesId);
            $genreDb = $this->repository->insert($genre);
            $this->transaction->commit();
            return new GenreCreateOutputDto(
                id: (string) $genreDb->id,
                name: $genreDb->name,
                is_active: $genreDb->is_active,
                created_at: $genreDb->createdAt()
            );

            
       }catch(\Throwable $e){
            $this->transaction->rollback();
            throw $e;
       }
    }

    private function validateGateroriesId(array $categoriesId = [])
    {
        $categoriesDb = $this->categoryRepository->getIdsByListIds($categoriesId);

        $arrayDiff = array_diff($categoriesId, $categoriesDb);

        if(count($arrayDiff)){
            $msgFormatted = sprintf(
                '%s %s not found',
                count($arrayDiff) > 1 ? 'Categories' : 'Category',
                implode(', ', $arrayDiff)
            );
            throw new NotFoundException($msgFormatted);
        }
    }  
}