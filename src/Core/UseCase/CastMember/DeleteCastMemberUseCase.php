<?php

namespace Core\UseCase\CastMember;

use Core\Domain\Repository\CastMemberRepositoryInterface;
use Core\UseCase\DTO\CastMember\Delete\CastMemberDeleteOutputDto;
use Core\UseCase\DTO\CastMember\CastMemberInputDto;


class DeleteCastMemberUseCase
{
    protected $repository;

    public function __construct(CastMemberRepositoryInterface $repository)
    {
        $this->repository = $repository;    
    }

    public function execute(CastMemberInputDto $input):CastMemberDeleteOutputDto
    {
        $castMemberDeleted = $this->repository->delete($input->id);

        return new CastMemberDeleteOutputDto($castMemberDeleted);
    }
}