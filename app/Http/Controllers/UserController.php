<?php

namespace App\Http\Controllers;

use App\Http\Requests\User\LoginRequest;
use App\Http\Requests\User\StoreUserRequest;
use App\Http\Requests\User\UpdateUserRequest;
use App\Http\Resources\UserResource;
use App\Repositories\Contracts\UserRepositoryInterface;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Tymon\JWTAuth\Facades\JWTAuth;
class UserController extends Controller
{
     protected $users;

    public function __construct(UserRepositoryInterface $users)
    {
        $this->users = $users;
    }
/**
 * @OA\Post(
 *     path="/api/register",
 *     summary="Đăng ký người dùng",
 *     tags={"Auth"},
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\JsonContent(ref="#/components/schemas/RegisterRequest")
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Đăng ký thành công",
 *         @OA\JsonContent(
 *             @OA\Property(property="status", type="boolean", example=true),
 *             @OA\Property(property="message", type="string", example="Đăng ký thành công"),
 *             @OA\Property(property="token", type="string", example="jwt.token.here"),
 *             @OA\Property(property="data", ref="#/components/schemas/User")
 *         )
 *     )
 * )
 */

    public function register(StoreUserRequest $request)
    {
        $user = $this->users->create($request->validated());
        $token = JWTAuth::fromUser($user);

        return response()->json([
            'status' => true,
            'message' => 'Đăng ký thành công',
            'token' => $token,
            'data' => new UserResource($user),
        ]);
    }
    /**
 * @OA\Post(
 *     path="/api/login",
 *     summary="Đăng nhập người dùng",
 *     tags={"Auth"},
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\JsonContent(ref="#/components/schemas/LoginRequest")
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Đăng nhập thành công",
 *         @OA\JsonContent(
 *             @OA\Property(property="status", type="boolean", example=true),
 *             @OA\Property(property="message", type="string", example="Đăng nhập thành công"),
 *             @OA\Property(property="token", type="string", example="jwt.token.here"),
 *             @OA\Property(property="data", ref="#/components/schemas/User")
 *         )
 *     ),
 *     @OA\Response(
 *         response=401,
 *         description="Sai email hoặc mật khẩu",
 *         @OA\JsonContent(
 *             @OA\Property(property="status", type="boolean", example=false),
 *             @OA\Property(property="message", type="string", example="Sai email hoặc mật khẩu")
 *         )
 *     )
 * )
 */

    public function login(LoginRequest $request)
    {
        $credentials = $request->only('email', 'password');

        if (!$token = Auth::guard('api')->attempt($credentials)) {
            return response()->json([
                'status' => false,
                'message' => 'Sai email hoặc mật khẩu',
            ], 401);
        }

        return response()->json([
            'status' => true,
            'message' => 'Đăng nhập thành công',
            'token' => $token,
            'data' => new UserResource(Auth::guard('api')->user()),
        ]);
    }
/**
 * @OA\Get(
 *     path="/api/profile",
 *     summary="Lấy thông tin người dùng hiện tại",
 *     tags={"Auth"},
 *     security={{"bearerAuth":{}}},
 *     @OA\Response(
 *         response=200,
 *         description="Thông tin người dùng",
 *         @OA\JsonContent(ref="#/components/schemas/User")
 *     )
 * )
 */

    public function profile()
    {
        return new UserResource(auth::guard('api')->user());
    }
/**
 * @OA\Post(
 *     path="/api/logout",
 *     summary="Đăng xuất người dùng",
 *     tags={"Auth"},
 *     security={{"bearerAuth":{}}},
 *     @OA\Response(
 *         response=200,
 *         description="Đăng xuất thành công",
 *         @OA\JsonContent(
 *             @OA\Property(property="message", type="string", example="Đăng xuất thành công")
 *         )
 *     )
 * )
 */

    public function logout()
    {
        Auth::guard('api')->logout();
        return response()->json(['message' => 'Đăng xuất thành công']);
    }


/**
 * @OA\Put(
 *     path="/api/user/update",
 *     summary="Cập nhật thông tin người dùng",
 *     tags={"User"},
 *     security={{"bearerAuth":{}}},
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\JsonContent(ref="#/components/schemas/UserUpdateRequest")
 *     ),   
 * *     @OA\Response(
 *         response=200,
 *         description="Cập nhật thông tin thành công",
 *         @OA\JsonContent(
 *             @OA\Property(property="status", type="boolean", example=true),
 *             @OA\Property(property="message", type="string", example="Cập nhật thông tin thành công"),        
 *            @OA\Property(property="data", ref="#/components/schemas/User")
 *        )
 * *     ),
 *    @OA\Response(
 *        response=422,
 *       description="Dữ liệu không hợp lệ",
 *       @OA\JsonContent(
 *            @OA\Property(property="status", type="boolean", example=false),
 *           @OA\Property(property="message", type="string", example="Dữ liệu không hợp lệ")
 * *        )
 *    )
 * )
 * */
    public function updateProfile(UpdateUserRequest $request){
        $user = $this->users->updateUser(Auth::id(), $request->validated());
        return response()->json([
            'status' => true,
            'message' => 'Cập nhật thông tin thành công',
            'data' => new UserResource($user),
        ]);
    }
}
