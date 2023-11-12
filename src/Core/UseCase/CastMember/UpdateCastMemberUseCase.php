<?php

namespace Core\UseCase\CastMember;

use Core\Domain\Entity\CastMember as CastMemberEntity;
use Core\Domain\Repository\CastMemberRepositoryInterface;
use Core\UseCase\DTO\CastMember\Update\{UpdateCastMemberInputDto, UpdateCastMemberOutputDto};

class UpdateCastMemberUseCase
{
    protected CastMemberRepositoryInterface $repository;

    public function __construct(CastMemberRepositoryInterface $repository)
    {
        $this->repository = $repository;
    }

    public function execute(UpdateCastMemberInputDto $input):UpdateCastMemberOutputDto
    {
        $castMember = $this->repository->findById($input->id);
        $castMember->update($input->name);
        
        $castMemberUpdated = $this->repository->update($castMember);

        return new UpdateCastMemberOutputDto(
            $castMemberUpdated->id(),
            $castMemberUpdated->name,
            $castMemberUpdated->type->value,
            $castMemberUpdated->createdAt()
        );
    }
}
