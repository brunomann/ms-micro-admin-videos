<?php

namespace Core\UseCase\DTO\Genre;

use DateTime;

class GenreOutputDto
{
    public function __construct(
        public string $id,
        public string $name,
        public bool $is_active = true,
        public string|DateTime $created_at = ''
    )
    {

    }
}