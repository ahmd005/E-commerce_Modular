<?php

namespace Modules\Shared\App\Traits;

use Illuminate\Support\Facades\Auth;
use Exception;

trait AuthenticatedUserTrait
{
    /**
     * الحصول على معرّف المستخدم الحالي المصادق عليه عبر JWT
     *
     * @throws Exception
     */
    protected function getAuthenticatedUserId()
    {
        // استخدام guard الـ api الخاص بـ JWT لجلب المستخدم الحالي
        $user = auth('api')->user();

        if (!$user) {
            throw new Exception('Unauthorized: User not found or invalid token', 401);
        }

        return $user->id;
    }

    /**
     * الحصول على كائن المستخدم الحالي بالكامل
     */
    protected function getAuthenticatedUser()
    {
        $user = auth('api')->user();

        if (!$user) {
            throw new Exception('Unauthorized: User not found or invalid token', 401);
        }

        return $user;
    }
}