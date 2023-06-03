<?php

namespace Mmstfkc\BasicCrud\app\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Mmstfkc\BasicCrud\app\Http\Requests\IndexRequest;
use Mmstfkc\BasicCrud\app\Http\Requests\StoreRequest;
use Mmstfkc\BasicCrud\app\Http\Requests\UpdateRequest;
use Mmstfkc\BasicCrud\app\Http\Resources\IndexResource;
use Mmstfkc\BasicCrud\app\Http\Resources\PaginateResource;
use Mmstfkc\BasicCrud\app\Repositories\BaseRepository;

class ModelController extends Controller
{
    protected BaseRepository $repository;
    public string $modelName;

    /**
     * @param string $modelName
     */
    public function __construct(string $modelName)
    {
        $this->modelName = $modelName;
        $this->repository = new BaseRepository($this->modelName);
    }

    /**
     * @param IndexRequest $request
     * @return JsonResponse
     */
    public function modelIndex(IndexRequest $request): JsonResponse
    {
        $dbData = new PaginateResource($this->repository->index($request->validated()), IndexResource::class);

        return $this->sendSuccessResponse($dbData);
    }

    /**
     * @param int $id
     * @return JsonResponse
     */
    public function modelDetail(int $id): JsonResponse
    {
        return $this->sendSuccessResponse(new IndexResource($this->repository->detail($id)));
    }

    /**
     * @param StoreRequest $request
     * @return JsonResponse
     */
    public function modelStore(StoreRequest $request): JsonResponse
    {
        if ($this->repository->store($request->validated())) {
            return $this->sendSuccessResponse();
        }

        return $this->sendError();

    }

    /**
     * @param UpdateRequest $request
     * @param int $id
     * @return JsonResponse
     */
    public function modelUpdate(UpdateRequest $request, int $id): JsonResponse
    {
        if ($this->repository->update($request->validated(), $id)) {
            return $this->sendSuccessResponse();
        }

        return $this->sendError();
    }

    /**
     * @param $id
     * @return JsonResponse
     */
    public function modelDelete($id): JsonResponse
    {
        if ($this->repository->delete($id)) {
            return $this->sendSuccessResponse();
        }

        return $this->sendError();
    }

    /**
     * @param $data
     * @return JsonResponse
     */
    public function sendSuccessResponse($data = null): JsonResponse
    {
        return response()->json([
            'status' => true,
            'code' => 1000,
            'message' => 'success',
            'data' => $data
        ]);
    }

    /**
     * @param $data
     * @return JsonResponse
     */
    public function sendError($data = null): JsonResponse
    {
        return response()->json([
            'status' => false,
            'code' => 1001,
            'message' => 'error',
            'error' => $data
        ]);
    }
}
