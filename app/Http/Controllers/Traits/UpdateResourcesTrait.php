<?php

namespace App\Http\Controllers\Traits;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @template TService of BaseResourceService
 */
trait UpdateResourcesTrait
{
    /**
     * Update a resource by its identifier
     *
     * @param  Request  $request
     * @param $id
     *
     * @return JsonResource
     */
    public function update(Request $request, $id = null)
    {
        return $this->asResource(
            $this->service->update($id, $this->asData($request))
        );
    }
}
