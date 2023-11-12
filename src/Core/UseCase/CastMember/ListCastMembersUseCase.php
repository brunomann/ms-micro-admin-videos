<?php 

namespace Core\UseCase\CastMember;

use Core\Domain\Repository\CastMemberRepositoryInterface;
use Core\UseCase\DTO\CastMember\List\{ListCastMembersInputDto, ListCastMembersOutputDto};

class ListCastMembersUseCase
{
    protected $repository;

    public function __construct(CastMemberRepositoryInterface $repository)
    {
        $this->repository = $repository;
    }

    public function execute(ListCastMembersInputDto $input):ListCastMembersOutputDto
    {
        $returnRepository = $this->repository->paginate($input->filter, $input->order, $input->page, $input->totalPages);

        return new ListCastMembersOutputDto(
            $returnRepository->items(),
            $returnRepository->total(),
            $returnRepository->firstPage(),
            $returnRepository->lastPage(),
            $returnRepository->currentPage(),
            $returnRepository->itemPerPage(),
            $returnRepository->to(),
            $returnRepository->from(),
        );
    }
}