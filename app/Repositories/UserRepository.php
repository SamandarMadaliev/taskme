<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Models\User;
use Illuminate\Database\Eloquent\Collection;

class UserRepository implements UserRepositoryInterface
{
    protected User $userModel;

    public function __construct()
    {
        $this->userModel = new User();
    }

    public function all(): Collection
    {
        return $this->userModel->all();
    }

    public function find(int $id): User
    {
        return $this->userModel->findOrFail($id);
    }

    public function create(array $data)
    {
        return $this->userModel->create($data);
    }

    public function update(int $id, array $data): User
    {
        $user = $this->find($id);
        $user->update($data);
        return $user;
    }

    public function delete(int $id): ?bool
    {
        $user = $this->find($id);
        return $user->delete();
    }

    public function findByEmail(string $email): ?User
    {
        return $this->userModel::query()->where('email', $email)->first();
    }
}
