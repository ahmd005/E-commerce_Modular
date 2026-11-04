<?php

namespace Modules\Notifications\Reposiotires\Contracts;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Modules\Notifications\Models\Notification;

interface NotificationRepositoryInterface
{
    /**
     * جلب إشعارات مستخدم مع Pagination.
     */
    public function paginateForUser(int $userId, bool $unreadOnly = false, int $perPage = 20): LengthAwarePaginator;

    /**
     * عدد الإشعارات غير المقروءة لمستخدم.
     */
    public function unreadCountForUser(int $userId): int;

    /**
     * جلب إشعار واحد بمعرّفه (uuid).
     */
    public function findByUuid(string $uuid): ?Notification;

    /**
     * تعليم إشعار واحد كمقروء.
     */
    public function markAsRead(Notification $notification): Notification;

    /**
     * تعليم كل إشعارات مستخدم كمقروءة.
     *
     * @return int عدد الصفوف المتأثرة
     */
    public function markAllAsReadForUser(int $userId): int;
}