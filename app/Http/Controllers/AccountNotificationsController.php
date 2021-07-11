<?php

namespace App\Http\Controllers;

class AccountNotificationsController extends Controller
{
    /**
     * Display a listing of notifications
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        return view('account.notifications.index', [
            'user' => auth()->user(),
            'notifications' => $this->getNotifications(),
        ]);
    }

    /**
     * Get user paginated notifications with formatted date of creation
     *
     * @return LengthAwarePaginator
     */
    protected function getNotifications()
    {
        $notificationsPaginated = auth()->user()
            ->notificationsSinceLastWeek()
            ->paginate(2);

        $notificationsTransformed = $notificationsPaginated->getCollection()
            ->map(function ($notification, $key) {
                $notification['date_created'] = $notification->created_at->calendar();
                return $notification;
            })->toArray();

        return new \Illuminate\Pagination\LengthAwarePaginator(
            $notificationsTransformed,
            $notificationsPaginated->total(),
            $notificationsPaginated->perPage(),
            $notificationsPaginated->currentPage(), [
                'path' => request()->url(),
                'query' => [
                    'page' => $notificationsPaginated->currentPage(),
                ],
            ]
        );
    }
}