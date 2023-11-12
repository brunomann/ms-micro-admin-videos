<?php

namespace Tests\Feature\Core\UseCase\Category;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\Category as CategoryModel;
use App\Repositories\Eloquent\CategoryEloquentRepository;
use Core\UseCase\Category\UpdateCategoryUseCase;
use Core\UseCase\DTO\Category\UpdateCategory\UpdateCategoryInputDto;

class UpdateCategoryUseCaseTest extends TestCase
{
    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function testExecuteUseCase()
    {
        $category = CategoryModel::factory()->create();
        $repository = new CategoryEloquentRepository(new CategoryModel());
        $useCase = new UpdateCategoryUseCase($repository);

        $updateCategoryDto =  new UpdateCategoryInputDto($category->id, 'Category Updated', 'Description Updated');
        $responseUseCase = $useCase->execute($updateCategoryDto);

        $this->assertEquals($category->id, $responseUseCase->id);
        $this->assertEquals($responseUseCase->name, 'Category Updated');
        $this->assertEquals($responseUseCase->description, 'Description Updated');
    }
}
