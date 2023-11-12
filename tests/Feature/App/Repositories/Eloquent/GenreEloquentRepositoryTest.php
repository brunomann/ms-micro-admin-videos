<?php

namespace Tests\Feature\App\Repositories\Eloquent;

use App\Models\Category;
use Core\Domain\Exception\NotFoundException;
use App\Models\Genre as GenreModel;
use App\Repositories\Eloquent\GenreEloquentRepository;
use Core\Domain\Entity\Genre as GenreEntity;
use Core\Domain\Repository\GenreRepositoryInterface;
use Core\Domain\ValueObject\Uuid;
use Ramsey\Uuid\Uuid as RamseyUuid;
use Tests\TestCase;
use Throwable;

class GenreEloquentRepositoryTest extends TestCase
{
    protected $repository;

    protected function setUp():void
    {
        parent::setUp();
        $this->repository = new GenreEloquentRepository(new GenreModel());
    }

    public function testImplementsInterface()
    {
        $this->assertInstanceOf(GenreRepositoryInterface::class, $this->repository);
    }

    public function testInsert()
    {
        $genre = new GenreEntity('New Genre');

        $response = $this->repository->insert($genre);

        $this->assertDatabaseHas('genres', [
            'id'=> $response->id()
        ]);
    }

    public function testInsertDeactivate()
    {
        $genre = new GenreEntity('New Genre');
        $genre->deactivate();

        $response = $this->repository->insert($genre);

        $this->assertDatabaseHas('genres', [
            'id'=> $response->id(),
            'is_active' => false
        ]);
    }

    public function testInsertWithRelationships()
    {
        $categories = Category::factory()->count(4)->create();
        
        $genre = new GenreEntity('New Genre');

        foreach($categories as $category){
            $genre->addCategory($category->id);
        }

        $response = $this->repository->insert($genre);

        $this->assertDatabaseHas('genres', [
            'id'=> $response->id()
        ]);

        $this->assertDatabaseCount('category_genre', 4);
    }

    public function testNotFoundById()
    {
        $this->expectException(NotFoundException::class);

        $genre = 'not_found';

        $this->repository->findById($genre);
    }

    public function testFindById()
    {

        $genre = GenreModel::factory()->create();

        $response = $this->repository->findById($genre->id);


        $this->assertEquals($genre->id, $response->id());
        $this->assertDatabaseHas('genres', [
            'id'=> $response->id()
        ]);
    }

    public function testFildAll()
    {
        $genres = GenreModel::factory()->count(4)->create();

        $genresDb = $this->repository->findAll();

        $this->assertCount(4, $genresDb);
    }

    public function testFildAllEmpty()
    {

        $genresDb = $this->repository->findAll();

        $this->assertCount(0, $genresDb);
    }

    public function testFildAllWithFilter()
    {
        GenreModel::factory()->count(10)->create([
            'name' => 'Bruno'
        ]);
        GenreModel::factory()->count(10)->create();

        $genresDb = $this->repository->findAll('Bruno');
        
        $this->assertCount(10, $genresDb);
        
        $genresDb = $this->repository->findAll();
        $this->assertCount(20, $genresDb);
    }

    public function testPagination()
    {
        GenreModel::factory()->count(30)->create();

        $genres = $this->repository->paginate();

        $this->assertEquals(15, count($genres->items()));
        $this->assertEquals(30, $genres->total());
    }

    public function testPaginationEmpty()
    {
        $genres = $this->repository->paginate();

        $this->assertEquals(0, count($genres->items()));
        $this->assertEquals(0, $genres->total());
    }

    public function testUpdate()
    {
        $genre = GenreModel::factory()->create();
        $entity = new GenreEntity($genre->name, new Uuid($genre->id), $genre->is_active, [], new \Datetime($genre->created_at));
        $entity->update('Bruno');

        $genreUpdated = $this->repository->update($entity);

        $this->assertEquals($genre->id, $genreUpdated->id);
        $this->assertEquals('Bruno', $genreUpdated->name);
        $this->assertDatabaseHas('genres', [
            'name' => 'Bruno'
        ]);
    }

    public function testUpdateNotFound()
    {
        $this->expectException(NotFoundException::class);

        $genreId = (string) RamseyUuid::uuid4();
        $entity = new GenreEntity('Bruno', new Uuid($genreId), true, [], new \Datetime(date('Y-m-d H:i:s')));
 
        $this->repository->update($entity);
    }

    public function testDeleteNotFoundException()
    {
        $this->expectException(NotFoundException::class);

        $this->repository->delete('fake_id');
    }

    public function testDelete()
    {
        $genre = GenreModel::factory()->create();

       $response =  $this->repository->delete($genre->id);

        $this->assertSoftDeleted('genres', [
            'id' => $genre->id
        ]);
        $this->assertTrue($response);
    }
}
