<?php

namespace Modules\Users\Actions;

use Modules\Users\Models\User;
use Modules\Users\Repositries\Contracts\UserRepositoryInterface;
use Illuminate\Support\Facades\Hash;
use PHPOpenSourceSaver\JWTAuth\Facades\JWTAuth; // أضف هذا في الأعلى

class AuthinticationLogical {

    public function __construct(
        private UserRepositoryInterface $userRepository
    ) {}

    
protected function respondWithToken(string $accessToken): array
{
    // 1. جلب المستخدم الحالي
    $user = auth('api')->user();

    // 2. توليد Refresh Token حقيقي بصلاحية 14 يوماً
    $refreshToken = JWTAuth::customClaims([
        'type' => 'refresh',
        'exp' => now()->addDays(14)->timestamp 
    ])->fromUser($user);

    return [
        'access_token' => $accessToken,
        'token_type' => 'bearer',
        'expires_in' => auth('api')->factory()->getTTL() * 60,
        'refresh_token' => $refreshToken, // <--- الآن التوكن موجود
    ];
}
    // تغيير النوع إلى array
    public function execute($data): array 
    {
        
        $token = auth('api')->attempt([
            'email' => $data['email'],
            'password' => $data['password']
        ]);

        if (!$token) {
            throw new \Exception('بيانات الاعتماد غير صحيحة', 401);
        }

        return $this->respondWithToken($token);
    }

    public function Expire(): void
    {
         try {
        // إبطال التوكن الحالي (وضعه في الـ Blacklist) ومسح جلسة المستخدم
        auth('api')->logout();
    } catch (\Exception $e) {
        throw new \Exception('فشل تسجيل الخروج أو أن التوكن غير صالح', 401);
    }
    }

   public function Refresh(): array
{
    try {
        $token = JWTAuth::getToken();
        
        $payload = JWTAuth::getPayload($token);
        
        if ($payload->get('type') !== 'refresh') {
            throw new \Exception('This is not a valid refresh token', 401);
        }

        $user = JWTAuth::toUser($token);

        $newAccessToken = auth('api')->login($user);

        $newRefreshToken = JWTAuth::customClaims([
            'type' => 'refresh',
            'exp' => now()->addDays(14)->timestamp
        ])->fromUser($user);

        return [
            'access_token' => $newAccessToken,
            'token_type' => 'bearer',
            'expires_in' => auth('api')->factory()->getTTL() * 60,
            'refresh_token' => $newRefreshToken,
        ];
    } catch (\Exception $e) {
        throw new \Exception('Refresh token is invalid or expired: ' . $e->getMessage(), 401);
    }
}

    public function Register($data): array
    {
        $password = $data['password'];
        $data['password'] = Hash::make($data['password']);
        $user = $this->userRepository->create($data);
        $request = [
            'email'=> $data['email'],
            'password'=> $password
        ];
        $token = $this->execute($request);
        $dataInformation = [
            'user' => $user,
            'token' => $token
        ];
        return $dataInformation;

    }

    // تبسيط دالة Login، لا داعي للتحقق اليدوي لأن attempt يفعل ذلك
    public function Login($data): array
    {
        $user = $this->userRepository->findByEmail($data['email']);
        if (!$user) {
            throw new \Exception('المستخدم غير موجود', 404);
        }
        if(!Hash::check($data['password'], $user->password)) {
            throw new \Exception('كلمة المرور غير صحيحة', 401);
        }

        $info = [
            'user' => $user,
            'info_token' => $this->execute($data)
        ];
        return $info;
    }

    public function Profile()
    {
        return auth('api')->user();
    }
}