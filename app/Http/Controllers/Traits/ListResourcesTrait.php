<?php

namespace App\Http\Controllers\Traits;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Http\Resources\Json\ResourceCollection;

trait ListResourcesTrait
{
    /**
     * List all resources, including deleted ones
     *
     * @param  Request  $request
     *
     * @return ResourceCollection
     *
     */
    public function indexAll(Request $request)
    {
        return $this->asCollection(
            $this->service->all()
        );
    }

    /**
     * List active resources
     *
     * @param  Request  $request
     *
     * @return ResourceCollection
     */
    public function index(Request $request)
    {
        return $this->asCollection(
            $this->service->paginate()
        );
    }
}
