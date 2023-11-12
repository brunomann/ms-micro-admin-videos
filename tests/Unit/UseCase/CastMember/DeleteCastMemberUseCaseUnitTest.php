<?php

use Core\Domain\Repository\CastMemberRepositoryInterface;
use Core\UseCase\CastMember\DeleteCastMemberUseCase;
use Core\UseCase\DTO\CastMember\CastMemberInputDto;
use Core\UseCase\DTO\CastMember\Delete\CastMemberDeleteOutputDto;
use PHPUnit\Framework\TestCase;
use Ramsey\Uuid\Uuid;
use Core\UseCase\DTO\Genre\GenreInputDto;

class DeleteCastMemberUseCaseUnitTest extends TestCase
{
    public function testDelete()
    {
        $mockRepository = Mockery::mock(stdClass::class, CastMemberRepositoryInterface::class);
        $mockRepository->shouldReceive('delete')
                        ->once()
                        ->andReturn(true);

        $useCase = new DeleteCastMemberUseCase($mockRepository);
        
        $uuid = (string) Uuid::uuid4();
        $inputDto = Mockery::mock( CastMemberInputDto::class, [$uuid]);
        $responseUseCase = $useCase->execute($inputDto);

        $this->assertInstanceOf(CastMemberDeleteOutputDto::class, $responseUseCase);
        $this->assertTrue($responseUseCase->success);

        Mockery::close();
    }
}