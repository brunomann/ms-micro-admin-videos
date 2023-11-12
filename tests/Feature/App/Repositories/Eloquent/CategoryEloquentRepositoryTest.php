<?php

namespace Tests\Feature\App\Repositories\Eloquent;

use Core\Domain\Exception\NotFoundException;
use App\Models\Category as ModelCategory;
use App\Repositories\Eloquent\CategoryEloquentRepository;
use Core\Domain\Entity\Category as EntityCategory;
use Core\Domain\Repository\CategoryRepositoryInterface;
use Core\Domain\Repository\PaginationInterface;
use Tests\TestCase;
use Throwable;

class CategoryEloquentRepositoryTest extends TestCase
{
    protected $repository;

    protected function setUp():void
    {
        parent::setUp();
        $this->repository = new CategoryEloquentRepository(new ModelCategory());
    }

    public function testInsert()
    {
        $entity = new EntityCategory(
            name: 'Test'
        );

        $response = $this->repository->insert($entity);

        $this->assertInstanceOf(CategoryRepositoryInterface::class, $this->repository);
        $this->assertInstanceOf(EntityCategory::class, $response);
        $this->assertDatabaseHas('categories', [
            'name' => $entity->name
        ]);
    }

    public function testFindById()
    {
        $category = ModelCategory::factory()->create();

        $response = $this->repository->findById($category->id);

        $this->assertInstanceOf(EntityCategory::class, $response);
        $this->assertEquals($category->id, $response->id());
        $this->assertEquals($category->name, $response->name);
        $this->assertEquals($category->description, $response->description);
    }

    public function testFindByIdNotFound()
    {
        try{
            $response = $this->repository->findById('1234');
            $this->assertTrue(false);
        }catch(Throwable $th){
            $this->assertInstanceOf(NotFoundException::class, $th);
        }
    }

    public function testFindAll()
    {
        $categories = ModelCategory::factory()->count(10)->create();

        $response = $this->repository->findAll();

        $this->assertCount(count($categories), $response);
    }

    public function testPaginate()
    {
        $categories = ModelCategory::factory()->count(20)->create();

        $response = $this->repository->paginate();

        $this->assertInstanceOf(PaginationInterface::class, $response);
        $this->assertCount(15, $response->items());
    }

    public function testPaginateWithoutData()
    {
        $response = $this->repository->paginate();

        $this->assertInstanceOf(PaginationInterface::class, $response);
        $this->assertCount(0, $response->items());
    }

    public function testUpdateIdNotFound()
    {
        try{
            $category = new EntityCategory(name: 'Test');
            $response = $this->repository->update($category);
            $this->assertTrue(false);
        }catch(Throwable $th){
            $this->assertInstanceOf(NotFoundException::class, $th);
        }
    }

    public function testUpdate()
    {
        $categoryBd = ModelCategory::factory()->create();

        $category = new EntityCategory(
            id: $categoryBd->id,
            name: 'Test updated',
        );

        $response = $this->repository->update($category);

        $this->assertInstanceOf(EntityCategory::class, $response);
        $this->assertNotEquals($response->name, $categoryBd->name);
        $this->assertEquals('Test updated', $response->name);
    }

    public function testDeleteIdNotFound()
    {
        try{
            $response = $this->repository->delete('1234');

            $this->assertTrue(false);
        }catch(Throwable $th){
            $this->assertInstanceOf(NotFoundException::class, $th);
        }
    }

    public function testDelete()
    {
        $category = ModelCategory::factory()->create();

        $response = $this->repository
                            ->delete($category->id);

        $this->assertTrue($response);
    }
}
