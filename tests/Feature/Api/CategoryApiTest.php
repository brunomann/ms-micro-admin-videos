<?php

namespace Tests\Feature\Api;

use App\Models\Category;
use GuzzleHttp\Psr7\Response;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Http\Response as HttpResponse;
use Spatie\FlareClient\Http\Response as FlareClientHttpResponse;
use Symfony\Component\HttpFoundation\Response as HttpFoundationResponse;
use Tests\TestCase;

class CategoryApiTest extends TestCase
{
    protected $endpoint = '/api/categories';

    public function testListEmptyCategories()
    {
        $response = $this->getJson($this->endpoint);

        $response->assertStatus(200);
        $response->assertJsonCount(0, 'data');
    }

    public function testListAllCategories()
    {
        $categories = Category::factory()->count(20)->create();
        
        $response = $this->getJson($this->endpoint);

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'meta'  => [
                'total',
                'currentPage',
                'lastPage',
                'firstPage',
                'itemPerPage',
                'to',
                'from',
            ]
        ]);
        $response->assertJsonCount(15, 'data');

    }

    public function testListPaginateCategories()
    {
        $categories = Category::factory()->count(20)->create();
        
        $response = $this->getJson("{$this->endpoint}?page=2");

        $response->assertStatus(200);
        $this->assertEquals(2, $response['meta']['currentPage']);
        $this->assertEquals(20, $response['meta']['total']);
        $response->assertJsonCount(5, 'data');

    }

    public function testShowCategoryNotFound()
    {
        $response = $this->getJson("{$this->endpoint}/fake_value");
       
        $response->assertStatus(HttpResponse::HTTP_NOT_FOUND);
    }

    public function testShowCategory()
    {
        $category = Category::factory()->create();
        
        $response = $this->getJson("{$this->endpoint}/{$category->id}");

        $response->assertStatus(HttpResponse::HTTP_OK);
        $response->assertJsonStructure([
            'data'  => [
                'id',
                'name',
                'description',
                'is_active',
                'created_at',
            ]
        ]);
        $this->assertEquals($category->id,  $response['data']['id']);
    }

    public function testExeceptionStoreCategory()
    {
        $data = [];
        $response = $this->postJson($this->endpoint, $data);
        $response->assertStatus(HttpResponse::HTTP_UNPROCESSABLE_ENTITY);
        $response->assertJsonStructure([
            'message',
            'errors' => [
                'name',
            ]
        ]);
    }

    public function testStore()
    {
        $data = [
            'name'  => 'New Name'
        ];

        $response = $this->postJson($this->endpoint, $data);
        $response->assertStatus(HttpResponse::HTTP_CREATED);
        $response->assertJsonStructure([
            'data' => [
                'id',
                'name',
                'description',
                'is_active',
                'created_at',
            ]
        ]);

        $response = $this->postJson($this->endpoint, [
            'name' => 'New Cat',
            'description' => 'New Desc',
            'is_active' => false,
        ]);

        $response->assertStatus(HttpResponse::HTTP_CREATED);
        $this->assertEquals('New Cat', $response['data']['name']);
        $this->assertEquals('New Desc', $response['data']['description']);
        $this->assertEquals(false, $response['data']['is_active']);
        $response->assertJsonStructure([
            'data' => [
                'id',
                'name',
                'description',
                'is_active',
                'created_at',
            ]
        ]);
        $this->assertDatabaseHas('categories', [
            'id' => $response['data']['id'],
            'is_active' => $response['data']['is_active'],
        ]);
    }

    public function testNotFoundUpdate()
    {
        $data = [
            'name'  => 'New Name'
        ];

        $response = $this->putJson("{$this->endpoint}/{fake_id}", $data);

        $response->assertStatus(HttpResponse::HTTP_NOT_FOUND);
    }

    public function testValidationsUpdate()
    {
        $response = $this->putJson("{$this->endpoint}/{fake_id}", []);

        $response->assertStatus(HttpResponse::HTTP_UNPROCESSABLE_ENTITY);
        $response->assertJsonStructure([
            'message',
            'errors' => [
                'name',
            ]
        ]);
    }

    public function testUpdate()
    {
        $category = Category::factory()->create();

        $response = $this->putJson("{$this->endpoint}/{$category->id}", ['name'=>'Other Cat']);

        $response->assertStatus(HttpResponse::HTTP_OK);
        $response->assertJsonStructure([
            'data' => [
                'id',
                'name',
                'description',
                'is_active',
                'created_at',
            ]
        ]);
        $this->assertDatabaseHas('categories', [
            'name'  => 'Other Cat'
        ]);
    }

    public function testDestroyInvalidCategory()
    {

        $response = $this->deleteJson("{$this->endpoint}/{fake_id}");

        $response->assertStatus(HttpResponse::HTTP_NOT_FOUND);
    }

    public function testDestroy()
    {
        $category = Category::factory()->create();

        $response = $this->deleteJson("{$this->endpoint}/{$category->id}");

        $response->assertStatus(HttpResponse::HTTP_NO_CONTENT);
        $this->assertSoftDeleted('categories', [
            'id'  => $category->id
        ]);
    }

}
