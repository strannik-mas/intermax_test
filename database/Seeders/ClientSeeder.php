<?php
declare(strict_types=1);

namespace Database\Seeders;

class ClientSeeder extends BaseSeeder
{

    public function run(): void
    {
        $rows = [];
        for ($i = 1; $i <= 50; $i++) {
            $rows[] = [
                'name' => $this->faker->name(),
                'email' => $this->faker->unique()->safeEmail(),
                'is_active' => $this->faker->boolean(80), // 80% chance of being active
                'created_at' => $this->faker->dateTimeBetween('-2 years', 'now')->format('Y-m-d H:i:s'),
            ];
        }
        $this->db->table('clients')->insert($rows);
        echo "Clients seeded successfully.\n";
    }
}