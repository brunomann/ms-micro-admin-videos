<?php

namespace Tests\Feature\Core\UseCase\Category;


use App\Models\Category as CategoryModel;
use App\Repositories\Eloquent\CategoryEloquentRepository;
use Core\Domain\Entity\Category as CategoryEntity;
use Core\UseCase\Category\CreateCategoryUseCase;
use Core\UseCase\Category\DeleteCategoryUseCase;
use Core\UseCase\Category\ListCategoriesUseCase;
use Core\UseCase\DTO\Category\CreateCategory\CreateCategoryInputDto;
use Core\UseCase\DTO\Category\CreateCategory\CreateCategoryOutputDto;
use Core\UseCase\DTO\Category\DeleteCategory\DeleteCategoryInputDto;
use Core\UseCase\DTO\Category\ListCategories\ListCategoriesInputDto;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class ListCategoriesUseCaseTest extends TestCase
{
    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function testExecuteUseCase()
    {
        $categories = CategoryModel::factory()->count(20)->create();

        $repository = new CategoryEloquentRepository(new CategoryModel());
        $useCase = new ListCategoriesUseCase($repository);
        
        $listCategoriesDto = new ListCategoriesInputDto();
        $responseUseCase = $useCase->execute($listCategoriesDto);

        $this->assertCount($listCategoriesDto->totalPages, $responseUseCase->items);
        $this->assertEquals(count($categories), $responseUseCase->total);
    }

    public function testExecuteUseCaseWithoutData()
    {
        $repository = new CategoryEloquentRepository(new CategoryModel());
        $useCase = new ListCategoriesUseCase($repository);
        
        $listCategoriesDto = new ListCategoriesInputDto();
        $responseUseCase = $useCase->execute($listCategoriesDto);

        $this->assertCount(0, $responseUseCase->items);
    }
}
