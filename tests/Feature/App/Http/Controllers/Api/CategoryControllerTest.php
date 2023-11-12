<?php

namespace Tests\Feature\App\Http\Controllers\Api;

use App\Http\Requests\StoreCategoryRequest;
use App\Http\Controllers\Api\CategoryController;
use App\Http\Requests\UpdateCategoryRequest;
use App\Models\Category as CategoryModel;
use App\Repositories\Eloquent\CategoryEloquentRepository;
use Core\Domain\Entity\Category;
use Core\UseCase\Category\CreateCategoryUseCase;
use Core\UseCase\Category\DeleteCategoryUseCase;
use Core\UseCase\Category\ListCategoriesUseCase;
use Core\UseCase\Category\ListCategoryUseCase;
use Core\UseCase\Category\UpdateCategoryUseCase;
use Database\Factories\CategoryFactory;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Http\Response;
use Symfony\Component\HttpFoundation\ParameterBag;
use Tests\TestCase;

class CategoryControllerTest extends TestCase
{
    protected $respository;
    protected $categoryController;

    protected function setUp():void
    {
        $this->respository = new CategoryEloquentRepository(
            new CategoryModel());

        $this->categoryController = new CategoryController();
        parent::setUp();
    } 

    public function testIndex()
    {
        $useCase = new ListCategoriesUseCase($this->respository);


        $response = $this->categoryController->index(new Request(), $useCase);

        $this->assertInstanceOf(AnonymousResourceCollection::class, $response);
        $this->assertArrayHasKey('meta', $response->additional);
    }

    public function testStore()
    {
        $useCase = new CreateCategoryUseCase($this->respository);
        
        $request = new StoreCategoryRequest();
        $request->headers->set('content-type', 'application/json');
        $request->setJson(new ParameterBag([
            'name'  => 'Teste',
        ]));

        $response = $this->categoryController->store($request, $useCase);

        $this->assertInstanceOf(JsonResponse::class, $response);
        $this->assertEquals(Response::HTTP_CREATED, $response->status());
    }

    public function testShow()
    {
        $category = CategoryModel::factory()->create();

        $response = $this->categoryController->show(
            id: $category->id,
            useCase: new ListCategoryUseCase($this->respository)
        );
        $this->assertInstanceOf(JsonResponse::class, $response);
        $this->assertEquals(Response::HTTP_OK, $response->status());
    }

    public function testUpdate()
    {
        $category = CategoryModel::factory()->create();

        $useCase = new UpdateCategoryUseCase($this->respository);

        $request = new UpdateCategoryRequest();
        $request->headers->set('content-type', 'application/json');
        $request->setJson(new ParameterBag([
            'name'  => 'Updated',
        ]));

        $response = $this->categoryController->update(
            request: $request,
            id: $category->id,
            useCase: $useCase
        );
        
        $this->assertInstanceOf(JsonResponse::class, $response);
        $this->assertEquals(Response::HTTP_OK, $response->status());
        $this->assertDatabaseHas('categories', [
            'name'  => 'Updated',
        ]);
    }

    public function testDestroy()
    {
        $category = CategoryModel::factory()->create();

        $useCase = new DeleteCategoryUseCase($this->respository);

        $response = $this->categoryController->destroy(id: $category->id, useCase: $useCase);

        // $this->assertInstanceOf(JsonResponse::class, $response);
        $this->assertEquals(Response::HTTP_NO_CONTENT, $response->status());
        $this->assertSoftDeleted('categories', [
            'id'  => $category->id,
        ]);
    }
}
