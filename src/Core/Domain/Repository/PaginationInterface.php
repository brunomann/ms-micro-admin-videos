<?php

namespace Core\Domain\Repository;

use Core\Domain\Entity\Category;
use stdClass;

interface PaginationInterface
{
    /**
     * @return stdClass[]
     */
    public function items():array;
    
    public function total():int;

    public function firstPage():int;
    
    public function lastPage():int;

    public function currentPage():int;

    public function itemPerPage():int;

    public function to():int;

    public function from():int;
     
}