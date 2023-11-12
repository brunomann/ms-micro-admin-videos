<?php

namespace Tests\Unit\App\Models;

use App\Models\CastMember;
use Illuminate\Database\Eloquent\Model;
use PHPUnit\Framework\TestCase;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;

class CastMemberUnitTest extends TestCase
{

    protected function model():Model
    {
        return new CastMember();
    }

    public function testIfUseTraits()
    {
        $traitsNeed = [
            HasFactory::class,
            SoftDeletes::class,
        ];
        
        $traitsUsed = array_keys(class_uses($this->model()));

        $this->assertEquals($traitsNeed, $traitsUsed);
    }

    public function testIncrementingIsFalse()
    {
       $model = $this->model();
       $this->assertFalse($model->incrementing);
    }

    public function testFillables()
    {
        $fillableNeed = [
            'id',
            'name',
            'type',
            'created_at',
        ];
        $fillable = $this->model()->getFillable();

        $this->assertEquals($fillableNeed, $fillable);
    }

    public function testHasCasts()
    {
        $castsNeed = [
            'id' => 'string',
            'created_at' => 'datetime',
            'deleted_at' => 'datetime'
        ];

        $castsUsed = $this->model()->getCasts();

        $this->assertEquals($castsNeed, $castsUsed);
    }
}
