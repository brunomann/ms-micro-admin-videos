<?php

use Core\Domain\Enum\CastMemberType;
use Core\Domain\Repository\CastMemberRepositoryInterface;
use PHPUnit\Framework\TestCase;
use Core\UseCase\CastMember\UpdateCastMemberUseCase;
use Core\Domain\ValueObject\Uuid as ValueObjectUuid;
use Core\Domain\Entity\CastMember as CastMemberEntity;
use Core\UseCase\DTO\CastMember\Update\{UpdateCastMemberInputDto, UpdateCastMemberOutputDto};
use Mockery\Mock;
use Ramsey\Uuid\Uuid;

class UpdateCastMemberUseCaseUnitTest extends TestCase
{
    public function testUpdate()
    {
        $uuid = (string) Uuid::uuid4();

        $mockEntity = Mockery::mock(CastMemberEntity::class, ['name', CastMemberType::ACTOR, new ValueObjectUuid($uuid)]);
        $mockEntity->shouldReceive('id')->andReturn($uuid);
        $mockEntity->shouldReceive('update');
        $mockEntity->shouldReceive('createdAt')->andReturn(date('Y-m-d H:i:s'));
        
        $mockRepository = Mockery::mock(stdClass::class, CastMemberRepositoryInterface::class);
        $mockRepository->shouldReceive('findById')
                        ->times(1)
                        ->with($uuid)
                        ->andReturn($mockEntity);
                        
        $mockEntityUpdated = Mockery::mock(CastMemberEntity::class, ['Name Updated', CastMemberType::ACTOR, new ValueObjectUuid($uuid)]);
        $mockEntityUpdated->shouldReceive('id')->andReturn($uuid);
        $mockEntityUpdated->shouldReceive('createdAt')->andReturn(date('Y-m-d H:i:s'));
        $mockRepository->shouldReceive('update')
                        ->times(1)
                        ->andReturn($mockEntityUpdated);
                        
        $useCase = new UpdateCastMemberUseCase($mockRepository);

        $inputDto = Mockery::mock(UpdateCastMemberInputDto::class, [$uuid, 'Name Updated']);

        $responseUseCase = $useCase->execute($inputDto);

        $this->assertInstanceOf(UpdateCastMemberOutputDto::class, $responseUseCase);
        $this->assertEquals($uuid, $responseUseCase->id);
        $this->assertEquals('Name Updated', $responseUseCase->name);
        $this->assertNotEmpty($responseUseCase->type);
        $this->assertNotEmpty($responseUseCase->created_at);

        Mockery::close();
    }
}