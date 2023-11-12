<?php

namespace Tests\Unit\UseCase\CastMember;

use Core\Domain\Entity\CastMember as CastMemberEntity;
use Core\Domain\Enum\CastMemberType;
use Core\Domain\Repository\CastMemberRepositoryInterface;
use PHPUnit\Framework\TestCase;
use Core\UseCase\CastMember\CreateCastMemberUseCase;
use Core\UseCase\DTO\CastMember\Create\CastMemberCreateInputDto;
use Core\UseCase\DTO\CastMember\Create\CastMemberCreateOutputDto;
use Mockery;
use Ramsey\Uuid\Uuid;
use stdClass;

class CreateCastMemberUseCaseUnitTest extends TestCase
{
    /**
     * A basic unit test example.
     *
     * @return void
     */
    public function testCreate()
    {
        $uuid = (string) Uuid::uuid4();

        $mockEntity = Mockery::mock(CastMemberEntity::class,['name', CastMemberType::ACTOR]);
        $mockEntity->shouldReceive('id')->andReturn($uuid);
        $mockEntity->shouldReceive('createdAt')->andReturn(date('Y-m-d H:i:s'));
        
        // Arrange
        $mockRepository = Mockery::mock(stdClass::class, CastMemberRepositoryInterface::class);
        $mockRepository->shouldReceive('insert')
                        ->once()
                        ->andReturn($mockEntity);
        $useCase = new CreateCastMemberUseCase($mockRepository);
        
        $mockDto = Mockery::mock(CastMemberCreateInputDto::class, [
            'name', 1
        ]);

        // Action
        $response = $useCase->execute($mockDto);

        // Assert
        $this->assertInstanceOf(CastMemberCreateOutputDto::class, $response);
        $this->assertEquals($uuid, $response->id);
        $this->assertEquals('name', $response->name);
        $this->assertEquals(1, $response->type);
        $this->assertNotEmpty($response->created_at);

        Mockery::close();
    }
}
