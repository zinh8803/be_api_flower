<?php

namespace App\Http\Controllers;

use App\Http\Requests\User\LoginRequest;
use App\Http\Requests\User\StoreUserRequest;
use App\Http\Requests\User\UpdateUserRequest;
use App\Http\Resources\UserResource;
use App\Mail\SendOtpMail;
use App\Models\EmailOtp;
use App\Models\RefreshToken;
use App\Models\User;
use App\Repositories\Contracts\UserRepositoryInterface;
use Crypt;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

use Tymon\JWTAuth\Facades\JWTAuth;

class UserController extends Controller
{
    protected $users;

    public function __construct(UserRepositoryInterface $users)
    {
        $this->users = $users;
    }

    /**
     * @OA\Get(
     *     path="/api/users/getall",
     *     summary="Lấy danh sách người dùng",
     *     tags={"User"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Response(
     *         response=200,
     *         description="Danh sách người dùng",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Danh sách người dùng"),
     *             @OA\Property(property="data", type="array", @OA\Items(ref="#/components/schemas/User"))
     *         )
     *     )
     * )
     */
    public function index()
    {
        return UserResource::collection($this->users->getAll());
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
        $otpRecord = EmailOtp::where('email', $request->email)->first();

        if (!$otpRecord || $otpRecord->otp !== $request->otp || $otpRecord->expires_at < now()) {
            return response()->json([
                'status' => false,
                'message' => 'Mã OTP không đúng hoặc đã hết hạn',
            ], 422);
        }

        $user = $this->users->create($request->validated());
        $token = JWTAuth::fromUser($user);

        $refreshToken = Str::random(60);

        RefreshToken::create([
            'token' => $refreshToken,
            'user_id' => $user->id,
            'expires_at' => now()->addDays(30),
            'ip_address' => request()->ip(),
            'user_agent' => request()->header('User-Agent'),
        ]);

        $accessCookie = cookie('access_token', $token, 60, null, null, true, true, false, 'Strict');
        $refreshCookie = cookie('refresh_token', $refreshToken, 20160, null, null, true, true, false, 'Strict');

        return response()->json([
            'status' => true,
            'message' => 'Đăng ký thành công',
        ])->withCookie($accessCookie)->withCookie($refreshCookie);
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

    public function login(Request $request)
    {
        $credentials = $request->only('email', 'password');

        if (!$token = Auth::guard('api')->attempt($credentials)) {
            return response()->json([
                'status' => false,
                'message' => 'Email hoặc mật khẩu không đúng',
            ], 401);
        }
        $user = Auth::guard('api')->user();
        $refreshToken = Str::random(60);
        RefreshToken::create([
            'token' => $refreshToken,
            'user_id' => $user->id,
            'expires_at' => now()->addDays(30),
            'ip_address' => request()->ip(),
            'user_agent' => request()->header('User-Agent'),
        ]);

        // Set cookie HttpOnly
        $accessCookie = cookie('access_token', $token, 60, null, null, true, true, false, 'Strict');
        $refreshCookie = cookie('refresh_token', $refreshToken, 20160, null, null, true, true, false, 'Strict');

        return response()->json([
            'status' => true,
            'message' => 'Đăng nhập thành công'
        ], 200)->withCookie($accessCookie)->withCookie($refreshCookie);
    }

    /**
     * @OA\Post(
     *     path="/api/refresh-token",
     *     summary="Làm mới access token",
     *     tags={"Auth"},
     *     description="Lấy refresh_token từ cookie, không cần truyền trong body.",
     *     @OA\Response(
     *         response=200,
     *         description="Access token mới",
     *         @OA\JsonContent(
     *             @OA\Property(property="access_token", type="string", example="new.jwt.token.here")
     *         )
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Thiếu refresh token",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Missing refresh token")
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Refresh token không hợp lệ",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Refresh token không hợp lệ")
     *         )
     *     )
     * )
     */
    public function refreshToken(Request $request)
    {
        // Lấy refresh_token từ cookie
        $refreshToken = $request->cookie('refresh_token');
        if (!$refreshToken) {
            return response()->json([
                'status' => 400,
                'message' => 'Refresh token is required',
                'data' => null
            ], 400);
        }
        $tokenRecord = RefreshToken::where('token', $refreshToken)->first();

        if (!$tokenRecord) {
            return response()->json([
                'status' => 403,
                'message' => 'Invalid refresh token',
                'data' => null
            ], 403);
        }
        if ($tokenRecord->expires_at < now()) {
            return response()->json([
                'status' => 403,
                'message' => 'Refresh token đã hết hạn',
                'data' => null
            ], 403);
        }
        $user = User::find($tokenRecord->user_id);

        $newToken = JWTAuth::fromUser($user);

        $accessCookie = cookie('access_token', $newToken, 60, null, null, true, true, false, 'Strict');

        return response()->json([
            'status' => 200,
            'message' => 'New token generated successfully',
            'data' => [
                'access_token' => $newToken
            ]
        ])->withCookie($accessCookie);
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
        return new UserResource(auth()-> user());
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
        auth()->logout();
        $accessCookie = cookie('access_token', '', -1, null, null, true, true, false, 'Strict');
        $refreshCookie = cookie('refresh_token', '', -1, null, null, true, true, false, 'Strict');
        return response()->json(['message' => 'Đăng xuất thành công'])
            ->withCookie($accessCookie)
            ->withCookie($refreshCookie);
    }


    /**
     * @OA\Post(
     *     path="/api/user/update",
     *     summary="Cập nhật thông tin người dùng",
     *     tags={"User"},
     *     security={{"bearerAuth":{}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\MediaType(
     *             mediaType="multipart/form-data",
     *             @OA\Schema(ref="#/components/schemas/UserUpdateRequest")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Cập nhật thông tin thành công",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Cập nhật thông tin thành công"),
     *             @OA\Property(property="data", ref="#/components/schemas/User")
     *         )
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Dữ liệu không hợp lệ",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="Dữ liệu không hợp lệ")
     *         )
     *     )
     * )
     */
    public function updateProfile(UpdateUserRequest $request)
    {
        $user = $this->users->updateUser(auth()->user()->id, $request->validated());
        Log::info('User profile updated', ['request' => $request->all(), 'user_id' => auth()->user()->id]);
        return response()->json([
            'status' => true,
            'message' => 'Cập nhật thông tin thành công',
            'data' => new UserResource($user),
        ]);
    }


    /**
     * @OA\Post(
     *     path="/api/send-otp",
     *     summary="Gửi mã OTP đến email",
     *     tags={"Auth"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             @OA\Property(property="email", type="string", format="email", example="user@example.com")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Mã OTP đã được gửi",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Mã OTP đã được gửi đến email")
     *         )
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Dữ liệu không hợp lệ",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="Dữ liệu không hợp lệ")
     *         )
     *     )
     * )
     */
    public function sendOtp(Request $request)
    {
        $request->validate([
            'email' => 'required|email|unique:users,email',
        ]);

        $otp = rand(100000, 999999); // 6 chữ số

        // Lưu OTP
        EmailOtp::updateOrCreate(
            ['email' => $request->email],
            [
                'otp' => $otp,
                'expires_at' => now()->addMinutes(5),
            ]
        );

        // Gửi mail qua queue
        \App\Jobs\SendOtpMail::dispatch($request->email, $otp);

        return response()->json([
            'status' => true,
            'message' => 'OTP đã được gửi đến email',
        ]);
    }
}
