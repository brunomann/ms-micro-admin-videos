<?php

namespace Tests\Feature\Core\UseCase\Genre;

use App\Models\Genre as GenreModel;
use App\Repositories\Eloquent\CategoryEloquentRepository;
use App\Repositories\Eloquent\GenreEloquentRepository;
use App\Repositories\Transaction\DBTransaction;
use Core\UseCase\DTO\Genre\Create\GenreCreateInputDto;
use Core\UseCase\DTO\Genre\GenreInputDto;
use Core\UseCase\DTO\Genre\List\ListGenresInputDto;
use Core\UseCase\Genre\CreateGenreUseCase;
use Core\UseCase\Genre\ListGenreUseCase;
use Tests\TestCase;

class ListGenreUseCaseTest extends TestCase
{
    public function testFindById()
    {
        $genre = GenreModel::factory()->create();
        $genreRepository = new GenreEloquentRepository(new GenreModel());
        $useCase = new ListGenreUseCase($genreRepository);

        $genreDto = new GenreInputDto($genre->id);
        $response = $useCase->execute($genreDto);

        $this->assertEquals($genre->id, $response->id);
        $this->assertEquals($genre->name, $response->name);
    }
}
