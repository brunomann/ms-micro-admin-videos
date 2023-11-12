<?php

namespace Tests\Feature\Core\UseCase\Category;

use App\Models\Category as CategoryModel;
use App\Repositories\Eloquent\CategoryEloquentRepository;
use Core\Domain\Entity\Category as CategoryEntity;
use Core\UseCase\Category\CreateCategoryUseCase;
use Core\UseCase\Category\DeleteCategoryUseCase;
use Core\UseCase\DTO\Category\CreateCategory\CreateCategoryInputDto;
use Core\UseCase\DTO\Category\CreateCategory\CreateCategoryOutputDto;
use Core\UseCase\DTO\Category\DeleteCategory\DeleteCategoryInputDto;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;


class DeleteCategoryUseCaseTest extends TestCase
{
    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function testExecuteUseCase()
    {
        $categoryBd = CategoryModel::factory()->create();

        $this->assertDatabaseHas('categories', [
            'id' => $categoryBd->id
        ]);

        $repository = new CategoryEloquentRepository(new CategoryModel());
        $useCase = new DeleteCategoryUseCase($repository);
        $deleteCategoryDto = new DeleteCategoryInputDto($categoryBd->id);
        $responseUseCase = $useCase->execute($deleteCategoryDto);

        $this->assertTrue($responseUseCase->success);
        $this->assertSoftDeleted('categories', [
            'id' => $categoryBd->id
        ]);
    }
}
