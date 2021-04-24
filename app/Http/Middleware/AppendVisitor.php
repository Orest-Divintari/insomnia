<?php

namespace App\Http\Middleware;

use App\helpers\Visitor;
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

        if (auth()->check()) {
            $response = $this->appendVisitor($response, Visitor::get());
        }

        return $response;
    }

    protected function appendVisitor($response, $visitor)
    {
        $content = $this->getContent($response);
        return $response->setContent(json_encode(
            array_merge(['data' => $content], ['visitor' => $visitor])
        ));
    }

    protected function getContent($response)
    {
        $content = $response->content();

        if ($this->isJson($content)) {
            return json_decode($content, true);
        }

        return $content;
    }

    protected function isJson($string)
    {
        json_decode($string);
        return json_last_error() == JSON_ERROR_NONE;
    }
}