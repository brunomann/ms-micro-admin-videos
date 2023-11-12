<?php 

namespace Testes\Unit\Domain\Validation;
namespace Core\Domain\Validation\DomainValidation;

use Core\Domain\Exception\EntityValidationException;
use Core\Domain\Validation\DomainValidation;
use PHPUnit\Framework\TestCase;
use Throwable;

class DomainValidationUnitTest extends TestCase
{
    public function testNotNull()
    {
        try{
            $value = '';
            DomainValidation::notNull($value);

            $this->assertTrue(false);
        }catch(Throwable $th){
            $this->assertInstanceOf(EntityValidationException::class, $th);
        }
    }

    public function testNotNullWithCustomMessage()
    {
        try{
            $value = '';
            DomainValidation::notNull($value, 'The value cant be empty or null');

            $this->assertTrue(false);
        }catch(Throwable $th){
            $this->assertInstanceOf(EntityValidationException::class, $th);
            $this->assertEquals('The value cant be empty or null', $th->getMessage());
        }
    }

    public function testStrMaxLength()
    {
        try{
            $value = 'Testee';
            DomainValidation::strMaxLength($value, 5, 'Custom Message');

            $this->assertTrue(false);
        }catch(Throwable $th){
            $this->assertInstanceOf(EntityValidationException::class, $th, 'Custom Message');
        }
    }

    public function testStrMinLength()
    {
        try{
            $value = 'Testee';
            DomainValidation::strMinLength($value, 8, 'Custom Message');

            $this->assertTrue(false);
        }catch(Throwable $th){
            $this->assertInstanceOf(EntityValidationException::class, $th, 'Custom Message');
        }
    }

    public function testCanNullMaxLength()
    {
        try{
            $value = 'Testee';
            DomainValidation::canNullMaxLength($value, 2, 'Custom Message');

            $this->assertTrue(false);
        }catch(Throwable $th){
            $this->assertInstanceOf(EntityValidationException::class, $th, 'Custom Message');
        }
    }
}