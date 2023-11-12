<?php

namespace Tests\Unit\UseCase\Category;

use Core\Domain\Repository\CategoryRepositoryInterface;
use Core\Domain\Repository\PaginationInterface;
use Core\UseCase\Category\ListCategoriesUseCase;
use Core\UseCase\DTO\Category\ListCategories\ListCategoriesInputDto;
use Core\UseCase\DTO\Category\ListCategories\ListCategoriesOutputDto;
use Mockery;
use PHPUnit\Framework\TestCase;
use stdClass;

class ListCategoriesUseCaseUnitTest extends TestCase
{

    public function testListCategoriesEmpty()
    {
        $mockPaginationEntity = $this->createMockPaginationEntity([]);

        $mockRepository = Mockery::mock(CategoryRepositoryInterface::class);
        $mockRepository->shouldReceive('paginate')->andReturn($mockPaginationEntity);

        $mockInputDto = Mockery::mock(ListCategoriesInputDto::class, ['filter', 'desc']);
        $useCase = new ListCategoriesUseCase($mockRepository);
        $responseUseCase = $useCase->execute($mockInputDto);

        $this->assertInstanceOf(ListCategoriesOutputDto::class, $responseUseCase);
        $this->assertCount(0, $responseUseCase->items);

    }

    public function testSpyListCategoriesEmpty()
    {
        $mockPaginationEntity = $this->createMockPaginationEntity([]);

        $spyRepository = Mockery::spy(CategoryRepositoryInterface::class);
        $spyRepository->shouldReceive('paginate')->andReturn($mockPaginationEntity);

        $mockInputDto = Mockery::mock(ListCategoriesInputDto::class, ['filter', 'desc']);
        $useCase = new ListCategoriesUseCase($spyRepository);
        $responseUseCase = $useCase->execute($mockInputDto);

        $spyRepository->shouldHaveReceived('paginate');
        $this->assertTrue(true);

    }

    public function testListCategories()
    {
        $categoryOne = new stdClass();
        $categoryOne->id = '123456';
        $categoryOne->name = 'Name';
        $categoryOne->description = 'Desc';
        $categoryOne->is_active = true;

        $categoryTwo = new stdClass();
        $categoryTwo->id = '1234567';
        $categoryTwo->name = 'Names';
        $categoryTwo->description = 'Descs';
        $categoryTwo->is_active = true;
        $mockPaginationEntity = $this->createMockPaginationEntity([
            $categoryOne, $categoryTwo
        ]);

        $mockRepository = Mockery::mock(CategoryRepositoryInterface::class);
        $mockRepository->shouldReceive('paginate')->andReturn($mockPaginationEntity);

        $mockInputDto = Mockery::mock(ListCategoriesInputDto::class, ['filter', 'desc']);
        $useCase = new ListCategoriesUseCase($mockRepository);
        $responseUseCase = $useCase->execute($mockInputDto);

        $this->assertInstanceOf(ListCategoriesOutputDto::class, $responseUseCase);
        $this->assertInstanceOf(stdClass::class, $responseUseCase->items[0]);
        $this->assertInstanceOf(stdClass::class, $responseUseCase->items[1]);
        $this->assertCount(2, $responseUseCase->items);

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

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }
}