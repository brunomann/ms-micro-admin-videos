<?php

namespace Tests\Unit\UseCase\Category;

use Core\Domain\Entity\Category;
use Core\Domain\Repository\CategoryRepositoryInterface;
use Core\UseCase\Category\ListCategoryUseCase;
use Core\UseCase\DTO\Category\ListCategory\{ListCategoryInputDto, ListCategoryOutputDto} ;
use Mockery;
use Mockery\Mock;
use PHPUnit\Framework\TestCase;
use Ramsey\Uuid\Uuid;

class ListCategoryUseCaseUnitTest extends TestCase
{
    public function testGetById()
    {
        $id = Uuid::uuid4()->toString();
        $mockEntity = Mockery::mock(Category::class, [$id, 'Name']);
        $mockEntity->shouldReceive('id')->andReturn($id);
        $mockEntity->shouldReceive('createdAt')->andReturn(date('Y-m-d H:i:s'));


        $mockRepository = Mockery::mock(CategoryRepositoryInterface::class);
        $mockRepository->shouldReceive('findById')
                        ->with($id)
                        ->andReturn($mockEntity);

        $mockInputDto = Mockery::mock(ListCategoryInputDto::class, [$id]);
        $useCase = new ListCategoryUseCase($mockRepository);
        $responseUseCase = $useCase->execute($mockInputDto);

        $this->assertInstanceOf(ListCategoryOutputDto::class, $responseUseCase);
        $this->assertEquals($mockEntity->id(), $responseUseCase->id);
        $this->assertEquals($mockEntity->name, $responseUseCase->name);
    }

    public function testSpyGetById()
    {
        $id = Uuid::uuid4()->toString();
        $mockEntity = Mockery::mock(Category::class, [$id, 'Name']);
        $mockEntity->shouldReceive('id')->andReturn($id);
        $mockEntity->shouldReceive('createdAt')->andReturn(date('Y-m-d H:i:s'));


        $spyRepository = Mockery::spy(CategoryRepositoryInterface::class);
        $spyRepository->shouldReceive('findById')
                        ->with($id)
                        ->andReturn($mockEntity);

        $mockInputDto = Mockery::mock(ListCategoryInputDto::class, [$id]);
        $useCase = new ListCategoryUseCase($spyRepository);
        $responseUseCase = $useCase->execute($mockInputDto);

        $spyRepository->shouldHaveReceived('findById');
        $this->assertTrue(true);
    }

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }
}