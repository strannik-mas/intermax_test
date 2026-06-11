<?php
declare(strict_types=1);

namespace App\Service;

use App\Repository\ActivityCommentRepository;

class CommentService
{
    public function __construct(private ActivityCommentRepository $activityCommentRepository)
    {
    }

    public function addComment(int $activityId, string $comment): void
    {
        $this->activityCommentRepository->addComment($activityId, $comment);
    }
}