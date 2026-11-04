<?php

namespace Modules\Notifications\app\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Modules\Notifications\Actions\NotificationsAction;
use Modules\Notifications\Reposiotires\Contracts\NotificationRepositoryInterface;
use Modules\Notifications\app\Http\Resources\NotificationResource;
use Modules\Notifications\Models\Notification;
use Modules\Shared\App\Traits\AuthenticatedUserTrait;

class NotificationsController extends Controller
{
    use AuthenticatedUserTrait;

    public function __construct(
        private NotificationRepositoryInterface $repository,
        private NotificationsAction $notifications,
    ) {}

    public function index(Request $request)
    {
        $notifications = $this->repository->paginateForUser(
            userId: $this->getAuthenticatedUserId(),
            unreadOnly: $request->boolean('unread_only'),
        );

        return NotificationResource::collection($notifications);
    }

    public function unreadCount(): JsonResponse
    {
        $count = $this->repository->unreadCountForUser(
            $this->getAuthenticatedUserId(),
        );

        return response()->json(['count' => $count]);
    }

    public function markAsRead( $uuid): NotificationResource
    {
        $notification = $this->notifications->markAsRead(
             $uuid,
             $this->getAuthenticatedUserId(),
        );

        return new NotificationResource($notification);
    }

    public function markAllAsRead(): JsonResponse
    {
        $this->notifications->markAllAsRead(
            $this->getAuthenticatedUserId()
        );

        return response()->json(['message' => 'All notifications marked as read']);
    }
}