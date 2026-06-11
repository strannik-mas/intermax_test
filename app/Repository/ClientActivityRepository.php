<?php
declare(strict_types=1);

namespace App\Repository;

use Nette\Database\Table\Selection;

class ClientActivityRepository extends BaseRepository
{
    public function getClientActivities(int $clientId, int $limit, int $offset): Selection
    {
        return $this->db->table('client_activities')
            ->where('client_id', $clientId)
            ->order('created_at DESC, id DESC')
            ->limit($limit, $offset);
    }

    public function countClientActivities(int $clientId): int
    {
        return $this->db->table('client_activities')
            ->where('client_id', $clientId)
            ->count('*');
    }
}