<?php

namespace Mmstfkc\BasicCrud\app\Repositories;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;

class BaseRepository
{
    /**
     * @var Model
     */
    protected Model $model;

    /**
     * @var Builder
     */
    protected Builder $query;

    /**
     * @var string
     */
    protected string $table;

    /**
     * @var string
     */
    protected string $singular;

    /**
     * @var array
     */
    protected array $errors;


    public function __construct(string $modelName)
    {
        $this->model = app($modelName);
    }

    /**
     * @param array $data
     * @return mixed
     */
    public function index(array $data): mixed
    {
        $page = data_get($data, 'page', 1);
        $limit = data_get($data, 'limit') ? data_get($data, 'limit') : config('basicCrud.max_limit');
        $orderBy = data_get($data, 'order_by');
        $where = data_get($data, 'where');
        $like = data_get($data, 'like');

        return $this->model->paginate($limit, ['*'], 'page', $page);
    }

    public function detail(int $id)
    {
        return $this->model->where('id', $id)->first();
    }

    public function store(array $data)
    {
        return $this->model->create($data);
    }

    public function update(array $data, int $id)
    {
        return $this->model->where('id', $id)->first()->update($data);
    }

    public function delete(int $id)
    {
        return $this->model->where('id', $id)->first()->delete();
    }
}
