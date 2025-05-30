<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Contracts\Auth\Guard;
use Illuminate\Http\RedirectResponse;

class Access
{
    protected $auth;

    public function __construct(Guard $auth)
    {
        $this->auth = $auth;
    }

    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  \Closure $next
     * @param  string $role
     * @return mixed
     */
    public function handle($request, Closure $next, $role)
    {
        if($this->auth->check()) {
            switch($role){
                case 'admin':
                    if($request->user()->admin != 1){
                        if($request->ajax())
                            return new RedirectResponse(url('/'));
                        abort(404);
                    }
                break;

                default:
                    return new RedirectResponse(url('/'));
                    break;
            }
            return $next($request);
        }
        return new RedirectResponse(url('/'));
    }
}
