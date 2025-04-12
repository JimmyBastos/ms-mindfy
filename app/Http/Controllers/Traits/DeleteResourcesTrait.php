<?php

namespace App\Http\Controllers\Traits;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

trait DeleteResourcesTrait
{

    /**
     * Delete a resource by its identifier
     *
     * @param  Request  $request
     * @param $id
     *
     * @return JsonResponse
     */
    public function destroy(Request $request, $id = null)
    {
        $this->service->delete($id);

        return response()->json(null, 204);
    }

    /**
     * Restore a deleted resource by its identifier
     *
     * @param  Request  $request
     * @param $id
     *
     * @return JsonResponse
     */
    public function restore(Request $request, $id = null)
    {
        return $this->asResource(
            $this->service->restore($id)
        );
    }
}
