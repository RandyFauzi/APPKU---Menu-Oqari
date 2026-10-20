<?php

namespace App\Http\Middleware;

use App\Services\SchemaCompatibility;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureFeatureSchemaIsReady
{
    public function __construct(private SchemaCompatibility $schema) {}

    public function handle(Request $request, Closure $next, string ...$features): Response
    {
        $this->schema->requireFeatures($features);

        return $next($request);
    }
}
