<?php

namespace Tests\Feature\Api;

use App\Models\CastMember as CastMemberModel;
use Illuminate\Http\Response;
use Tests\TestCase;

class CastMemberApiTest extends TestCase
{
    private $endpoint = '/api/cast_members';

    public function testGetAllCastMemberEmpty()
    {
        $response = $this->getJson($this->endpoint);

        $response->assertStatus(Response::HTTP_OK);
        $response->assertJsonCount(0, 'data');
    }

    public function testGetAllCastMember()
    {
        CastMemberModel::factory()->count(50)->create();

        $response = $this->getJson($this->endpoint);

        $response->assertStatus(Response::HTTP_OK);
        $response->assertJsonCount(15, 'data');
        $response->assertJsonStructure([
            'meta' => [
                'total',
                'currentPage',
                'lastPage',
                'firstPage',
                'itemPerPage',
                'to',
                'from',
            ],
        ]);
    }

    public function testPagination()
    {
        CastMemberModel::factory()->count(20)->create();

        $response = $this->getJson("{$this->endpoint}?page=2");

        $response->assertStatus(Response::HTTP_OK);
        $response->assertJsonCount(5, 'data');
        
        $this->assertEquals(2, $response['meta']['currentPage']);
        $this->assertEquals(20, $response['meta']['total']);
    }

    public function testPaginationWithFilter()
    {
        CastMemberModel::factory()->count(20)->create();
        CastMemberModel::factory()->count(1)->create(['name' => 'AAAAAA']);
        CastMemberModel::factory()->count(1)->create(['name' => 'AAAAAA']);
        CastMemberModel::factory()->count(1)->create(['name' => 'BBBBBB']);

        $response = $this->getJson("{$this->endpoint}?filter=AAAAAA");

        $response->assertStatus(Response::HTTP_OK);
        $response->assertJsonCount(2, 'data');
        
        $this->assertEquals(2, $response['meta']['total']);
    }

    public function testShowByIdNotFound()
    {
        $response = $this->getJson("{$this->endpoint}/fake_id");

        $response->assertStatus(Response::HTTP_NOT_FOUND);
    }

    public function testShowById()
    {
        $castMember = CastMemberModel::factory()->create();

        $response = $this->getJson("{$this->endpoint}/{$castMember->id}");

        $response->assertStatus(Response::HTTP_OK);

        $this->assertEquals($castMember->id,  $response['data']['id']);
        $this->assertEquals($castMember->name,  $response['data']['name']);
        $this->assertEquals($castMember->type->value,  $response['data']['type']);
        $this->assertEquals($castMember->created_at,  $response['data']['created_at']);
    }

    public function testStoreInvalid()
    {
        $response = $this->postJson($this->endpoint, []);

        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
        $response->assertJsonStructure([
            'message',
            'errors' => [
                'name',
                'type',
            ]
        ]);
    }

    public function testStore()
    {
        $response = $this->postJson($this->endpoint, [
            'name' => 'Bruno Ramos',
            'type'  => 1
        ]);

        $response->assertStatus(Response::HTTP_CREATED);
        $response->assertJsonStructure([
            'data' => [
                'id',
                'name',
                'type',
                'created_at',
            ]
        ]);
        $this->assertDatabaseHas('cast_members', ['name' => 'Bruno Ramos']);
    }

    public function testUpdateNotFound()
    {
        $response = $this->putJson("{$this->endpoint}/fake_id", [
            'name' => 'Bruno',
            'type' => 1,
        ]);

        $response->assertStatus(Response::HTTP_NOT_FOUND);
    }

    public function testUpdateInvalidParameters()
    {
        $castMember = CastMemberModel::factory()->create();

        $response = $this->putJson("{$this->endpoint}/$castMember->id", [
            '' => '',
        ]);

        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
        $response->assertJsonStructure([
            'message', 
            'errors' => [
                'name',
            ]
        ]);
    }

    public function testUpdate()
    {
        $castMember = CastMemberModel::factory()->create();

        $response = $this->putJson("{$this->endpoint}/$castMember->id", [
            'name' => 'Bruno',
            'type' => 2,
        ]);

        $response->assertStatus(Response::HTTP_OK);
        $response->assertJsonStructure([
            'data' => [
                'id',
                'name',
                'type',
                'created_at',
            ]
        ]);
        $this->assertDatabaseHas('cast_members', ['name' => 'Bruno']);
    }

    public function testDeleteNotFound()
    {
        $response = $this->deleteJson("{$this->endpoint}/fake_value");

        $response->assertStatus(Response::HTTP_NOT_FOUND);
    }

    public function testDelete()
    {
        $castMember = CastMemberModel::factory()->create();

        $response = $this->deleteJson("{$this->endpoint}/$castMember->id");

        $response->assertStatus(Response::HTTP_NO_CONTENT);
        $this->assertSoftDeleted('cast_members', ['id' => $castMember->id]);
    }
}

