<?php

namespace Tests\Unit\Domain\Entity;

use Core\Domain\Entity\Genre;
use Core\Domain\Exception\EntityValidationException;
use Core\Domain\ValueObject\Uuid;
use DateTime;
use PHPUnit\Framework\TestCase;
use Ramsey\Uuid\Uuid as RamseyUuid;

class GenreUnitTest extends TestCase
{
    public function testAttributesInUpdate()
    {
        $uuid = (string) RamseyUuid::uuid4();
        // $date = new DateTime(date('Y-m-d H:i:s'));
        $date = date('Y-m-d H:i:s');

        $genre = new Genre(
            name: 'New name',
            id: new Uuid($uuid),
            is_active: true,
            created_at: new Datetime($date)
        );

        $this->assertEquals($uuid, $genre->id());
        $this->assertEquals('New name', $genre->name);
        $this->assertEquals(true, $genre->is_active);
        $this->assertEquals($date, $genre->createdAt());
    }

    public function testAttributesInCreate()
    {
        $date = new DateTime(date('Y-m-d H:i:s'));

        $genre = new Genre(
            name: 'New name',
            is_active: false
        );

        $this->assertNotEmpty($genre->id());
        $this->assertEquals('New name', $genre->name);
        $this->assertEquals(false, $genre->is_active);
        $this->assertNotEmpty($date, $genre->createdAt());
    }

    public function testDeactivate()
    {
        $genre = new Genre(
            name: 'New name',
            is_active: true
        );

        $this->assertEquals(true, $genre->is_active);

        $genre->deactivate();

        $this->assertEquals(false, $genre->is_active);
    }

    public function testActivate()
    {
        $genre = new Genre(
            name: 'New name',
            is_active: false
        );

        $this->assertEquals(false, $genre->is_active);

        $genre->activate();

        $this->assertEquals(true, $genre->is_active);
    }
    
    public function testUpdate()
    {
        $genre = new Genre(
            name: 'Old name',
            is_active: true
        );

        $this->assertEquals('Old name', $genre->name);

        $genre->update(
            name: 'New name'
        );

        $this->assertEquals('New name', $genre->name);
    }

    public function testCreateGenreException()
    {
        $this->expectException(EntityValidationException::class);

        $genre = new Genre(
            name: 's'
        );
    }

    public function testUpdateGenreException()
    {
        $this->expectException(EntityValidationException::class);

        $uuid = (string) RamseyUuid::uuid4();
        $date = date('Y-m-d H:i:s');

        $genre = new Genre(
            name: 'New name',
            id: new Uuid($uuid),
            is_active: true,
            created_at: new Datetime($date)
        );

        $genre->update('s');
    }

    public function testAddCategoryToGenre()
    {
        $categoryUuid = (string) RamseyUuid::uuid4();
        $genreUuid = (string) RamseyUuid::uuid4();
        $date = date('Y-m-d H:i:s');

        $genre = new Genre(
            name: 'New name',
            id: new Uuid($genreUuid),
            is_active: true,
            created_at: new Datetime($date)
        );

        $this->assertCount(0, $genre->categories_id);
        $genre->addCategory(categoryId: $categoryUuid);
        $this->assertCount(1, $genre->categories_id);
    }

    public function testRemoveCategoryToGenre()
    {
        $categoryUuid = (string) RamseyUuid::uuid4();
        $category2Uuid = (string) RamseyUuid::uuid4();
        $genreUuid = (string) RamseyUuid::uuid4();
        $date = date('Y-m-d H:i:s');

        $genre = new Genre(
            name: 'New name',
            id: new Uuid($genreUuid),
            is_active: true,
            categories_id: [$categoryUuid, $category2Uuid],
            created_at: new Datetime($date)
        );

        $this->assertCount(2, $genre->categories_id);
        $genre->removeCategory(categoryId: $category2Uuid);
        $this->assertCount(1, $genre->categories_id);
        $this->assertEquals($genre->categories_id[0], $categoryUuid);

    }
}
