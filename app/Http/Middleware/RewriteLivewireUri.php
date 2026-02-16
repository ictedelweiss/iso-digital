<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RewriteLivewireUri
{
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        // Only process if response has content methods
        if (method_exists($response, 'getContent') && method_exists($response, 'setContent')) {
            $content = $response->getContent();

            // Only process if content exists and contains Livewire update attributes
            if ($content && str_contains($content, 'data-update-uri')) {
                // Ensure the prefix is correctly applied to the update URI
                // We target the attribute specifically with a regex to be whitespace-agnostic
                $prefix = '/laravel-app/public';

                // Replace any variant of /livewire/update that doesn't start with our prefix
                // and fix any double-prefixes or corrupted /http variants
                $newContent = preg_replace(
                    '/data-update-uri=(["\'])(?!' . preg_quote($prefix, '/') . ')\/?livewire\/update\1/',
                    'data-update-uri=$1' . $prefix . '/livewire/update$1',
                    $content
                );

                // Specifically catch and fix corrupted absolute URLs with leading slash
                $newContent = preg_replace(
                    '/data-update-uri=(["\'])\/http[^"]+\/livewire\/update\1/',
                    'data-update-uri=$1' . $prefix . '/livewire/update$1',
                    $newContent
                );

                $response->setContent($newContent);
            }
        }

        return $response;
    }
}