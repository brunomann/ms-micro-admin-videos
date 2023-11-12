<?php

namespace Core\UseCase\DTO\CastMember\List;

class ListCastMembersInputDto
{
    public string $filter;
    public $order;
    public int $page;
    public int $totalPages;

    public function __construct(string $filter = '', $order = 'DESC', int $page = 1, int $totalPages = 15)
    {
        $this->filter = $filter;
        $this->order = $order;
        $this->page = $page;
        $this->totalPages = $totalPages;
    }
}