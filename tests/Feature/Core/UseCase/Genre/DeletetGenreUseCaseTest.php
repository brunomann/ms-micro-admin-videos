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
use Core\UseCase\Genre\DeleteGenreUseCase;
use Core\UseCase\Genre\ListGenreUseCase;
use Tests\TestCase;

class DeleteGenreUseCaseTest extends TestCase
{
    public function testDelete()
    {
        $genre = GenreModel::factory()->create();
        $genreRepository = new GenreEloquentRepository(new GenreModel());
        $useCase = new DeleteGenreUseCase($genreRepository);

        $genreDto = new GenreInputDto($genre->id);
        $response = $useCase->execute($genreDto);

        $this->assertTrue($response->success);
        $this->assertSoftDeleted('genres', [
            'id' => $genre->id
        ]);
    }
}
