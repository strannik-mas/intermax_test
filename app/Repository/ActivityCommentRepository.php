<?php
declare(strict_types=1);

namespace App\Repository;

use Nette\Database\Table\Selection;

class ActivityCommentRepository extends BaseRepository
{
    public function getAllActivityComments(int $activityId): Selection
    {
        return $this->db->table('activity_comments')
            ->where('activity_id', $activityId)
            ->order('created_at ASC');
    }

    public function getCommentsForActivities(array $activityIds): array
    {
        return $this->db->table('activity_comments')
            ->where('activity_id', $activityIds)
            ->order('created_at ASC')
            ->fetchAll();
    }

    public function addComment(int $activityId, string $comment): void
    {
        $this->db->table('activity_comments')->insert([
            'activity_id' => $activityId,
            'comment' => $comment
        ]);
    }
}