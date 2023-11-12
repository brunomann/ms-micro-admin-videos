<?php

namespace Tests\Unit\UseCase\Genre;

use Core\Domain\Entity\Genre as EntityGenre;
use Core\Domain\Repository\GenreRepositoryInterface;
use Core\Domain\ValueObject\Uuid as ValueObjectUuid;
use Core\UseCase\Genre\ListGenreUseCase;
use Core\UseCase\DTO\Genre\GenreInputDto;
use Core\UseCase\DTO\Genre\GenreOutputDto;
use Mockery;
use PHPUnit\Framework\TestCase;
use Ramsey\Uuid\Uuid;

class ListGenreUseCaseUnitTest extends TestCase
{

    public function testUseCaseListGenre()
    {
        $uuid = (string) Uuid::uuid4();
        $mockEntityGenre = Mockery::mock(EntityGenre::class, [
            'teste', new ValueObjectUuid($uuid), true, []
        ]);
        $mockEntityGenre->shouldReceive('created_at')->andReturn(date('Y-m-d H:i:s'));

        $mockRepository = Mockery::mock(stdClass::class, GenreRepositoryInterface::class);
        $mockRepository->shouldReceive('findById')->once()->with($uuid)->andReturn($mockEntityGenre);
        
        $mockInputDto = Mockery::mock(GenreInputDto::class, [
            $uuid
        ]);

        $useCase = new ListGenreUseCase($mockRepository);
        $result = $useCase->execute($mockInputDto);

        $this->assertInstanceOf(GenreOutputDto::class, $result);

        Mockery::close();
    }
}
