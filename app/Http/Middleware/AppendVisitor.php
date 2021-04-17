<?php

namespace App\Http\Middleware;

use Closure;

class AppendVisitor
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $response = $next($request);

        $visitor = $this->getVisitor($request);

        $response = $this->appendVisitor($response, $visitor);

        return $response;
    }

    protected function appendVisitor($response, $visitor)
    {
        $content = $this->getContent($response);
        return $response->setContent(json_encode(
            array_merge(['data' => $content], $visitor)
        ));
    }

    protected function getContent($response)
    {
        return json_decode($response->content(), true);
    }

    protected function getVisitor($request)
    {
        $user = $request->user();
        $unreadConversations = $user->unreadConversations()->count();

        return ['visitor' => [
            'avatar_path' => $user->avatar_path,
            'unread_conversations' => $unreadConversations,
            'unviewed_notifications' => $this->unviewedNotifications($user),
        ]];
    }

    protected function unviewedNotifications($user)
    {
        return $user->notificationsViewed() ? 0 : $user->unviewedNotificationsCount;
    }
}