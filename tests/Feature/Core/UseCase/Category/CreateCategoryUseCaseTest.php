<?php

namespace Tests\Feature\Core\UseCase\Category;

use App\Models\Category as CategoryModel;
use App\Repositories\Eloquent\CategoryEloquentRepository;
use Core\UseCase\Category\CreateCategoryUseCase;
use Core\UseCase\DTO\Category\CreateCategory\CreateCategoryInputDto;
use Core\UseCase\DTO\Category\CreateCategory\CreateCategoryOutputDto;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class CreateCategoryUseCaseTest extends TestCase
{

    public function testExecuteUseCase()
    {
        $repository = new CategoryEloquentRepository(new CategoryModel());
        $useCase = new CreateCategoryUseCase($repository);
        $createCategoryDto = new CreateCategoryInputDto('Category Name', 'Category Description');
        $responseUseCase = $useCase->execute($createCategoryDto);

        $this->assertInstanceOf(CreateCategoryOutputDto::class, $responseUseCase);
        $this->assertEquals('Category Name', $responseUseCase->name);
        $this->assertEquals('Category Description', $responseUseCase->description);
        $this->assertNotEmpty($responseUseCase->id);
        $this->assertDatabaseHas('categories', [
            'id' => $responseUseCase->id
        ]);

    }
}
