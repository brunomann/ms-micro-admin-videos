<?php

namespace App\Repositories\Eloquent;

use App\Models\CastMember as CastMemberModel;
use App\Repositories\Presenters\PaginationPresenter;
use Core\Domain\Entity\CastMember as CastMemberEntity;
use Core\Domain\Enum\CastMemberType;
use Core\Domain\Exception\NotFoundException;
use Core\Domain\Repository\CastMemberRepositoryInterface;
use Core\Domain\Repository\PaginationInterface;
use Core\Domain\ValueObject\Uuid;
use Illuminate\Database\Eloquent\Model;

class CastMemberEloquentRepository implements CastMemberRepositoryInterface
{
    protected $model;

    public function __construct(CastMemberModel $castMemberModel)
    {
        $this->model = $castMemberModel;
    }

    public function insert(CastMemberEntity $castMember):CastMemberEntity
    {
        $castMemberCreated = $this->model->create([
            'id' => $castMember->id(),
            'name' => $castMember->name,
            'type' => $castMember->type->value,
            'created_at' => $castMember->createdAt(),
        ]);

        return $this->convertToCastMember($castMemberCreated);
    }

    public function findAll(string $filter = '', $order = 'DESC'):array
    {
        $castMembersCollection = $this->model
                    ->where(function ($query) use ($filter){
                        if($filter){
                            $query->where('name', 'LIKE', "%{$filter}%");
                        }
                    })
                    ->orderBy('name', $order)
                    ->get();
        return $castMembersCollection->toArray();
    }

    public function findById(string $id):CastMemberEntity
    {
        if(! $castMemberModel = $this->model->find($id)){
            throw new NotFoundException(sprintf('CastMember %s not found', $id));
        }

        return $this->convertToCastMember($castMemberModel);
    }

    public function paginate(string $filter = '', $order = 'DESC', int $page = 1, int $totalPages = 15):PaginationInterface
    {
        $query = $this->model;
        if($filter){
            $query = $query->where('name', 'LIKE', "%{$filter}%");
        }

        $query = $query->orderBy('name', $order);
        $dbData = $query->paginate($totalPages);

        return new PaginationPresenter($dbData);
    }

    public function update(CastMemberEntity $castMember):CastMemberEntity
    {
        if(!$dataDb = $this->model->find($castMember->id())){
            throw new NotFoundException(sprintf('CastMember %s not found', $castMember->id()));
        }

        $dataDb->update([
            'name' => $castMember->name,
            'type' => $castMember->type->value,
        ]);

        $dataDb->refresh();

        return $this->convertToCastMember($dataDb);
    }
    
    public function delete(string $castMemberId):bool
    {
        if(!$dataDb = $this->model->find($castMemberId)){
            throw new NotFoundException(sprintf('CastMember %s not found', $castMemberId));
        }

        return $dataDb->delete();
    }

    private function convertToCastMember(Model $model):CastMemberEntity
    {
        return new CastMemberEntity(
            $model->name,
            CastMemberType::from($model->type),
            new Uuid( $model->id),
            $model->created_at,
        );
    }
}