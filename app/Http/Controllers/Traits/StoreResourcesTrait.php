<?php

namespace App\Http\Controllers\Traits;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

trait StoreResourcesTrait
{
    /**
     * List active resources
     *
     * @param  Request  $request
     *
     * @return JsonResponse
     */
    public function store(Request $request)
    {
        $response = $this->asResource(
            $this->service->create($this->asData($request))
        );

        return response()->json($response, 201);
    }
}
