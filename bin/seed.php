<?php

declare(strict_types=1);

use App\Bootstrap;
use Database\Seeders\ActivitySeeder;
use Database\Seeders\ClientSeeder;
use Database\Seeders\CommentSeeder;
use Faker\Factory;
use Nette\Database\Explorer;

require __DIR__ . '/../vendor/autoload.php';

$container = (new Bootstrap())->bootConsoleApplication();
$db = $container->getByType(Explorer::class);
$faker = Factory::create();

//important: truncate tables before seeding
$db->query('SET FOREIGN_KEY_CHECKS = 0');
$db->query('TRUNCATE TABLE clients');
$db->query('TRUNCATE TABLE client_activities');
$db->query('TRUNCATE TABLE activity_comments');
$db->query('SET FOREIGN_KEY_CHECKS = 1');

//call seeder classes
(new ClientSeeder($db, $faker))->run();
(new ActivitySeeder($db, $faker))->run();
(new CommentSeeder($db, $faker))->run();

echo "Done.\n";
