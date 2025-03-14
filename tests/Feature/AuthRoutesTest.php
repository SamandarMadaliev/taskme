<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Models\Task;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Symfony\Component\HttpFoundation\Response;
use Tests\TestCase;

class AuthRoutesTest extends TestCase
{
    use RefreshDatabase;

    private const BASE_ENDPOINT = '/api/tasks';
    private const TEST_DATA = [
        'title' => 'Test Task',
        'description' => 'This is a test task.',
        'status' => 'pending',
        'priority' => 'low',
        'due_date' => '2025-12-31 23:59:59',
    ];
    protected User $user;
    protected Task $task;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create();
        $this->task = Task::factory()->create();
    }

    /**
     * Test if an authenticated user can access protected routes.
     */
    public function test_authenticated_user_can_access_protected_routes()
    {
        Sanctum::actingAs($this->user);
        $createTask = $this->postJson(self::BASE_ENDPOINT, self::TEST_DATA);
        $createTask->assertCreated();
        $taskId = $createTask->json('task')['id'];
        foreach ($this->protectedRoutes($taskId) as $route) {
            [$method, $uri, $data] = $route;
            $response = $this->makeRequest($method, $uri, $data);
            $response->assertSuccessful();
        }
    }

    /**
     * Test if an unauthenticated user is blocked from protected routes.
     */
    public function test_unauthenticated_user_cannot_access_protected_routes()
    {
        $taskId = $this->task->id;
        foreach ($this->protectedRoutes($taskId) as $route) {
            [$method, $uri, $data] = $route;
            $response = $this->makeRequest($method, $uri, $data);
            $response->assertStatus(Response::HTTP_UNAUTHORIZED);
        }
    }

    /**
     * Provides protected routes with their HTTP methods and optional data.
     */
    private function protectedRoutes(?int $taskId): array
    {
        /**
         * Routes array
         * [
         *  [request-method, endpoint, request-data]
         * ]
         */
        return [
            ['postJson', self::BASE_ENDPOINT, self::TEST_DATA],
            ['getJson', self::BASE_ENDPOINT, []],
            ['getJson', self::BASE_ENDPOINT . '/' . $taskId, []],
            ['putJson', self::BASE_ENDPOINT . '/' . $taskId, ['title' => 'Updated Title']],
            ['putJson', self::BASE_ENDPOINT . '/' . $taskId . '/complete', []],
            ['deleteJson', self::BASE_ENDPOINT . '/' . $taskId, []],
        ];
    }


    /**
     * Helper method to perform API requests dynamically.
     */
    protected function makeRequest($method, $uri, $data = [])
    {
        return empty($data) ? $this->{$method}($uri) : $this->{$method}($uri, $data);
    }
}
