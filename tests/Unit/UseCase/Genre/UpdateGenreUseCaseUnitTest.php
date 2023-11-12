<?php

namespace Tests\Unit\UseCase\Genre;

use Core\UseCase\DTO\Genre\Update\GenreUpdateInputDto;
use Core\Domain\Entity\Genre as EntityGenre;
use Core\Domain\Exception\NotFoundException;
use Core\Domain\Repository\CategoryRepositoryInterface;
use Core\Domain\Repository\GenreRepositoryInterface;
use PHPUnit\Framework\TestCase;
use Core\UseCase\Genre\UpdateGenreUseCase;
use Mockery;
use Ramsey\Uuid\Uuid;
use stdClass;
use Core\Domain\ValueObject\Uuid as ValueObjectUuid;
use Core\UseCase\DTO\Genre\Update\GenreUpdateOutputDto;
use Core\UseCase\Interfaces\TransactionInterface;

class UpdateGenreUseCaseUnitTest extends TestCase
{
    public function testUpdate()
    {
        $uuid = (string) Uuid::uuid4();

        $mockRepository = $this->createRepository($uuid);

        $mockTransaction = $this->createTransaction();

        $mockCategoryRepository = Mockery::mock(stdClass::class, CategoryRepositoryInterface::class);
        $mockCategoryRepository->shouldReceive('getIdsByListIds')->andReturn([$uuid]);

        $mockInputDto = Mockery::mock(GenreUpdateInputDto::class, [
            $uuid, 'name', [$uuid]
        ]);
 
        $useCase = new UpdateGenreUseCase($mockRepository, $mockTransaction, $mockCategoryRepository);
        $result = $useCase->execute($mockInputDto);

        $this->assertInstanceOf(GenreUpdateOutputDto::class, $result);        
    }

    public function testUpdateException()
    {
        $this->expectException(NotFoundException::class);

        $uuid = (string) Uuid::uuid4();
        $uuid2 = (string) Uuid::uuid4();

        $mockRepository = $this->createRepository($uuid, 0);

        $mockTransaction = $this->createTransaction();

        $mockCategoryRepository = Mockery::mock(stdClass::class, CategoryRepositoryInterface::class);
        $mockCategoryRepository->shouldReceive('getIdsByListIds')->andReturn([$uuid]);

        $mockInputDto = Mockery::mock(GenreUpdateInputDto::class, [
            $uuid, 'name', [$uuid, $uuid2]
        ]);
 
        $useCase = new UpdateGenreUseCase($mockRepository, $mockTransaction, $mockCategoryRepository);
        $result = $useCase->execute($mockInputDto);

        $this->assertInstanceOf(GenreUpdateOutputDto::class, $result);
    }

    private function createEntity($uuid)
    {
        $mockEntityGenre = Mockery::mock(EntityGenre::class, [
            'teste', new ValueObjectUuid($uuid), true, []
        ]);
        $mockEntityGenre->shouldReceive('createdAt')->andReturn(date('Y-m-d H:i:s'));
        $mockEntityGenre->shouldReceive('update')->once();
        $mockEntityGenre->shouldReceive('addCategory');

        return $mockEntityGenre;
    }

    private function createRepository($uuid, $times = 1)
    {
        $entity = $this->createEntity($uuid);
        $mockRepository = Mockery::mock(stdClass::class, GenreRepositoryInterface::class);
        $mockRepository->shouldReceive('findById')->once()->with($uuid)->andReturn($entity);
        $mockRepository->shouldReceive('update')->times($times)->andReturn($entity);

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
