<?php

namespace Tests\Feature\Core\UseCase\Genre;

use App\Models\Category as CategoryModel;
use App\Models\Genre as GenreModel;
use App\Repositories\Eloquent\CategoryEloquentRepository;
use App\Repositories\Eloquent\GenreEloquentRepository;
use App\Repositories\Transaction\DBTransaction;
use Core\Domain\Exception\NotFoundException;
use Core\UseCase\DTO\Genre\Create\GenreCreateInputDto;
use Core\UseCase\Genre\CreateGenreUseCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use Throwable;

class CreateGenreUseCaseTest extends TestCase
{
    public function testInsertGenre()
    {
        $genreRepository = new GenreEloquentRepository(new GenreModel());
        $categoryRepository = new CategoryEloquentRepository(new CategoryModel());
        $transaction = new DBTransaction();
        $useCase = new CreateGenreUseCase($genreRepository, $transaction, $categoryRepository);

        $genreDto = new GenreCreateInputDto('Genre');
        $response = $useCase->execute($genreDto);

        $this->assertDatabaseHas('genres', [
            'name' => $response->name
        ]);

    }

    public function testInsertGenreWithCategories()
    {
        $genreRepository = new GenreEloquentRepository(new GenreModel());
        $categoryRepository = new CategoryEloquentRepository(new CategoryModel());
        $transaction = new DBTransaction();
        $useCase = new CreateGenreUseCase($genreRepository, $transaction, $categoryRepository);

        $categories = CategoryModel::factory()->count(4)->create();
        $categoriesId = $categories->pluck('id')->toArray();

        $genreDto = new GenreCreateInputDto('Genre', true, $categoriesId);
        $response = $useCase->execute($genreDto);

        $this->assertDatabaseHas('genres', [
            'name' => $response->name
        ]);

        $this->assertDatabaseCount('category_genre', 4);

    }

    public function testExceptionInsertGenreWithCategoriesIdInvalid()
    {
        $this->expectException(NotFoundException::class);

        $genreRepository = new GenreEloquentRepository(new GenreModel());
        $categoryRepository = new CategoryEloquentRepository(new CategoryModel());
        $transaction = new DBTransaction();
        $useCase = new CreateGenreUseCase($genreRepository, $transaction, $categoryRepository);

        $categories = CategoryModel::factory()->count(4)->create();
        $categoriesId = $categories->pluck('id')->toArray();
        array_push($categoriesId, '1', '2', '3', '4');

        $genreDto = new GenreCreateInputDto('Genre', true, $categoriesId);
        $response = $useCase->execute($genreDto);
    }

    public function testTransactionWithRollback()
    {
        $genreRepository = new GenreEloquentRepository(new GenreModel());
        $categoryRepository = new CategoryEloquentRepository(new CategoryModel());
        $transaction = new DBTransaction();
        $useCase = new CreateGenreUseCase($genreRepository, $transaction, $categoryRepository);

        $categories = CategoryModel::factory()->count(4)->create();
        $categoriesId = $categories->pluck('id')->toArray();
        
        try{
            $genreDto = new GenreCreateInputDto('Genre', true, $categoriesId);
            $response = $useCase->execute($genreDto);

            $this->assertDatabaseCount('category_genre', 4);

        }catch(\Throwable $th){
            $this->assertDatabaseCount('genres', 0);
            $this->assertDatabaseCount('category_genre', 0);
        }
    }
}
