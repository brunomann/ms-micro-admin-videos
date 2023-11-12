<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreCategoryRequest;
use App\Http\Requests\UpdateCategoryRequest;
use App\Http\Resources\CategoryResource;
use Core\UseCase\Category\CreateCategoryUseCase;
use Core\UseCase\Category\DeleteCategoryUseCase;
use Core\UseCase\Category\ListCategoriesUseCase;
use Core\UseCase\Category\ListCategoryUseCase;
use Core\UseCase\Category\UpdateCategoryUseCase;
use Core\UseCase\DTO\Category\CreateCategory\CreateCategoryInputDto;
use Core\UseCase\DTO\Category\DeleteCategory\DeleteCategoryInputDto;
use Core\UseCase\DTO\Category\ListCategories\ListCategoriesInputDto;
use Core\UseCase\DTO\Category\ListCategory\ListCategoryInputDto;
use Core\UseCase\DTO\Category\UpdateCategory\UpdateCategoryInputDto;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class CategoryController extends Controller
{
    public function index(Request $request, ListCategoriesUseCase $useCase)
    {
        $inputDto = new ListCategoriesInputDto(
            filter: $request->get('filter', ''),
            order: $request->get('order', 'DESC'),
            page: (int) $request->get('page', 1),
            totalPages: (int) $request->get('totalPages', 15)
        );

        $response = $useCase->execute(input: $inputDto);

        return CategoryResource::collection(collect($response->items))
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

    public function store(StoreCategoryRequest $request, CreateCategoryUseCase $useCase)
    {
        $response = $useCase->execute(
            input: new CreateCategoryInputDto(
                name: $request->name,
                description: $request->description ?? '',
                is_active: (bool) $request->is_active ?? true,
            )
        );

        return (new CategoryResource($response))->response()->setStatusCode(Response::HTTP_CREATED);
        // return CategoryResource::collection(collect($response))
        //             ->response()
        //             ->setStatusCode(Response::HTTP_CREATED);
    }

    public function show($id, ListCategoryUseCase $useCase)
    {
        $response = $useCase->execute(new ListCategoryInputDto($id));
        return (new CategoryResource($response))->response();
    }

    public function update(UpdateCategoryRequest $request, $id, UpdateCategoryUseCase $useCase)
    {
        $response = $useCase->execute(
            input: new UpdateCategoryInputDto(
                id: $id,
                name: $request->name
            )
        );
        // return (new CategoryResource(collect($response)))->response();
        return (new CategoryResource($response))->response();
    }

    public function destroy($id, DeleteCategoryUseCase $useCase)
    {
        $response = $useCase->execute(new DeleteCategoryInputDto(id: $id));
        
        return response()->noContent();
    }
}
