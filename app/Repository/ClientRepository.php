<?php
declare(strict_types=1);

namespace App\Repository;

use Nette\Database\Table\ActiveRow;
use Nette\Database\Table\Selection;

class ClientRepository extends BaseRepository
{
    public function findAll(): Selection
    {
        return $this->db->table('clients');
    }

    public function findById(int $id): ?ActiveRow
    {
        return $this->db->table('clients')->get($id);
    }

    public function search(
        string $name = '',
        string $email = '',
        ?bool $isActive = null,
        string $sortBy = 'created_at',
        string $sortOrder = 'DESC'
    ): Selection {
        $resultSelection = $this->db->table('clients');

        if (! empty($name)) {
            $resultSelection->where('name LIKE ?', '%' . $name . '%');
        }
        if (! empty($email)) {
            $resultSelection->where('email LIKE ?', '%' . $email . '%');
        }

        if ($isActive !== null) {
            $resultSelection->where('is_active', $isActive);
        }

        //only allow sorting by specific columns to prevent SQL injection
        $allowedSort = ['name', 'email', 'created_at'];
        $sortBy = in_array($sortBy, $allowedSort) ? $sortBy : 'created_at';
        $sortOrder = strtoupper($sortOrder) === 'ASC' ? 'ASC' : 'DESC';

        $resultSelection->order("$sortBy $sortOrder");

        return $resultSelection;
    }
}