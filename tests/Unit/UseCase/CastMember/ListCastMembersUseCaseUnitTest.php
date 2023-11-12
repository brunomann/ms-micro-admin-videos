<?php

namespace Tests\Unit\UseCase\CastMember;

use Core\Domain\Repository\CastMemberRepositoryInterface;
use Core\UseCase\CastMember\ListCastMembersUseCase;
use Core\UseCase\DTO\CastMember\List\{ListCastMembersInputDto, ListCastMembersOutputDto};
use Mockery;
use PHPUnit\Framework\TestCase;
use Tests\Unit\UseCase\UseCaseTrait;

class ListCastMembersUseCaseUnitTest extends TestCase
{
    use UseCaseTrait;

    public function testList()
    {
        $mockRepository = Mockery::mock(stdClass::class, CastMemberRepositoryInterface::class);
        $mockRepository->shouldReceive('paginate')
                        ->times(1)
                        ->andReturn($this->createMockPaginationEntity());

        $useCase = new ListCastMembersUseCase($mockRepository);

        $mockInputDto = Mockery::mock(ListCastMembersInputDto::class, [
            'filter', 'desc', 1, 15
        ]);

        $responseUseCase = $useCase->execute($mockInputDto);

        $this->assertInstanceOf(ListCastMembersOutputDto::class, $responseUseCase);

        Mockery::close();
    }
}
