<?php
declare(strict_types=1);

namespace App\Repository;

use Nette\Database\Explorer;

abstract class BaseRepository
{
    public function __construct(protected Explorer $db)
    {
        
    }
}