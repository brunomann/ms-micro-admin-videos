<?php

namespace Tests\Unit\UseCase\Genre;

use Core\Domain\Entity\Genre as EntityGenre;
use Core\Domain\Repository\GenreRepositoryInterface;
use Core\Domain\ValueObject\Uuid as ValueObjectUuid;
use Core\UseCase\Genre\DeleteGenreUseCase;
use Core\UseCase\DTO\Genre\GenreInputDto;
use Core\UseCase\DTO\Genre\Delete\DeleteGenreOutputDto;
use Mockery;
use PHPUnit\Framework\TestCase;
use Ramsey\Uuid\Uuid;

class DeleteGenreUseCaseUnitTest extends TestCase
{
    public function testUseCaseDeleteGenre()
    {
        $uuid = (string) Uuid::uuid4();
        $mockEntityGenre = Mockery::mock(EntityGenre::class, [
            'teste', new ValueObjectUuid($uuid), true, []
        ]);

        $mockRepository = Mockery::mock(stdClass::class, GenreRepositoryInterface::class);
        $mockRepository->shouldReceive('delete')
                        ->once()
                        ->with($uuid)
                        ->andReturn(true);

        $mockInputDto = Mockery::mock(GenreInputDto::class, [
            $uuid
        ]);

        $useCase = new DeleteGenreUseCase($mockRepository);
        $result = $useCase->execute($mockInputDto);

        $this->assertInstanceOf(DeleteGenreOutputDto::class, $result);
        $this->assertTrue($result->success);
    }

    public function testUseCaseDeleteGenreFalse()
    {
        $uuid = (string) Uuid::uuid4();
        $mockEntityGenre = Mockery::mock(EntityGenre::class, [
            'teste', new ValueObjectUuid($uuid), true, []
        ]);

        $mockRepository = Mockery::mock(stdClass::class, GenreRepositoryInterface::class);
        $mockRepository->shouldReceive('delete')
                        ->times(1)
                        ->with($uuid)
                        ->andReturn(false);

        $mockInputDto = Mockery::mock(GenreInputDto::class, [
            $uuid
        ]);

        $useCase = new DeleteGenreUseCase($mockRepository);
        $result = $useCase->execute($mockInputDto);

        $this->assertInstanceOf(DeleteGenreOutputDto::class, $result);
        $this->assertFalse($result->success);
    }

    public function tearDown():void
    {
        Mockery::close();
        parent::tearDown();
    }
}
