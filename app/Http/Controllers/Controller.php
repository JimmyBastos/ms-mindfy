<?php

namespace App\Http\Controllers;

use App\Data\BaseData;
use App\Http\Resources\BaseResource;
use App\Models\Base\BaseModel;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Support\Collection;

/**
 * @template TModel of BaseModel
 * @template TResource \App\Http\Resources\BaseResource
 * @template TData of BaseData
 */
class Controller extends BaseController
{
    use AuthorizesRequests, ValidatesRequests;

    /**
     * @var class-string<TResource>
     */
    protected string $resource = BaseResource::class;

    /**
     * @var class-string<TData>
     */
    protected string $data = BaseData::class;

    /**
     * @param  Request  $request
     *
     * @return TData
     */
    protected function asData(Request $request)
    {
        return $this->data::from($request);
    }

    /**
     * @param  TModel  $model
     *
     * @return TResource
     */
    protected function asResource($model)
    {
        return $this->resource::make($model);
    }

    /**
     * @param  Collection | LengthAwarePaginator  $items
     *
     * @return AnonymousResourceCollection
     */
    protected function asCollection($items)
    {
        return $this->resource::collection($items);
    }
}
