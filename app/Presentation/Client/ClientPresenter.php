<?php
declare(strict_types=1);

namespace App\Presentation\Client;

use App\Repository\ActivityCommentRepository;
use App\Repository\ClientActivityRepository;
use App\Repository\ClientRepository;
use App\Service\ClientSearchService;
use App\Service\CommentService;
use Nette\Application\AbortException;
use Nette\Application\BadRequestException;
use Nette\Application\UI\Presenter;
use Random\RandomException;

class ClientPresenter extends Presenter
{
    public function __construct(
        private ClientSearchService $clientSearchService,
        private CommentService $commentService,
        private ClientRepository $clientRepository,
        private ClientActivityRepository $clientActivityRepository,
        private ActivityCommentRepository $activityCommentRepository
    ) {
    }

    /**
     * Default action for displaying the client search form and results.
     * @return void
     */
    public function renderDefault(): void
    {
        $name = $this->getParameter('name', '');
        $email = $this->getParameter('email', '');
        $isActive = $this->getParameter('isActive', '');
        $sortBy = $this->getParameter('sortBy', 'created_at');
        $sortOrder = $this->getParameter('sortOrder', 'DESC');

        $result = $this->clientSearchService->searchClients($name, $email, $isActive, $sortBy, $sortOrder);
        $this->template->clients = $result;
    }

    /**
     * Detail action for displaying a specific client's information, activities, and comments.
     * @param int $id
     * @return void
     */
    public function renderDetail(int $id): void
    {
        $client = $this->clientRepository->findById($id);

        if (!$client) {
            throw new BadRequestException('Client not found');
        }

        $this->template->client = $client;

        // Fetch activities with pagination
        $page = max(1, (int)$this->getParameter('page', 1));
        $limit = 10;
        $offset = ($page - 1) * $limit;

        $activities = $this->clientActivityRepository->getClientActivities($id, $limit, $offset)->fetchAll();
        $totalActivities = $this->clientActivityRepository->countClientActivities($id);

        // Fetch comments for each activity
        $activityIds = array_column($activities, 'id');
        $allComments = $this->activityCommentRepository->getCommentsForActivities($activityIds);
        $comments = [];
        foreach ($allComments as $comment) {
            $comments[$comment->activity_id][] = $comment;
        }

        $this->template->activities = $activities;
        $this->template->totalActivities = $totalActivities;
        $this->template->currentPage = $page;
        $this->template->totalPages = ceil($totalActivities / $limit);
        $this->template->comments = $comments;

        // Generate CSRF token for AJAX comment submission
        $session = $this->getSession()->getSection('csrf');
        if (!isset($session->token)) {
            $session->token = bin2hex(random_bytes(32));
        }
        $this->template->csrfToken = $session->token;
    }

    public function handleAddComment(): void
    {
        // Validate CSRF token
        $token = $this->getHttpRequest()->getPost('_token');
        $session = $this->getSession()->getSection('csrf');
        if (!isset($session->token) || !hash_equals($session->token, $token)) {
            $this->sendJson(['success' => false, 'error' => 'Invalid CSRF token.']);
            return;
        }

        // Validate input parameters
        $activityId = $this->getHttpRequest()->getPost('activityId');
        if (empty($activityId)) {
            $this->sendJson(['success' => false, 'error' => 'Activity ID is required.']);
            return;
        }

        $comment = (string) $this->getHttpRequest()->getPost('comment');
        $comment = trim($comment);
        if ($comment === '') {
            $this->sendJson(['success' => false, 'error' => 'Comment cannot be empty.']);
            return;
        }

        try {
            // Add the comment to the database
            $this->commentService->addComment((int)$activityId, $comment);
            $dateNow = (new \DateTime())->format('Y-m-d H:i:s');
            $this->sendJson([
                'success' => true,
                'comment' => $comment,
                'created_at' => $dateNow
            ]);
        } catch (AbortException $e) {
            throw $e;
        } catch (\Throwable $e) {
            $this->sendJson([
                'success' => false,
                'error' => get_class($e) . ': ' . $e->getMessage() . ' in ' . $e->getFile() . ':' . $e->getLine()
            ]);
        }
    }
}