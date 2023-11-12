<?php

namespace Tests\Feature\App\Http\Controllers\Api;

use App\Http\Requests\StoreCastMemberRequest;
use App\Http\Controllers\Api\CastMemberController;
use App\Http\Requests\UpdateCastMemberRequest;
use App\Models\CastMember as CastMemberModel;
use App\Repositories\Eloquent\CastMemberEloquentRepository;
use Core\UseCase\CastMember\CreateCastMemberUseCase;
use Core\UseCase\CastMember\DeleteCastMemberUseCase;
use Core\UseCase\CastMember\ListCastMembersUseCase;
use Core\UseCase\CastMember\ListCastMemberUseCase;
use Core\UseCase\CastMember\UpdateCastMemberUseCase;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\Response;
use Symfony\Component\HttpFoundation\ParameterBag;
use Tests\TestCase;

class CastMemberControllerTest extends TestCase
{
    protected $respository;
    protected $castMemberController;

    protected function setUp():void
    {
        $this->respository = new CastMemberEloquentRepository(
            new CastMemberModel());

        $this->castMemberController = new CastMemberController();
        parent::setUp();
    }

    public function testIndex()
    {
        $useCase = new ListCastMembersUseCase($this->respository);

        $response = $this->castMemberController->index(new Request(), $useCase);

        $this->assertInstanceOf(AnonymousResourceCollection::class, $response);
        $this->assertArrayHasKey('meta', $response->additional);
    }

    public function testStore()
    {
        $useCase = new CreateCastMemberUseCase($this->respository);
        
        $request = new StoreCastMemberRequest();
        $request->headers->set('content-type', 'application/json');
        $request->setJson(new ParameterBag([
            'name'  => 'Teste',
            'type'  => 1
        ]));

        $response = $this->castMemberController->store($request, $useCase);

        $this->assertInstanceOf(JsonResponse::class, $response);
        $this->assertEquals(Response::HTTP_CREATED, $response->status());
    }

    public function testShow()
    {
        $castMember = CastMemberModel::factory()->create();

        $response = $this->castMemberController->show(
            id: $castMember->id,
            useCase: new ListCastMemberUseCase($this->respository)
        );
        $this->assertInstanceOf(JsonResponse::class, $response);
        $this->assertEquals(Response::HTTP_OK, $response->status());
    }

    public function testUpdate()
    {
        $castMember = CastMemberModel::factory()->create();

        $useCase = new UpdateCastMemberUseCase($this->respository);

        $request = new UpdateCastMemberRequest();
        $request->headers->set('content-type', 'application/json');
        $request->setJson(new ParameterBag([
            'name'  => 'Updated',
            'type'  => 2
        ]));

        $response = $this->castMemberController->update(
            request: $request,
            id: $castMember->id,
            useCase: $useCase
        );
        
        $this->assertInstanceOf(JsonResponse::class, $response);
        $this->assertEquals(Response::HTTP_OK, $response->status());
        $this->assertDatabaseHas('cast_members', [
            'name'  => 'Updated',
        ]);
    }

    public function testDestroy()
    {
        $castMember = CastMemberModel::factory()->create();

        $useCase = new DeleteCastMemberUseCase($this->respository);

        $response = $this->castMemberController->destroy(id: $castMember->id, useCase: $useCase);

        $this->assertEquals(Response::HTTP_NO_CONTENT, $response->status());
        $this->assertSoftDeleted('cast_members', [
            'id'  => $castMember->id,
        ]);
    }
}
