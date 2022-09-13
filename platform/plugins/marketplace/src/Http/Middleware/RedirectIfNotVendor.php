<?php

namespace Botble\Marketplace\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use MarketplaceHelper;

class RedirectIfNotVendor
{
    /**
     * Handle an incoming request.
     *
     * @param Request $request
     * @param Closure $next
     * @param string|null $guard
     * @return mixed
     */
    public function handle($request, Closure $next, $guard = 'customer')
    {
        if (!Auth::guard($guard)->check() || !Auth::guard($guard)->user()->is_vendor) {
            if ($request->ajax() || $request->wantsJson()) {
                return response('Unauthorized.', 401);
            }

            return redirect()->guest(route('customer.login'));
        }

        if (MarketplaceHelper::getSetting('verify_vendor', 1) &&
            !Auth::guard($guard)->user()->vendor_verified_at) {
            if ($request->ajax() || $request->wantsJson()) {
                return response('Vendor account is not verified.', 403);
            }

            return redirect()->guest(route('marketplace.vendor.become-vendor'));
        }

        return $next($request);
    }
}
