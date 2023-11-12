<?php

namespace Core\UseCase\DTO\Genre\Create;

class GenreCreateInputDto
{
    public function __construct(
        public string $name,
        public bool $is_active = true,
        public array $categoriesId = []
    )
    {

    }
}