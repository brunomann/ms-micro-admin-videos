<?php

namespace Tests\Unit\UseCase\Genre;

use Core\UseCase\DTO\Genre\Create\GenreCreateInputDto;
use Core\Domain\Entity\Genre as EntityGenre;
use Core\Domain\Exception\NotFoundException;
use Core\Domain\Repository\CategoryRepositoryInterface;
use Core\Domain\Repository\GenreRepositoryInterface;
use PHPUnit\Framework\TestCase;
use Core\UseCase\Genre\CreateGenreUseCase;
use Mockery;
use Ramsey\Uuid\Uuid;
use stdClass;
use Core\Domain\ValueObject\Uuid as ValueObjectUuid;
use Core\UseCase\DTO\Genre\Create\GenreCreateOutputDto;
use Core\UseCase\Interfaces\TransactionInterface;

class CreateGenreUseCaseUnitTest extends TestCase
{
    public function testCreate()
    {
        $uuid = (string) Uuid::uuid4();

        $mockRepository = $this->createRepository($uuid, 1);

        $mockTransaction = $this->createTransaction();

        $mockCategoryRepository = Mockery::mock(stdClass::class, CategoryRepositoryInterface::class);
        $mockCategoryRepository->shouldReceive('getIdsByListIds')->andReturn([$uuid]);

        $mockInputDto = Mockery::mock(GenreCreateInputDto::class, [
            'name', true, [$uuid]
        ]);
 
        $useCase = new CreateGenreUseCase($mockRepository, $mockTransaction, $mockCategoryRepository);
        $result = $useCase->execute($mockInputDto);

        $this->assertInstanceOf(GenreCreateOutputDto::class, $result);        
    }

    public function testCreateException()
    {
        $this->expectException(NotFoundException::class);

        $uuid = (string) Uuid::uuid4();
        $uuid2 = (string) Uuid::uuid4();

        $mockRepository = $this->createRepository($uuid, 0);

        $mockTransaction = $this->createTransaction();

        $mockCategoryRepository = Mockery::mock(stdClass::class, CategoryRepositoryInterface::class);
        $mockCategoryRepository->shouldReceive('getIdsByListIds')->andReturn([$uuid]);

        $mockInputDto = Mockery::mock(GenreCreateInputDto::class, [
            'name', true, [$uuid, $uuid2]
        ]);
 
        $useCase = new CreateGenreUseCase($mockRepository, $mockTransaction, $mockCategoryRepository);
        $result = $useCase->execute($mockInputDto);

        $this->assertInstanceOf(GenreCreateOutputDto::class, $result);
    }

    private function createEntity($uuid)
    {
        $mockEntityGenre = Mockery::mock(EntityGenre::class, [
            'teste', new ValueObjectUuid($uuid), true, []
        ]);
        $mockEntityGenre->shouldReceive('createdAt')->andReturn(date('Y-m-d H:i:s'));

        return $mockEntityGenre;
    }

    private function createRepository($uuid, int $times)
    {
        $mockRepository = Mockery::mock(stdClass::class, GenreRepositoryInterface::class);
        $mockRepository->shouldReceive('insert')
                        ->times($times)
                        ->andReturn($this->createEntity($uuid));

        return $mockRepository;
    }

    private function createTransaction()
    {
        $mockTransaction = Mockery::mock(stdClass::class, TransactionInterface::class);
        $mockTransaction->shouldReceive('commit');
        $mockTransaction->shouldReceive('rollback');

        return $mockTransaction;
    }

    protected function tearDown():void
    {
        Mockery::close();
        parent::tearDown();
    }
}
