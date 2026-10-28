<?php
namespace Modules\Users\app\Contracts;

interface UserContract
{

    // التحقق من وجود المستخدم
    public function userExists(int $userId): bool;
    
    // جلب البريد الإلكتروني (مثلاً لإرسال إشعار)
    public function getUserEmail(int $userId): ?string;
    
    // جلب الـ UUID (إذا كنت تستخدمه في الطلبات)
    public function getUserUuid(int $userId): ?string;
    
    // جلب الأدوار (لتحديد الصلاحيات)
    public function getUserRoles(int $userId): array;
}