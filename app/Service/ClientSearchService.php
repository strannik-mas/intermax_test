<?php
declare(strict_types=1);

namespace App\Service;

use App\Repository\ClientRepository;
use Nette\Database\Table\Selection;

class ClientSearchService
{
    public function __construct(private ClientRepository $clientsRepository)
    {
    }

    public function searchClients(
        string $name,
        string $email,
        string $isActive,
        string $sortBy = 'created_at',
        string $sortOrder = 'DESC'
    ): Selection {
        $isActiveBool = match ($isActive) {
            '1' => true,
            '0' => false,
            default => null,
        };
        return $this->clientsRepository->search($name, $email, $isActiveBool, $sortBy, $sortOrder);
    }
}