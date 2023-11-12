<?php

namespace Core\UseCase\CastMember;

use Core\Domain\Repository\CastMemberRepositoryInterface;
use Core\UseCase\DTO\CastMember\CastMemberInputDto;
use Core\UseCase\DTO\CastMember\CastMemberOutputDto;

class ListCastMemberUseCase
{
    protected CastMemberRepositoryInterface $repository;

    public function __construct(CastMemberRepositoryInterface $repository)
    {
        $this->repository = $repository;
    }

    public function execute(CastMemberInputDto $input):CastMemberOutputDto
    {
        $castMember = $this->repository->findById($input->id);

        return new CastMemberOutputDto($castMember->id(), $castMember->name, $castMember->type->value, $castMember->createdAt());
    }
}