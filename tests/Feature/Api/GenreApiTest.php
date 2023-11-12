<?php

namespace Tests\Feature\Api;

use App\Models\Category as CategoryModel;
use App\Models\Genre as GenreModel;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Http\Response as HttpResponse;
use Tests\TestCase;

class GenreApiTest extends TestCase
{
    protected $endpoint = '/api/genres';

    public function testListAllEmpty()
    {
        $response = $this->getJson($this->endpoint);

        $response->assertStatus(HttpResponse::HTTP_OK);
        $response->assertJsonCount(0, 'data');
    }

    public function testListAll()
    {
        GenreModel::factory()->count(20)->create();

        $response = $this->getJson($this->endpoint);

        $response->assertStatus(HttpResponse::HTTP_OK);
        $response->assertJsonCount(15, 'data');
        $response->assertJsonStructure([
            'meta' => [
                'total',
                'current_page',
                'last_page',
                'first_page',
                'per_page',
                'to',
                'from',
            ]
        ]);
    }

    public function testStore()
    {
        $categories = CategoryModel::factory()->count(3)->create();

        $response = $this->postJson($this->endpoint, [
            'name' => 'Test Genre Store',
            'is_active' => true,
            'categories_ids' => $categories->pluck('id')->toArray()
        ]);

        $response->assertStatus(HttpResponse::HTTP_CREATED);
        $response->assertJsonStructure([
            'data' => [
                'id',
                'name',
                'is_active',
            ]
        ]);
    }

    public function testValidationsStore()
    {
        $categories = CategoryModel::factory()->count(3)->create();

        $payload = [
            'name' => '',
            'is_active' => true,
            'categories_ids' => $categories->pluck('id')->toArray()
        ];

        $response = $this->postJson($this->endpoint, $payload);

        $response->assertStatus(HttpResponse::HTTP_UNPROCESSABLE_ENTITY);
        $response->assertJsonStructure([
            'message',
            'errors' => [
                'name'
            ]
        ]);
    }

    public function testShowNotFound()
    {
        $category = CategoryModel::factory()->create();

        $response = $this->getJson("{$this->endpoint}/fake_id");

        $response->assertStatus(HttpResponse::HTTP_NOT_FOUND);
    }

    public function testShow()
    {
        $genre = GenreModel::factory()->create();

        $response = $this->getJson("{$this->endpoint}/{$genre->id}");

        $response->assertStatus(HttpResponse::HTTP_OK);
        $response->assertJsonStructure([
            'data' => [
                'id',
                'name',
                'is_active',
            ]
        ]);
    }

    public function testUpdateNotFound()
    {
        $categories = CategoryModel::factory()->count(3)->create();

        $response = $this->putJson("{$this->endpoint}/fake_id", [
            'name'  => 'New Name',
            'categories_ids' => $categories->pluck('id')->toArray()
        ]);
        $response->assertStatus(HttpResponse::HTTP_NOT_FOUND);

    }

    public function testValidationsUpdate()
    {
        $response = $this->putJson("{$this->endpoint}/fake_id", [
            'name'  => 'New Name',
            'categories_ids' => []
        ]);

        $response->assertStatus(HttpResponse::HTTP_NOT_FOUND);
        $response->assertJsonStructure([
            'message',
        ]);
    }

    public function testUpdate()
    {
        $genre = GenreModel::factory()->create();
        $categories = CategoryModel::factory()->count(3)->create();

        $response = $this->putJson("{$this->endpoint}/{$genre->id}", [
            'name'  => 'New Name',
            'categories_ids' => $categories->pluck('id')->toArray()
        ]);
        $response->assertStatus(HttpResponse::HTTP_OK);
        $response->assertJsonStructure([
            'data' => [
                'id',
                'name',
                'is_active',
            ]
        ]);
    }

    public function testDeleteNotFound()
    {
        $response = $this->deleteJson("{$this->endpoint}/fake_id");

        $response->assertStatus(HttpResponse::HTTP_NOT_FOUND);
    }

    public function testDelete()
    {
        $genre = GenreModel::factory()->create();
        $response = $this->deleteJson("{$this->endpoint}/{$genre->id}");
         
        $response->assertStatus(HttpResponse::HTTP_NO_CONTENT);
    }
}
