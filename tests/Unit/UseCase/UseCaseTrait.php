<?php

namespace Tests\Unit\UseCase;

use Core\Domain\Repository\PaginationInterface;
use Mockery;

trait UseCaseTrait
{
    protected function createMockPaginationEntity(array $itemsToMock = []):PaginationInterface
    {
        $mockPaginationEntity = Mockery::mock(stdClass::class, PaginationInterface::class);
        $mockPaginationEntity->shouldReceive('items')->andReturn($itemsToMock);
        $mockPaginationEntity->shouldReceive('total')->andReturn(0);
        $mockPaginationEntity->shouldReceive('firstPage')->andReturn(0);
        $mockPaginationEntity->shouldReceive('lastPage')->andReturn(0);
        $mockPaginationEntity->shouldReceive('currentPage')->andReturn(0);
        $mockPaginationEntity->shouldReceive('itemPerPage')->andReturn(0);
        $mockPaginationEntity->shouldReceive('to')->andReturn(0);
        $mockPaginationEntity->shouldReceive('from')->andReturn(0);

        return $mockPaginationEntity;
    }
}