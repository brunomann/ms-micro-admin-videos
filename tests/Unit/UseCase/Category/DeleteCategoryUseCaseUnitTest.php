<?php

use Core\Domain\Entity\Category as EntityCategory;
use Core\Domain\Repository\CategoryRepositoryInterface;
use Core\UseCase\Category\DeleteCategoryUseCase;
use Core\UseCase\Category\UpdateCategoryUseCase;
use Core\UseCase\DTO\Category\DeleteCategory\DeleteCategoryInputDto;
use Core\UseCase\DTO\Category\DeleteCategory\DeleteCategoryOutputDto;
use Core\UseCase\DTO\Category\UpdateCategory\UpdateCategoryInputDto;
use Core\UseCase\DTO\Category\UpdateCategory\UpdateCategoryOutputDto;
use PHPUnit\Framework\TestCase;
use Ramsey\Uuid\Uuid;

class DeleteCategoryUseCaseUnitTest extends TestCase
{
    public function testDelete()
    {
        $uuid = Uuid::uuid4()->toString();
        $mockEntity = Mockery::mock(EntityCategory::class, [$uuid, 'Name', 'Desc']);

        $mockRepository = Mockery::mock(CategoryRepositoryInterface::class);
        $mockRepository->shouldReceive('delete')->andReturn(true);

        $mockInputDto = Mockery::mock(DeleteCategoryInputDto::class, [$uuid]);

        $useCase = new DeleteCategoryUseCase($mockRepository);
        $responseUseCase = $useCase->execute($mockInputDto);

        $this->assertInstanceOf(DeleteCategoryOutputDto::class, $responseUseCase);
        $this->assertTrue($responseUseCase->success);
    }

    public function testSpyDelete()
    {
        $uuid = Uuid::uuid4()->toString();
        $mockEntity = Mockery::mock(EntityCategory::class, [$uuid, 'Name', 'Desc']);

        $spyRepository = Mockery::spy(CategoryRepositoryInterface::class);
        $spyRepository->shouldReceive('delete')->andReturn(true);

        $mockInputDto = Mockery::mock(DeleteCategoryInputDto::class, [$uuid]);

        $useCase = new DeleteCategoryUseCase($spyRepository);
        $responseUseCase = $useCase->execute($mockInputDto);

        $spyRepository->shouldHaveReceived('delete');
        $this->assertTrue(true);
        
    }

    protected function tearDown():void
    {
        Mockery::close();
        parent::tearDown();
    }
}