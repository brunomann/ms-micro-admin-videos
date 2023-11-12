<?php

namespace App\Repositories\Eloquent;

use App\Models\Category as Model;
use App\Repositories\Presenters\PaginationPresenter;
use Core\Domain\Entity\Category as CategoryEntity;
use Core\Domain\Repository\CategoryRepositoryInterface;
use Core\Domain\Repository\PaginationInterface;
use Core\Domain\Exception\NotFoundException;

class CategoryEloquentRepository implements CategoryRepositoryInterface
{
    private $model;

    public function __construct(Model $model)
    {
        $this->model = $model;
    }

    public function insert(CategoryEntity $category):CategoryEntity
    {
        $category = $this->model->create([
            'id' => $category->id(),
            'name' => $category->name,
            'description' => $category->description,
            'is_active' => $category->is_active,
            'created_at' => $category->created_at,
        ]);

        return $this->toCategory($category);
    }

    public function findAll(string $filter = '', $order = 'DESC'):array
    {
        $categories = $this->model
                            ->where(function ($query) use ($filter) {
                                if($filter){
                                    $query->where('name', 'LIKE', "%{$filter}%");
                                }
                            })
                            ->orderBy('id', $order)
                            ->get();

        return $categories->toArray();
    }

    public function findById(string $id):CategoryEntity
    {
        if(!$categoryDb = $this->model->find($id)){
            throw new NotFoundException("Category {$id} not found");
        }

        return $this->toCategory($categoryDb);
    }

    public function paginate(string $filter = '', $order = 'DESC', int $page = 1, int $totalPages = 15):PaginationInterface
    {
        $query = $this->model;
        if($filter){
            $query = $query->where('name', 'LIKE', "%{$filter}%");
        }
        $query = $query->orderBy('id', $order);
        $paginator = $query->paginate();

        return new PaginationPresenter($paginator);
    }

    public function update(CategoryEntity $category):CategoryEntity
    {
        if(!$categoryDb = $this->model->find($category->id)){
            throw new NotFoundException();
        }

        $categoryDb->update([
            'name'          => $category->name,
            'description'   => $category->description,
            'is_active'     => $category->is_active,
        ]);

        $categoryDb->refresh();

        return $this->toCategory($categoryDb);
    }

    public function delete(string $id):bool
    {
        if(!$categoryDb = $this->model->find($id)){
            throw new NotFoundException();
        }

        return $categoryDb->delete();
    }

    private function toCategory(object $object):CategoryEntity
    {
        $entity = new CategoryEntity(
            id: $object->id,
            name: $object->name,
            description: $object->description,
        );

        ((bool) $object->is_active) ? $entity->activate() : $entity->disable();

        return $entity;
    }

    public function getIdsByListIds(array $categoriesId = []):array
    {
        return $this->model
                    ->whereIn('id', $categoriesId)
                    ->pluck('id')
                    ->toArray();
    }
}