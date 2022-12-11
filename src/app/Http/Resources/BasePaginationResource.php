<?php

namespace Mmstfkc\BasicCrud\app\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class BasePaginationResource extends JsonResource
{
    protected string $indexResource;

    public function __construct($resource, string $indexResource)
    {
        parent::__construct($resource);
        $this->indexResource = $indexResource;
    }

    /**
     * Transform the resource into an array.
     *
     * @param  Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'items' => $this->indexResource::collection($this->resource)->jsonSerialize(),
            'pagination' => [
                'total' => $this->total(),
                'count' => $this->count(),
                'per_page' => $this->perPage(),
                'current_page' => $this->currentPage(),
                'total_page' => $this->lastPage(),
            ],
        ];
    }
}
