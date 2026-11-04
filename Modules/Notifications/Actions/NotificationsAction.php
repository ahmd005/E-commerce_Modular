<?php

namespace Modules\Notifications\Actions;

use App\Models\User;
use Modules\Notifications\Models\Notification;
use Illuminate\Database\Eloquent\Model;
use Modules\Notifications\Reposiotires\Contracts\NotificationRepositoryInterface;

class NotificationsAction
{
    public function __construct(
        private Notification $notificationModel,
        private NotificationRepositoryInterface $notificationRepository
    ) {}

    public function createForUser(
        User $user,
        string $title,
        string $message,
    ): Notification {
        return $this->notificationModel->create([
            'user_id'         => $user->id,
            'title'           => $title,
            'message'         => $message,
                    ]);
    }

    public function markAsRead($uuid): Notification
    {
        $notification = $this->notificationRepository->findByUuid($uuid);
        if (! $notification->is_read) {
            $notification->update([
                'is_read' => true,
                'read_at' => now(),
            ]);
        }

        return $notification->refresh();
    }

    public function markAllAsRead($id)
    {
        try{
            return $this->notificationRepository->markAllAsReadForUser($id);
        } catch (\Exception $e) {
            // يمكنك تسجيل الخطأ هنا إذا رغبت
            return response()->json("Error: " . $e->getMessage(), 500); // أو أي قيمة مناسبة تشير إلى الفشل
        }
    }
}