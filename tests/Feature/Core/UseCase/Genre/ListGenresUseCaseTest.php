<?php

namespace Tests\Feature\Core\UseCase\Genre;

use App\Models\Genre as GenreModel;
use App\Repositories\Eloquent\GenreEloquentRepository;
use Core\UseCase\DTO\Genre\List\ListGenresInputDto;
use Core\UseCase\Genre\ListGenresUseCase;
use Core\UseCase\Genre\ListGenreUseCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class ListGenresUseCaseTest extends TestCase
{
    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function testFindAll()
    {
        $genre = GenreModel::factory()->count(50)->create();
        $genreRepository = new GenreEloquentRepository(new GenreModel());
        $useCase = new ListGenresUseCase($genreRepository);

        $genresDto = new ListGenresInputDto();
        $response = $useCase->execute($genresDto);

        $this->assertEquals(15, count($response->items));
        $this->assertEquals(50, $response->total);
    }
}
