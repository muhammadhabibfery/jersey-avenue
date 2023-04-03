<?php

namespace Tests;

use App\Models\User;
use Illuminate\Support\Facades\Storage;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    use CreatesApplication, RefreshDatabase;

    /**
     * Create a user instance.
     */
    public function createUser(?array $data = [], ?int $count = 1): User|Collection
    {
        $users = User::factory()->count($count)->create($data);

        return $count < 2 ? $users->first() : $users;
    }

    /**
     * Create authenticated user.
     */
    public function authenticatedUser(?array $data = []): User
    {
        $user = $this->createUser($data);
        $this->actingAs($user);
        return $user;
    }

    /**
     * Delete file(s).
     */
    public function deleteFile(string $path): void
    {
        Storage::disk('public')->delete($path);
    }
}
