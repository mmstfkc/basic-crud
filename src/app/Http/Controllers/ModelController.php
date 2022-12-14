<?php

namespace Mmstfkc\BasicCrud\app\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Contracts\Container\BindingResolutionException;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\JsonResponse;
use Mmstfkc\BasicCrud\app\Http\Requests\IndexRequest;
use Mmstfkc\BasicCrud\app\Http\Requests\StoreRequest;
use Mmstfkc\BasicCrud\app\Http\Requests\UpdateRequest;
use Mmstfkc\BasicCrud\app\Http\Resources\IndexResource;
use Mmstfkc\BasicCrud\app\Http\Resources\PaginateResource;

class ModelController extends Controller
{
    public string $modelName;
    protected Model $model;

    protected ?string $indexRequest;

    /**
     * @param string $modelName
     * @param string|null $indexRequest
     * @throws BindingResolutionException
     */
    public function __construct(string $modelName, string $indexRequest = null)
    {
        $this->modelName = $modelName;
        $this->model = app()->make($this->modelName);
        $this->indexRequest = $indexRequest;
    }

    /**
     * @param IndexRequest $request
     * @return JsonResponse
     */
    public function index(IndexRequest $request): JsonResponse
    {
        $requestData = $request->validated();

        $page = data_get($requestData, 'page', 1);
        $limit = data_get($requestData, 'limit') ? data_get($requestData, 'limit') : config('basicCrud.max_limit');
        $orderBy = data_get($requestData, 'order_by');
        $where = data_get($requestData, 'where');
        $like = data_get($requestData, 'like');

        $dbData = $this->model->paginate($limit, ['*'], 'page', $page);

        $dbData = new PaginateResource($dbData, IndexResource::class);

        return $this->sendSuccessResponse($dbData);
    }

    /**
     * @param int $id
     * @return JsonResponse
     */
    public function detail(int $id): JsonResponse
    {
        $data = $this->model->where('id', $id)->first();
        return $this->sendSuccessResponse(new IndexResource($data));
    }

    /**
     * @param StoreRequest $request
     * @return JsonResponse
     */
    public function store(StoreRequest $request): JsonResponse
    {
        if ($this->model->create($request->validated())) {
            return $this->sendSuccessResponse();
        }

        return $this->sendError();

    }

    public function update(UpdateRequest $request, int $id)
    {
        dd('update');
    }

    /**
     * @param $id
     * @return JsonResponse
     */
    public function delete($id): JsonResponse
    {
        $isDelete = $this->model->where('id', $id)->first()->delete();

        if ($isDelete) {
            return $this->sendSuccessResponse();
        }

        return $this->sendError();
    }

    public function sendSuccessResponse($data = null)
    {
        return response()->json([
            'status' => true,
            'code' => 1000,
            'message' => 'success',
            'data' => $data
        ]);
    }

    public function sendError($data = null)
    {
        return response()->json([
            'status' => false,
            'code' => 1001,
            'message' => 'error',
            'error' => $data
        ]);
    }

}
