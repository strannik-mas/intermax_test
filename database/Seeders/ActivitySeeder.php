<?php
declare(strict_types=1);

namespace Database\Seeders;
use App\Enum\ActivityType;

class ActivitySeeder extends BaseSeeder
{

    public function run(): void
    {
        $clientIds = $this->db->table('clients')->fetchPairs(null, 'id');

        $types = array_column(ActivityType::cases(), 'value');

        foreach ($clientIds as $index => $clientId) {
            //for the first 3 clients, generate a higher number of activities to create more realistic data
            $count = $index < 3 ?
                $this->faker->numberBetween(5000, 10000) :
                $this->faker->numberBetween(5, 50);

            $rows = [];
            for ($i = 0; $i < $count; $i++) {
                $rows[] = [
                    'client_id' => (int) $clientId,
                    'activity_type' => $this->faker->randomElement($types),
                    'details' => $this->faker->sentence(),
                    'created_at' => $this->faker->dateTimeBetween('-1 year', 'now')->format('Y-m-d H:i:s'),
                ];

                // Insert in batches of 1000 to improve performance
                if (count($rows) >= 1000) {
                    $this->db->table('client_activities')->insert($rows);
                    $rows = [];
                }
            }

            // Insert any remaining rows
            if (count($rows) > 0) {
                $this->db->table('client_activities')->insert($rows);
            }
        }

        echo "Client activities seeded successfully.\n";
    }
}