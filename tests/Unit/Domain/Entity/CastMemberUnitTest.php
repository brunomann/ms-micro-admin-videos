<?php

namespace Tests\Unit\Domain\Entity;

use PHPUnit\Framework\TestCase;
use Core\Domain\Entity\CastMember;
use Core\Domain\Enum\CastMemberType;
use Core\Domain\Exception\EntityValidationException;
use Ramsey\Uuid\Uuid as RamseyUuid;
use Core\Domain\ValueObject\Uuid;
use DateTime;

class CastMemberUnitTest extends TestCase
{
    public function testAttributes()
    {
        $uuid =  (string) RamseyUuid::uuid4();
    
        $castMember = new CastMember(
            id: new Uuid($uuid),
            name: 'Name',
            type: CastMemberType::ACTOR,
            created_at: new DateTime()
        );

        $this->assertEquals($uuid, $castMember->id());
        $this->assertEquals('Name', $castMember->name);
        $this->assertEquals(CastMemberType::ACTOR, $castMember->type);
        $this->assertNotEmpty($castMember->createdAt());
    }

    public function testAttributesNewEntity()
    {    
        $castMember = new CastMember(
            name: 'Name',
            type: CastMemberType::DIRECTOR
        );

        $this->assertNotEmpty($castMember->id());
        $this->assertEquals('Name', $castMember->name);
        $this->assertEquals(CastMemberType::DIRECTOR, $castMember->type);
        $this->assertNotEmpty($castMember->createdAt());
    }

    public function testException()
    {
        $this->expectException(EntityValidationException::class);

        $castMember = new CastMember(
            name: 'ab',
            type: CastMemberType::DIRECTOR
        );
    }

    public function testExceptionUpdate()
    {
        $this->expectException(EntityValidationException::class);

        $castMember = new CastMember(
            name: 'ab',
            type: CastMemberType::DIRECTOR
        );

        $castMember->update('Bruno');

        $this->assertEquals('Bruno', $castMember->name);
    }

    public function testUpdate()
    {
        $castMember = new CastMember(
            name: 'Bruno',
            type: CastMemberType::DIRECTOR
        );
        $this->assertEquals('Bruno', $castMember->name);

        $castMember->update('Bruno Mann');

        $this->assertEquals('Bruno Mann', $castMember->name);
    }
}