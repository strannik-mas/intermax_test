<?php

declare(strict_types=1);

use App\Bootstrap;
use Nette\Database\Explorer;

require __DIR__ . '/../vendor/autoload.php';

$container = (new Bootstrap())->bootConsoleApplication();
$db = $container->getByType(Explorer::class);

//create migration table if not exists
$db->query(<<<SQL
CREATE TABLE IF NOT EXISTS migrations (
    id INT AUTO_INCREMENT PRIMARY KEY,
    migration VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
)
SQL);

//applied migrations
$applied = $db->table('migrations')->fetchPairs(null, 'migration');

//get all migration files from database/migrations by alphabetical order
$files = glob(__DIR__ . "/../database/Migrations/*.sql");
sort($files);

foreach ($files as $file) {
    $filename = basename($file);

    //check if migration is already applied
    if (in_array($filename, $applied, true)) {
        echo "Skipping $filename (already applied)\n";
        continue;
    }

    //apply migration
    $sql = file_get_contents($file);
    $db->query($sql);

    //record applied migration
    $db->table('migrations')->insert(['migration' => $filename]);
    echo "Applied $filename\n";
}

echo "Done.\n";
