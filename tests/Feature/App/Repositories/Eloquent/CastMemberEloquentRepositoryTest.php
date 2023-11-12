<?php

namespace Tests\Feature\App\Repositories\Eloquent;

use App\Models\CastMember as CastMemberModel;
use Core\Domain\Entity\CastMember as CastMemberEntity;
use App\Repositories\Eloquent\CastMemberEloquentRepository;
use Core\Domain\Enum\CastMemberType;
use Core\Domain\Exception\NotFoundException;
use Core\Domain\Repository\CastMemberRepositoryInterface;
use Core\Domain\ValueObject\Uuid;
use Tests\TestCase;

class CastMemberEloquentRepositoryTest extends TestCase
{
    protected $repository; 

    protected function setUp():void
    {
        parent::setUp();

        $this->repository = new CastMemberEloquentRepository(new CastMemberModel());
    }

    public function testCheckImplementsInterfaceRepository()
    {

        $this->assertInstanceOf(CastMemberRepositoryInterface::class, $this->repository);
    }

    public function testInsert()
    {
        $castMember = new CastMemberEntity('Bruno', CastMemberType::DIRECTOR);

        $response = $this->repository->insert($castMember);

        $this->assertDatabaseHas('cast_members', [
            'id' => $response->id()
        ]);
        $this->assertEquals($castMember->name, $response->name);
        $this->assertEquals($castMember->type->value, $response->type->value);
    }

    public function testFindByIdNotFound()
    {
        $this->expectException(NotFoundException::class);

        $response = $this->repository->findById('fake_id');
    }

    public function testFindById()
    {
        $castMember = CastMemberModel::factory()->create();

        $response = $this->repository->findById($castMember->id);

        $this->assertEquals($castMember->id, $response->id());
        $this->assertEquals($castMember->name, $response->name);
        $this->assertEquals($castMember->type->value, $response->type->value);
    }

    public function testFindAllEmpty()
    {
        $response = $this->repository->findAll();

        $this->assertCount(0, $response);
    }

    public function testFindAll()
    {
        CastMemberModel::factory()->count(10)->create();

        $response = $this->repository->findAll();

        $this->assertCount(10, $response);
    }

    public function testPaginate()
    {
        CastMemberModel::factory()->count(50)->create();

        $response = $this->repository->paginate();

        $this->assertCount(15, $response->items());
        $this->assertEquals(50, $response->total());
    }

    public function testPaginatePageTwo()
    {
        CastMemberModel::factory()->count(20)->create();

        $response = $this->repository->paginate('', 'ASC', 2, 10);

        $this->assertCount(10, $response->items());
        $this->assertEquals(20, $response->total());
    }

    public function testUpdateNotFound()
    {
        $this->expectException(NotFoundException::class);

        $castMember = new CastMemberEntity('Bruno', CastMemberType::ACTOR);

        $this->repository->update($castMember);
    }

    public function testUpdate()
    {
        $castMember = CastMemberModel::factory()->create();

        $castMemberEntity = new CastMemberEntity('Bruno Mann', CastMemberType::ACTOR, new Uuid( $castMember->id));

        $response = $this->repository->update($castMemberEntity);

        $this->assertNotEquals($castMember->name, $response->name);
        $this->assertEquals('Bruno Mann', $response->name);
    }

    public function testDeleteNotFound()
    {
        $this->expectException(NotFoundException::class);

        $this->repository->delete('fake_value');
    }

    public function testDeleteNot()
    {
        $castMember = CastMemberModel::factory()->create();

        $response = $this->repository->delete($castMember->id);

        $this->assertTrue($response);
        $this->assertSoftDeleted('cast_members', [
            'id' => $castMember->id
        ]);
    }
}