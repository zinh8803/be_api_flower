<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class NotificationController extends Controller
{
    /**
     * Display a listing of the notifications.
     *
     * @return \Illuminate\Http\Response
     */


    public function index()
    {
        $notifications = auth()->user()->notifications;

        return response()->json([
            'notifications' => $notifications,
            'unread_count' => auth()->user()->unreadNotifications->count(),
        ]);
    }

    /**
     * Mark a notification as read.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function markAsRead($id)
    {
        $notification = auth()->user()->notifications()->findOrFail($id);
        $notification->markAsRead();

        return response()->json(['message' => 'Đã đánh dấu đã đọc']);
    }

    
    public function delete($id)
    {
        $notification = auth()->user()->notifications()->findOrFail($id);
        $notification->delete();

        return response()->json(['message' => 'Đã xóa thông báo']);
    }

    /**
     * Delete all notifications.
     *
     * @return \Illuminate\Http\Response
     */
    public function deleteAll()
    {
        auth()->user()->notifications()->delete();

        return response()->json(['message' => 'Đã xóa tất cả thông báo']);
    }
}
