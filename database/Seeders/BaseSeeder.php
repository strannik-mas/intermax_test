<?php
declare(strict_types=1);

namespace Database\Seeders;

use Nette\Database\Explorer;
use Faker\Generator;

abstract class BaseSeeder
{
    public function __construct(
        protected Explorer $db,
        protected Generator $faker
    )
    {
    }
    abstract public function run(): void;
}