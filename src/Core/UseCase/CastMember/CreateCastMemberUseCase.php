<?php

namespace Core\UseCase\CastMember;

use Core\Domain\Entity\CastMember;
use Core\Domain\Enum\CastMemberType;
use Core\Domain\Repository\CastMemberRepositoryInterface;
use Core\UseCase\DTO\CastMember\Create\CastMemberCreateInputDto;
use Core\UseCase\DTO\CastMember\Create\CastMemberCreateOutputDto;

class CreateCastMemberUseCase
{
    protected $repository;

    public function __construct(CastMemberRepositoryInterface $repository)
    {
        $this->repository = $repository;    
    }

    public function execute(CastMemberCreateInputDto $input): CastMemberCreateOutputDto
    {
        $castMemberEntity = new CastMember($input->name, $input->type == 1 ? CastMemberType::DIRECTOR : CastMemberType::ACTOR);

        $castMemberCreated = $this->repository->insert($castMemberEntity);

        return new CastMemberCreateOutputDto($castMemberCreated->id(), $castMemberCreated->name, $input->type, $castMemberCreated->createdAt());
    }
}