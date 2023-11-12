<?php

namespace Tests\Unit\UseCase\CastMember;

use Core\Domain\Entity\CastMember as CastMemberEntity;
use Core\Domain\Enum\CastMemberType;
use Core\Domain\Repository\CastMemberRepositoryInterface;
use Core\Domain\ValueObject\Uuid as ValueObjectUuid;
use Core\UseCase\CastMember\ListCastMemberUseCase;
use Core\UseCase\DTO\CastMember\{CastMemberInputDto, CastMemberOutputDto};
use Mockery;
use PHPUnit\Framework\TestCase;
use Ramsey\Uuid\Uuid;

class ListCatMemberUseCaseUnitTest extends TestCase
{

    public function testList()
    {
        $uuid = (string) Uuid::uuid4();

        $mockEntity = Mockery::mock(CastMemberEntity::class,['name', CastMemberType::ACTOR, new ValueObjectUuid($uuid)]);
        $mockEntity->shouldReceive('id')->andReturn($uuid);
        $mockEntity->shouldReceive('createdAt')->andReturn(date('Y-m-d H:i:s'));
        
        $mockRepository = Mockery::mock(stdClass::class, CastMemberRepositoryInterface::class);
        $mockRepository->shouldReceive('findById')
                        ->times(1)
                        ->with($uuid)
                        ->andReturn($mockEntity);
        
        $mockInputDto = Mockery::mock(CastMemberInputDto::class, [$uuid]);

        $useCase = new ListCastMemberUseCase($mockRepository);
        $castMemberOutputDto = $useCase->execute($mockInputDto);

        $this->assertInstanceOf(CastMemberOutputDto::class, $castMemberOutputDto);

        Mockery::close();
    }
}
