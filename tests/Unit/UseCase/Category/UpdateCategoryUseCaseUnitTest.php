<?php

use Core\Domain\Entity\Category as EntityCategory;
use Core\Domain\Repository\CategoryRepositoryInterface;
use Core\UseCase\Category\UpdateCategoryUseCase;
use Core\UseCase\DTO\Category\UpdateCategory\UpdateCategoryInputDto;
use Core\UseCase\DTO\Category\UpdateCategory\UpdateCategoryOutputDto;
use PHPUnit\Framework\TestCase;
use Ramsey\Uuid\Uuid;

class UpdateCategoryUseCaseUnitTest extends TestCase
{
    public function testUpdate()
    {
        $uuid = Uuid::uuid4()->toString();
        $mockEntity = Mockery::mock(EntityCategory::class, [$uuid, 'Name', 'Desc']);
        $mockEntityUpdated = Mockery::mock(EntityCategory::class, [$uuid, 'ReName', 'New Desc']);
        $mockEntity->shouldReceive('update');
        $mockEntityUpdated->shouldReceive('createdAt')->andReturn(date('Y-m-d H:i:s'));


        $mockRepository = Mockery::mock(CategoryRepositoryInterface::class);
        $mockRepository->shouldReceive('findById')->andReturn($mockEntity);
        $mockRepository->shouldReceive('update')->andReturn($mockEntityUpdated);

        $mockInputDto = Mockery::mock(UpdateCategoryInputDto::class, [$uuid, 'ReName', 'New Desc']);

        $useCase = new UpdateCategoryUseCase($mockRepository);
        $responseUseCase = $useCase->execute($mockInputDto);

        $this->assertInstanceOf(UpdateCategoryOutputDto::class, $responseUseCase);
        $this->assertEquals('ReName', $responseUseCase->name);
        $this->assertEquals('New Desc', $responseUseCase->description);
        
    }

    public function testSpyUpdate()
    {
        $uuid = Uuid::uuid4()->toString();
        $mockEntity = Mockery::mock(EntityCategory::class, [$uuid, 'Name', 'Desc']);
        $mockEntityUpdated = Mockery::mock(EntityCategory::class, [$uuid, 'ReName', 'New Desc']);
        $mockEntity->shouldReceive('update');
        $mockEntityUpdated->shouldReceive('createdAt')->andReturn(date('Y-m-d H:i:s'));

        $spyRepository = Mockery::spy(CategoryRepositoryInterface::class);
        $spyRepository->shouldReceive('findById')->andReturn($mockEntity);
        $spyRepository->shouldReceive('update')->andReturn($mockEntityUpdated);

        $mockInputDto = Mockery::mock(UpdateCategoryInputDto::class, [$uuid, 'ReName', 'New Desc']);

        $useCase = new UpdateCategoryUseCase($spyRepository);
        $responseUseCase = $useCase->execute($mockInputDto);

        $spyRepository->shouldHaveReceived('findById');
        $spyRepository->shouldHaveReceived('update');
        $this->assertTrue(true);
        
    }

    protected function tearDown():void
    {
        Mockery::close();
        parent::tearDown();
    }
}