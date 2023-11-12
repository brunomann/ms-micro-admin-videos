<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreGenre;
use App\Http\Requests\UpdateGenre;
use App\Http\Resources\GenreResource;
use Core\UseCase\DTO\Genre\Create\GenreCreateInputDto;
use Core\UseCase\DTO\Genre\GenreInputDto;
use Core\UseCase\DTO\Genre\List\ListGenresInputDto;
use Core\UseCase\DTO\Genre\Update\GenreUpdateInputDto;
use Core\UseCase\Genre\CreateGenreUseCase;
use Core\UseCase\Genre\DeleteGenreUseCase;
use Core\UseCase\Genre\ListGenresUseCase;
use Core\UseCase\Genre\ListGenreUseCase;
use Core\UseCase\Genre\UpdateGenreUseCase;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class GenreController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request, ListGenresUseCase $useCase)
    {
        $response = $useCase->execute(
            new ListGenresInputDto(
                filter: $request->get('filter', ''),
                order: $request->get('order', 'DESC'),
                page: (int) $request->get('page', 1),
                totalPages: (int) $request->get('totalPages', 15)
            )
        );

        return GenreResource::collection(collect($response->items))
        ->additional([
            'meta'  => [
                'total' => $response->total,
                'current_page' => $response->currentPage,
                'last_page' => $response->lastPage,
                'first_page' => $response->firstPage,
                'per_page' => $response->itemPerPage,
                'to' => $response->to,
                'from' => $response->from,
            ]
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \App\Http\Requests\StoreGenre $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreGenre $request, CreateGenreUseCase $useCase)
    {
        $response = $useCase->execute(
            new GenreCreateInputDto(
                $request->name,
                (bool) $request->is_active,
                $request->categories_ids
            )
        );

        return (new GenreResource($response))
                    ->response()
                    ->setStatusCode(Response::HTTP_CREATED);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(ListGenreUseCase $useCase, $id)
    {
        $response = $useCase->execute(
            new GenreInputDto(
                $id
            )
        );

        return new GenreResource($response);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateGenre $request, UpdateGenreUseCase $useCase, $id)
    {
        $response = $useCase->execute(new GenreUpdateInputDto(
            $id,
            $request->name,
            $request->categories_ids
        ));

        return new GenreResource($response);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(DeleteGenreUseCase $useCase, $id)
    {
        $response = $useCase->execute(
            new GenreInputDto($id)
        );

        return response()->noContent();

    }
}
