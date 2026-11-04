<?php

namespace Modules\Notifications\Reposiotires;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Modules\Notifications\Reposiotires\Contracts\NotificationRepositoryInterface;
use Modules\Notifications\Models\Notification;

class NotificationRepository implements NotificationRepositoryInterface
{
    public function __construct(
        private Notification $model,
    ) {}

    public function paginateForUser(
        int $userId,
        bool $unreadOnly = false,
        int $perPage = 20
    ): LengthAwarePaginator {
        return $this->model
            ->newQuery()
            ->forUser($userId)
            ->when($unreadOnly, fn ($q) => $q->unread())
            ->latest()
            ->paginate($perPage);
    }

    public function unreadCountForUser(int $userId): int
    {
        return $this->model
            ->newQuery()
            ->forUser($userId)
            ->unread()
            ->count();
    }

    public function findByUuid(string $uuid): ?Notification
    {
        return $this->model
            ->newQuery()
            ->where('uuid', $uuid)
            ->first();
    }

    public function markAsRead(Notification $notification): Notification
    {
        if (! $notification->is_read) {
            $notification->update([
                'is_read' => true,
            ]);
        }

        return $notification->refresh();
    }

    public function markAllAsReadForUser(int $userId): int
    {
        return $this->model
            ->newQuery()
            ->forUser($userId)
            ->unread()
            ->update([
                'is_read' => true,
            ]);
    }
}