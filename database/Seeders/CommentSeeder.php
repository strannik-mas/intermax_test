<?php
declare(strict_types=1);

namespace Database\Seeders;

class CommentSeeder extends BaseSeeder
{

    public function run(): void
    {
        //find random client activities
        $activityIds = $this->db->query('SELECT id FROM client_activities ORDER BY RAND() LIMIT 500')
            ->fetchPairs(null, 'id');
        $rows = [];

        foreach ($activityIds as $activityId) {
            $commentCount = $this->faker->numberBetween(1, 4);

            for ($i = 0; $i < $commentCount; $i++) {
                $rows[] = [
                    'activity_id' => (int) $activityId,
                    'comment' => $this->faker->paragraph(),
                    'created_at' => $this->faker->dateTimeBetween('-1 year', 'now')->format('Y-m-d H:i:s'),
                ];

                // Insert in batches of 1000 to improve performance
                if (count($rows) >= 1000) {
                    $this->db->table('activity_comments')->insert($rows);
                    $rows = [];
                }
            }
        }

        // Insert any remaining rows
        if (count($rows) > 0) {
            $this->db->table('activity_comments')->insert($rows);
        }

        echo "Activity comments seeded successfully.\n";
    }
}