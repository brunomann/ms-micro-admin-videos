<?php

namespace Core\Domain\Validation;

use Core\Domain\Exception\EntityValidationException;
use Exception;

class DomainValidation
{
    public static function notNull(string $value, $exeptionMessage = null)
    {
        if(empty($value)){
            throw new EntityValidationException($exeptionMessage ?? "Value can't be empty");
        }
    }

    public static function strMaxLength(string $value, int $maxLength = 255, $exeptionMessage = null)
    {
        if(strlen($value) > $maxLength){
            throw new EntityValidationException($exeptionMessage ?? "Quanity of characters can't be more than {$maxLength}");
        }
    }

    public static function strMinLength(string $value, int $minLength = 3, $exeptionMessage = null)
    {
        if(strlen($value) < $minLength){
            throw new EntityValidationException($exeptionMessage ?? "Quanity of characters can't be less than {$minLength}");
        }
    }

    public static function canNullMaxLength(string $value = '', int $maxLength = 255, $exeptionMessage = null)
    {
        if(!empty($value) && strlen($value) > $maxLength){
            throw new EntityValidationException($exeptionMessage ?? "Quanity of characters can't be more than {$maxLength}");
        }
    }
}