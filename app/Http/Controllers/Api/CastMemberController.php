<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreCastMemberRequest;
use App\Http\Requests\UpdateCastMemberRequest;
use App\Http\Resources\CastMemberResource;
use Core\UseCase\CastMember\ListCastMembersUseCase;
use Core\UseCase\CastMember\CreateCastMemberUseCase;
use Core\UseCase\CastMember\DeleteCastMemberUseCase;
use Core\UseCase\CastMember\ListCategoriesUseCase;
use Core\UseCase\CastMember\ListCastMemberUseCase;
use Core\UseCase\CastMember\UpdateCastMemberUseCase;
use Core\UseCase\DTO\CastMember\Create\CastMemberCreateInputDto;
use Core\UseCase\DTO\CastMember\List\ListCastMembersInputDto;
use Core\UseCase\DTO\CastMember\CastMemberInputDto;
use Core\UseCase\DTO\CastMember\CreateCastMember\CreateCastMemberInputDto;
use Core\UseCase\DTO\CastMember\DeleteCastMember\DeleteCastMemberInputDto;
use Core\UseCase\DTO\CastMember\ListCastMember\ListCastMemberInputDto;
use Core\UseCase\DTO\CastMember\Update\UpdateCastMemberInputDto;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class CastMemberController extends Controller
{
    public function index(Request $request, ListCastMembersUseCase $useCase)
    {
        $inputDto = new ListCastMembersInputDto(
            filter: $request->get('filter', ''),
            order: $request->get('order', 'DESC'),
            page: (int) $request->get('page', 1),
            totalPages: (int) $request->get('totalPages', 15)
        );

        $response = $useCase->execute(input: $inputDto);

        return CastMemberResource::collection(collect($response->items))
                                    ->additional([
                                        'meta'  => [
                                            'total' => $response->total,
                                            'currentPage' => $response->currentPage,
                                            'lastPage' => $response->lastPage,
                                            'firstPage' => $response->firstPage,
                                            'itemPerPage' => $response->itemPerPage,
                                            'to' => $response->to,
                                            'from' => $response->from,
                                        ]
                                    ]);
    }

    public function store(StoreCastMemberRequest $request, CreateCastMemberUseCase $useCase)
    {
        $response = $useCase->execute(
            input: new CastMemberCreateInputDto(
                name: $request->name,
                type: (int) $request->type
            )
        );

        return (new CastMemberResource($response))->response()->setStatusCode(Response::HTTP_CREATED);
    }

    public function show($id, ListCastMemberUseCase $useCase)
    {
        $response = $useCase->execute(new CastMemberInputDto($id));
        return (new CastMemberResource($response))->response();
    }

    public function update(UpdateCastMemberRequest $request, $id, UpdateCastMemberUseCase $useCase)
    {
        $response = $useCase->execute(
            input: new UpdateCastMemberInputDto(
                id: $id,
                name: $request->name
            )
        );
        // return (new CastMemberResource(collect($response)))->response();
        return (new CastMemberResource($response))->response();
    }

    public function destroy($id, DeleteCastMemberUseCase $useCase)
    {
        $response = $useCase->execute(new CastMemberInputDto(id: $id));
        
        return response()->noContent();
    }
}
