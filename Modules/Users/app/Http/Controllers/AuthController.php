<?php

namespace Modules\Users\App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Modules\Users\App\Http\Requests\LoginRequest;
use Modules\Users\app\Http\Resources\UserResource;
use Modules\Users\App\Http\Requests\RegisterRequest;
use Modules\Users\Actions\AuthinticationLogical;
class AuthController extends Controller
{

public function Register(RegisterRequest $request, AuthinticationLogical $auth)
    {
        $data = $request->validated();


        $user = $auth->Register($data);
        
        
        return response()->json([UserResource::make($user['user']),$user['token']], 201);
    }


    public function login(LoginRequest $request, AuthinticationLogical $auth)
    {
        $data = $request->validated();

        return response()->json([UserResource::make($auth->Login($data)['user']), $auth->Login($data)['info_token']], 200);
    }

    public function logout( AuthinticationLogical $auth){
        try{
              
            $auth->Expire();
             return response()->json(['message' => 'تم تسجيل الخروج بنجاح'], 200);

        }
        catch(\Exception $e){
            return response()->json(['message' => $e->getMessage()], 400);
        }
            }

            public function refresh(AuthinticationLogical $auth)
{
    try {

        $tokens = $auth->Refresh();
        return response()->json($tokens, 200);
    } catch (\Exception $e) {
        return response()->json(['error' => $e->getMessage()], 401);
    }
}
}
