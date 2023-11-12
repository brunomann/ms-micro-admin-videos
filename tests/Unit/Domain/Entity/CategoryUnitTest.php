<?php

namespace Tests\Unit\Domain\Entity;

use Core\Domain\Entity\Category;
use Core\Domain\Exception\EntityValidationException;
use Exception;
use PHPUnit\Framework\TestCase;
use Ramsey\Uuid\Rfc4122\UuidV4;
use Ramsey\Uuid\Uuid;
use Throwable;

class CategoryUnitTest extends TestCase
{
    public function testAttributes()
    {
        $category = new Category(
            name: 'New Category',
            description: 'New desc',
            is_active: true
        );

        $this->assertNotEmpty($category->createdAt());
        $this->assertNotEmpty($category->id());
        $this->assertEquals('New Category', $category->name);
        $this->assertEquals('New desc', $category->description);
        $this->assertEquals(true, $category->is_active);
    }

    public function testActivated()
    {
        $category = new Category(
            name: 'New Category',
            is_active: false
        );
        $this->assertFalse($category->is_active);

        $category->activate();
        $this->assertTrue($category->is_active);
    }

    public function testDisabled()
    {
        $category = new Category(
            name: 'New Category',
            is_active: true
        );
        $this->assertTrue($category->is_active);

        $category->disable();
        $this->assertFalse($category->is_active);
    }

    public function testUpdate()
    {
        $uuid = (string) Uuid::uuid4()->toString();
        $category = new Category(
            id: $uuid,
            name: 'New Category',
            description: 'New desc',
            is_active: true,
            created_at: '2022-06-14 15:00:00'  
        );

        $category->update(
            name: 'New Category Name',
            description: 'New desc changed',
        );

        $this->assertEquals('New Category Name', $category->name);
        $this->assertEquals('New desc changed', $category->description);
        $this->assertEquals($uuid, $category->id());
        $this->assertEquals($category->createdAt(), $category->createdAt());
    }

    public function testExceptionName()
    {
        try{
            $category = new Category(
                name: 'N',
                description: 'N',
            );
    
            $this->assertTrue(false);
        }catch(Throwable $th){
            $this->assertInstanceOf(EntityValidationException::class, $th);
        }
    }

    public function testExceptionDescription()
    {
        try{
            $category = new Category(
                name: 'N',
                description: random_bytes(9999),
            );
    
            $this->assertTrue(false);
        }catch(Throwable $th){
            $this->assertInstanceOf(EntityValidationException::class, $th);
        }
    }
}