<?php
declare(strict_types=1);

namespace App\Presentation\Api;

use App\Service\ClientSearchService;
use Nette\Application\UI\Presenter;

class ApiPresenter extends Presenter
{
    public function __construct(private ClientSearchService $clientSearchService)
    {
    }

    /**
     * Search clients by AJAX based on post parameters and return results as JSON by sendJson method.
     * @return void
     */
    public function actionClients(): void
    {
        $name = $this->getParameter('name', '');
        $email = $this->getParameter('email', '');
        $isActive = $this->getParameter('isActive', '');
        $sortBy = $this->getParameter('sortBy', 'created_at');
        $sortOrder = $this->getParameter('sortOrder', 'DESC');

        $result = $this->clientSearchService->searchClients($name, $email, $isActive, $sortBy, $sortOrder);

        $clients = [];
        foreach ($result as $client) {
            $clients[] = [
                'id' => $client->id,
                'name' => $client->name,
                'email' => $client->email,
                'is_active' => (bool) $client->is_active,
                'created_at' => (string) $client->created_at,
            ];
        }
        $this->sendJson($clients);
    }
}