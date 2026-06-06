<?php

namespace App\Http\Controllers\Api\V1\Admin;

use App\Models\User;
use App\Traits\ApiResponser;
use App\Services\UserService;
use App\Http\Controllers\Controller;
use Dedoc\Scramble\Attributes\Group;
use App\Http\Requests\User\ChangeStatus;
use Illuminate\Routing\Attributes\Middleware;
use App\Http\Resources\User\Resource as UserResource;

/**
 * @tags Admin / User
 */
#[Group('Admin / User', weight: 70)]
#[Middleware(['auth:api', 'notification-read'])]
class UserStatusController extends Controller
{
    use ApiResponser;

    private UserService $userService;

    public function __construct()
    {
        $this->userService = new UserService;
    }

    /**
     * Change status.
     *
     * @response array{message: string, user: UserResource}
     */
    public function __invoke(User $user, ChangeStatus $request)
    {
        $user = $this->userService->changeStatus($user, $request->validated());

        return $this->success($user);
    }
}
