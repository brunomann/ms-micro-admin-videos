<?php

namespace Core\UseCase\Genre;

use Core\Domain\Entity\Genre;
use Core\Domain\Exception\NotFoundException;
use Core\Domain\Repository\CategoryRepositoryInterface;
use Core\Domain\Repository\GenreRepositoryInterface;
use Core\Domain\ValueObject\Uuid;
use Core\UseCase\Interfaces\TransactionInterface;
use Core\UseCase\DTO\Genre\Update\GenreUpdateInputDto;
use Core\UseCase\DTO\Genre\Update\GenreUpdateOutputDto;

class UpdateGenreUseCase
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
    
    public function execute(GenreUpdateInputDto $input):GenreUpdateOutputDto
    {
        $genre = $this->repository->findById($input->id);
        $genre->update($input->name);
        $this->validateGateroriesId($input->categoriesId);

        foreach($input->categoriesId as $categoryId){
            $genre->addCategory($categoryId);
        }
        try{
            $genreDb = $this->repository->update($genre);
            $this->transaction->commit();
            return new GenreUpdateOutputDto(
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