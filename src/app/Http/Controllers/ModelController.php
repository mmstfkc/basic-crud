<?php

namespace Mmstfkc\BasicCrud\app\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Database\Eloquent\Model;
use Mmstfkc\BasicCrud\app\Http\Requests\IndexRequest;
use Mmstfkc\BasicCrud\app\Http\Resources\IndexResource;
use Mmstfkc\BasicCrud\app\Http\Resources\PaginateResource;

class ModelController extends Controller
{
    public string $modelName;
    protected Model $model;

    protected ?string $indexRequest;

    /**
     * @param string $modelName
     * @param string $indexRequest
     */
    public function __construct(string $modelName, string $indexRequest = null)
    {
        $this->modelName = $modelName;
        $this->model = app()->make($this->modelName);
        $this->indexRequest = $indexRequest;
    }


    public function index(IndexRequest $request)
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
     * @return mixed
     */
    public function detail(int $id)
    {
        $data = $this->model->where('id', $id)->first();
        return $this->sendSuccessResponse(new IndexResource($data));
    }

    /**
     * @return void
     */
    public function store()
    {
        dd('store');
    }

    public function update()
    {
        dd('update');
    }

    public function delete($id)
    {
        dd('delete');
    }
}
