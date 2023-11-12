<?php

namespace Tests\UseCase\Category;

use Core\Domain\Repository\CategoryRepositoryInterface;
use Mockery;
use PHPUnit\Framework\TestCase;
use stdClass;
use Core\UseCase\Category\CreateCategoryUseCase;
use Ramsey\Uuid\Uuid;
use Core\Domain\Entity\Category;
use Core\UseCase\DTO\Category\CreateCategory\{CreateCategoryOutputDto, CreateCategoryInputDto};

class  CreateCategoryUseCaseUnitTest extends TestCase
{
    public function testCreateNewCategory()
    {
        $uuid = Uuid::uuid4()->toString();
        $mockEntity = Mockery::mock(Category::class, [$uuid, 'Name']);
        $mockEntity->shouldReceive('id')->andReturn($uuid);
        $mockEntity->shouldReceive('createdAt')->andReturn(date('Y-m-d H:i:s'));

        $mockRepository = Mockery::mock(CategoryRepositoryInterface::class);
        $mockRepository->shouldReceive('insert')->andReturn($mockEntity);

        $mockInputDto = Mockery::mock(CreateCategoryInputDto::class, ['Name']);

        $useCase = new CreateCategoryUseCase($mockRepository);
        $responseUseCase = $useCase->execute($mockInputDto);

        $this->assertInstanceOf(CreateCategoryOutputDto::class, $responseUseCase);
        $this->assertEquals($responseUseCase->name, $mockInputDto->name);
        $this->assertEquals($responseUseCase->description, '');


        /**
         * Spies
         */

         $mockSpy = Mockery::spy(CategoryRepositoryInterface::class);
         $mockSpy->shouldReceive('insert')->andReturn($mockEntity);

         $useCase = new CreateCategoryUseCase($mockSpy);
         $responseUseCase = $useCase->execute($mockInputDto);
         $mockSpy->shouldHaveReceived('insert');
        Mockery::close();
    }
}