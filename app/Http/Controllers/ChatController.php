<?php

namespace App\Http\Controllers;

use App\Events\NewChatMessage;
use App\Models\ChatMessage;
use App\Models\User;
use Illuminate\Http\Request;

class ChatController extends Controller
{
    public function index(Request $request, int $partnerId)
    {
        $user       = auth()->user();
        $userId     = $user->id;
        $staffRoles = ['admin', 'employee'];
        $role       = strtolower($user->role ?? '');

        // dùng $role đã chuyển về chữ thường để so sánh
        if (in_array($role, $staffRoles, true)) {
            // admin/employee xem đoạn chat với 1 customer cụ thể ($partnerId)
            $customerId = $partnerId;

            $messages = ChatMessage::where(function ($q) use ($customerId) {
                // customer -> nhóm admin/employee
                $q->where('sender_id', $customerId)
                    ->where('to_staff_group', true);
            })
                ->orWhere(function ($q) use ($customerId, $staffRoles) {
                    // customer -> 1 staff cụ thể (nếu có)
                    $q->where('sender_id', $customerId)
                        ->where('to_staff_group', false)
                        ->whereHas('receiver', function ($q) use ($staffRoles) {
                            $q->whereIn('role', $staffRoles);
                        });
                })
                ->orWhere(function ($q) use ($customerId, $staffRoles) {
                    // bất kỳ staff -> customer
                    $q->where('receiver_id', $customerId)
                        ->whereHas('sender', function ($q) use ($staffRoles) {
                            $q->whereIn('role', $staffRoles);
                        });
                })
                ->orderBy('created_at', 'asc')
                ->get();
        } else {
            // user thường xem chat với "bộ phận quản trị" (admin + employee)
            $customerId = $userId;

            $messages = ChatMessage::where(function ($q) use ($customerId) {
                // user -> nhóm admin/employee
                $q->where('sender_id', $customerId)
                    ->where('to_staff_group', true);
            })
                ->orWhere(function ($q) use ($customerId, $staffRoles) {
                    // user -> 1 staff cụ thể (nếu có)
                    $q->where('sender_id', $customerId)
                        ->where('to_staff_group', false)
                        ->whereHas('receiver', function ($q) use ($staffRoles) {
                            $q->whereIn('role', $staffRoles);
                        });
                })
                ->orWhere(function ($q) use ($customerId, $staffRoles) {
                    // bất kỳ staff -> user
                    $q->where('receiver_id', $customerId)
                        ->whereHas('sender', function ($q) use ($staffRoles) {
                            $q->whereIn('role', $staffRoles);
                        });
                })
                ->orderBy('created_at', 'asc')
                ->get();
        }

        return response()->json($messages);
    }

    // Gửi tin nhắn mới
    public function store(Request $request)
    {
        $user       = auth()->user();
        $userId     = $user->id;
        $staffRoles = ['admin', 'employee'];
        $role       = strtolower($user->role ?? '');

        // Nếu là admin/employee: gửi đến 1 customer cụ thể (receiver_id)
        if (in_array($role, $staffRoles, true)) {
            $data = $request->validate([
                'receiver_id' => ['required', 'integer', 'exists:users,id'],
                'message'     => ['required', 'string'],
            ]);

            $receiverId    = $data['receiver_id'];
            $toStaffGroup  = false;
        } else {
            // user thường: gửi đến "nhóm quản trị" (tất cả admin + employee)
            $data = $request->validate([
                'message' => ['required', 'string'],
            ]);

            $receiverId   = null;
            $toStaffGroup = true;
        }

        $message = ChatMessage::create([
            'sender_id'      => $userId,
            'receiver_id'    => $receiverId,
            'message'        => $data['message'],
            'to_staff_group' => $toStaffGroup,
        ]);

        broadcast(new NewChatMessage($message))->toOthers();

        return response()->json($message, 201);
    }

    /**
     * Lịch sử tin nhắn của user gửi cho admin/employee (to_staff_group = true).
     * - Chỉ cho phép role admin, employee.
     * - Có thể truyền ?sender_id=ID để lọc theo 1 user cụ thể.
     */
    public function historySenderToAdmin(Request $request)
    {
        $user       = auth()->user();
        $staffRoles = ['admin', 'employee'];
        $role       = strtolower($user->role ?? '');

        if (!in_array($role, $staffRoles, true)) {
            return response()->json(['message' => 'Forbidden'], 403);
        }

        $data = $request->validate([
            'sender_id' => ['nullable', 'integer', 'exists:users,id'],
        ]);

        $messagesQuery = ChatMessage::with('sender')
            ->where('to_staff_group', true);

        if (!empty($data['sender_id'])) {
            $messagesQuery->where('sender_id', $data['sender_id']);
        }

        $messages = $messagesQuery
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json($messages);
    }
}
