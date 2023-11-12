<?php

namespace App\Repositories\Eloquent;

use App\Models\Genre as GenreModel;
use App\Repositories\Presenters\PaginationPresenter;
use Core\Domain\Entity\Genre as GenreEntity;
use Core\Domain\Exception\NotFoundException;
use Core\Domain\Repository\GenreRepositoryInterface;
use Core\Domain\Repository\PaginationInterface;
use Core\Domain\ValueObject\Uuid;
use DateTime;
use Exception;

class GenreEloquentRepository implements GenreRepositoryInterface
{
    protected $model;

    public function __construct(GenreModel $model)
    {
        $this->model = $model;
    }

    public function insert(GenreEntity $genre):GenreEntity
    {
        $genreCreated = $this->model->create([
            'id' => $genre->id(),
            'name' => $genre->name,
            'is_active' => $genre->is_active,
            'created_at' => $genre->createdAt(),
        ]);

        if(count($genre->categories_id)){
            // throw new \Exception('Debug');
            $genreCreated->categories()->sync($genre->categories_id);
        }

        return $this->toGenre($genreCreated);
    }
    
    public function findAll(string $filter = '', $order = 'DESC'):array
    {
        $result = $this->model
            ->where(function($query) use ($filter){
                if($filter !== ''){
                    $query->where('name', 'LIKE', "%{$filter}%");
                }
            })
            ->orderBy('name', $order)
            ->get();

        return $result->toArray();
    }
    
    public function findById(string $id):GenreEntity
    {
        if(! $genreDb = $this->model->find($id)){
            throw new NotFoundException("Genre {$id} not found");
        }
        return $this->toGenre($genreDb);
    }
    
    public function paginate(string $filter = '', $order = 'DESC', int $page = 1, int $totalPages = 15):PaginationInterface
    {
        $result = $this->model
            ->where(function($query) use ($filter){
                if($filter !== ''){
                    $query->where('name', 'LIKE', "%{$filter}%");
                }
            })
            ->orderBy('name', $order)
            ->paginate($totalPages);

        return new PaginationPresenter($result);
    }
    
    public function update(GenreEntity $genre):GenreEntity
    {
        if(! $genreDb = $this->model->find($genre->id)){
            throw new NotFoundException("Genre {$genre->id} not found");
        }
        $genreDb->update([
            'name' => $genre->name
        ]);

        if(count($genre->categories_id)){
            $genreDb->categories()->sync($genre->categories_id);
        }

        $genreDb->refresh();

        return $this->toGenre($genreDb);
    }
    
    public function delete(string $id):bool
    {
        if(! $genreDb = $this->model->find($id)){
            throw new NotFoundException("Genre {$id} not found");
        }

        $genreDb->delete();

        return true;
    }

    private function toGenre(object $object):GenreEntity
    {
        $entity = new GenreEntity(
            id: new Uuid($object->id),
            name: $object->name,
            created_at: new DateTime($object->created_at)
        );

        ((bool) $object->is_active) ? $entity->activate() : $entity->deactivate();

        return $entity;
    }
}