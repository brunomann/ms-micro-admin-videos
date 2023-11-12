<?php

namespace Tests\Unit\UseCase\Genre;

use Core\UseCase\DTO\Genre\List\ListGenresInputDto;
use Core\Domain\Repository\GenreRepositoryInterface;
use Core\Domain\Repository\PaginationInterface;
use Core\UseCase\DTO\Genre\List\ListGenresOutputDto;
use PHPUnit\Framework\TestCase;
use Core\UseCase\Genre\ListGenresUseCase;
use Mockery;
use Mockery\Mock;
use stdClass;

class ListGenresUseCaseUnitTest extends TestCase
{
    public function testUseCase()
    {
        $mockRepository = Mockery::mock(stdClass::class, GenreRepositoryInterface::class);
        $mockRepository->shouldReceive('paginate')
                        ->once()
                        ->andReturn($this->createMockPaginationEntity());
        $mockInputDto   = Mockery::mock(ListGenresInputDto::class, ['teste', 'desc', 1, 15]);

        $useCase = new ListGenresUseCase($mockRepository);
        $response = $useCase->execute($mockInputDto);
        // dump($response);
        $this->assertInstanceOf(ListGenresOutputDto::class, $response);

        Mockery::close();

        /**
         * Spies
         */
        //# Arrange
        $spy = Mockery::spy(stdClass::class, GenreRepositoryInterface::class);
        $spy->shouldReceive('paginate')
                        ->once()
                        ->andReturn($this->createMockPaginationEntity());
        $useCase = new ListGenresUseCase($spy);

        //# Action
        $spyResponse = $useCase->execute($mockInputDto);

        //# Assert
        $spy->shouldHaveReceived()->paginate(
            'teste', 'desc', 1, 15
        );
    }

    protected function createMockPaginationEntity(array $itemsToMock = []):PaginationInterface
    {
        $mockPaginationEntity = Mockery::mock(stdClass::class, PaginationInterface::class);
        $mockPaginationEntity->shouldReceive('items')->andReturn($itemsToMock);
        $mockPaginationEntity->shouldReceive('total')->andReturn(0);
        $mockPaginationEntity->shouldReceive('firstPage')->andReturn(0);
        $mockPaginationEntity->shouldReceive('lastPage')->andReturn(0);
        $mockPaginationEntity->shouldReceive('currentPage')->andReturn(0);
        $mockPaginationEntity->shouldReceive('itemPerPage')->andReturn(0);
        $mockPaginationEntity->shouldReceive('to')->andReturn(0);
        $mockPaginationEntity->shouldReceive('from')->andReturn(0);

        return $mockPaginationEntity;
    }
}
