<?php

namespace App\Http\Middleware;

use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken as Middleware;

class VerifyCsrfToken extends Middleware
{
    /**
     * The URIs that should be excluded from CSRF verification.
     *
     * @var array<int, string>
     */
    protected $except = [
        '/client/get_list',
        '/product/get_list',
        '/order/get_list',
        '/delivery/get_list',
        '/helpdesk/get_list',
        '/offer/get_list'
    ];
}
