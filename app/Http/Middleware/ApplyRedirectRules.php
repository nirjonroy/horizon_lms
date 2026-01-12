<?php

namespace App\Http\Middleware;

use App\Models\RedirectRule;
use Closure;
use Illuminate\Http\Request;

class ApplyRedirectRules
{
    public function handle(Request $request, Closure $next)
    {
        if (!in_array($request->method(), ['GET', 'HEAD'], true)) {
            return $next($request);
        }

        $pathInfo = $request->getPathInfo() ?: '/';
        $requestUri = $request->getRequestUri() ?: $pathInfo;

        $rules = RedirectRule::where('is_active', true)->get()->sortByDesc(function (RedirectRule $rule) {
            return collect($rule->sources ?? [])
                ->map(fn ($source) => strlen((string) ($source['pattern'] ?? '')))
                ->max() ?? 0;
        });

        foreach ($rules as $rule) {
            if (!($rule->matches($pathInfo) || $rule->matches($requestUri))) {
                continue;
            }

            $statusCode = $rule->statusCode();

            if (in_array($statusCode, [410, 451], true)) {
                abort($statusCode);
            }

            $destination = $this->resolveDestination($rule->destination_url);

            if (!$destination || $this->isSameDestination($request, $destination)) {
                continue;
            }

            return redirect($destination, $statusCode);
        }

        return $next($request);
    }

    protected function resolveDestination(?string $destination): ?string
    {
        $destination = trim((string) $destination);

        if ($destination === '') {
            return null;
        }

        if (filter_var($destination, FILTER_VALIDATE_URL)) {
            return $destination;
        }

        return '/' . ltrim($destination, '/');
    }

    protected function isSameDestination(Request $request, string $target): bool
    {
        $current = rtrim($request->fullUrl(), '/');

        $resolvedTarget = filter_var($target, FILTER_VALIDATE_URL)
            ? rtrim($target, '/')
            : rtrim(url($target), '/');

        return $current === $resolvedTarget;
    }
}

