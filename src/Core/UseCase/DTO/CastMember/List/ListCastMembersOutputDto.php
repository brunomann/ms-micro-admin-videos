<?php

namespace Core\UseCase\DTO\CastMember\List;

class ListCastMembersOutputDto
{
    public function __construct(
        public array $items = [],
        public int $total,
        public int $firstPage,
        public int $lastPage,
        public int $currentPage,
        public int $itemPerPage,
        public int $to,
        public int $from
    ){}
}