<?php

namespace Tests\Feature\Core\UseCase\Category;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\Category as CategoryModel;
use App\Repositories\Eloquent\CategoryEloquentRepository;
use Core\UseCase\Category\ListCategoryUseCase;
use Core\UseCase\DTO\Category\ListCategory\ListCategoryInputDto;

class ListCategoryUseCaseTest extends TestCase
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
        $useCase = new ListCategoryUseCase($repository);

        $listCategoryDto =  new ListCategoryInputDto($category->id);
        $responseUseCase = $useCase->execute($listCategoryDto);

        $this->assertDatabaseHas('categories', [
            'id' => $category->id
        ]);
        $this->assertEquals($category->id, $responseUseCase->id);
        $this->assertEquals($category->name, $responseUseCase->name);
        $this->assertEquals($category->description, $responseUseCase->description);
    }
}
