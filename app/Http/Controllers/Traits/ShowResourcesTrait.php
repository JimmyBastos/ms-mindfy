<?php

namespace App\Http\Controllers\Traits;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Http\Resources\Json\ResourceCollection;

trait ShowResourcesTrait
{
    /**
     * List active resources
     *
     * @param  Request  $request
     * @param  mixed  $id
     *
     * @return JsonResource
     */
    public function show(Request $request, $id)
    {
        return $this->asResource(
            $this->service->find($id)
        );
    }
}
