<?php

namespace Tests\Feature\Core\UseCase\Genre;

use App\Models\Category as CategoryModel;
use App\Models\Genre as GenreModel;
use App\Repositories\Eloquent\CategoryEloquentRepository;
use App\Repositories\Eloquent\GenreEloquentRepository;
use App\Repositories\Transaction\DBTransaction;
use Core\Domain\Exception\NotFoundException;
use Core\UseCase\DTO\Genre\Create\GenreCreateInputDto;
use Core\UseCase\DTO\Genre\Update\GenreUpdateInputDto;
use Core\UseCase\Genre\CreateGenreUseCase;
use Core\UseCase\Genre\UpdateGenreUseCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use Throwable;

class UpdateGenreUseCaseTest extends TestCase
{
    public function testUpdateGenre()
    {
        $genreRepository = new GenreEloquentRepository(new GenreModel());
        $categoryRepository = new CategoryEloquentRepository(new CategoryModel());
        $transaction = new DBTransaction();
        $useCase = new UpdateGenreUseCase($genreRepository, $transaction, $categoryRepository);

        $categories = CategoryModel::factory()->count(4)->create();
        $categoriesId = $categories->pluck('id')->toArray();

        $genre = GenreModel::factory()->create();

        $genreDto = new GenreUpdateInputDto($genre->id, 'Teste', $categoriesId);
        $response = $useCase->execute($genreDto);

        $this->assertEquals('Teste', $response->name);
        $this->assertDatabaseHas('genres', [
            'name' => $response->name
        ]);
        $this->assertDatabaseCount('category_genre', 4);
    }

    public function testExceptionUpdateGenreWithCategoriesIdInvalid()
    {
        $this->expectException(NotFoundException::class);

        $genreRepository = new GenreEloquentRepository(new GenreModel());
        $categoryRepository = new CategoryEloquentRepository(new CategoryModel());
        $transaction = new DBTransaction();
        $useCase = new UpdateGenreUseCase($genreRepository, $transaction, $categoryRepository);

        $categories = CategoryModel::factory()->count(4)->create();
        $categoriesId = $categories->pluck('id')->toArray();
        array_push($categoriesId , '1');

        $genre = GenreModel::factory()->create();

        $genreDto = new GenreUpdateInputDto($genre->id, 'Teste', $categoriesId);
        $response = $useCase->execute($genreDto);
    }

    public function testTransactionWithRollback()
    {
        $genreRepository = new GenreEloquentRepository(new GenreModel());
        $categoryRepository = new CategoryEloquentRepository(new CategoryModel());
        $transaction = new DBTransaction();
        $useCase = new UpdateGenreUseCase($genreRepository, $transaction, $categoryRepository);

        $categories = CategoryModel::factory()->count(4)->create();
        $categoriesId = $categories->pluck('id')->toArray();

        $genre = GenreModel::factory()->create();

        try{

            $genreDto = new GenreUpdateInputDto($genre->id, 'Teste', $categoriesId);
            $response = $useCase->execute($genreDto);

            $this->assertDatabaseCount('category_genre', 4);

        }catch(\Throwable $th){
            $this->assertDatabaseCount('genres', 0);
            $this->assertDatabaseCount('category_genre', 0);
        }
    }
}
