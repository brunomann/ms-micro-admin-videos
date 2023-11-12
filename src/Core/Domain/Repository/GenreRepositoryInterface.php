<?php

namespace Core\Domain\Repository;

use Core\Domain\Entity\Category;
use Core\Domain\Entity\Genre;

interface GenreRepositoryInterface
{
    public function insert(Genre $genre):Genre;
    public function findAll(string $filter = '', $order = 'DESC'):array;
    public function findById(string $id):Genre;
    public function paginate(string $filter = '', $order = 'DESC', int $page = 1, int $totalPages = 15):PaginationInterface;
    public function update(Genre $genre):Genre;
    public function delete(string $id):bool;
    // public function toCategory(object $data):Category;
}