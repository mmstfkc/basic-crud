<?php

namespace Mmstfkc\BasicCrud\app\Repositories;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

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
        $this->table = $this->model->getTable();
        $this->setQuery();
    }

    public function getQuery(): Builder
    {
        return $this->query;
    }

    /**
     * @return void
     */
    public function setQuery()
    {
        $this->query = $this->model->newQuery();
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
        $ilike = data_get($data, 'ilike', false);

        if ($where) {
            $this->filter(['where' => $where]);
        }

        if ($like) {
            if ($ilike) {
                $this->filter(['ilike' => $like], true);
            } else {
                $this->filter(['like' => $like], true);
            }
        }

        return $this->paginate($limit, $page);
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

    public function paginate(int $limit = 6, int $page = 1): LengthAwarePaginator
    {
        return $this->query->paginate($limit, ['*'], 'page', $page);
    }

    /**
     * @param array $filters
     * @param bool $isLike
     * @return $this
     */
    protected function filter(array $filters, bool $isLike = false): static
    {
        foreach ($filters as $filterKey => $filterData) {
            $method = $filterKey;

            foreach ($filterData as $filterDatum) {
                $operator = data_get($filterDatum, config('basicCrud.filter_operator_key'));
                unset($filterDatum[config('basicCrud.filter_operator_key')]);

                if ($isLike) {
                    $operator = $method;
                    $method = 'where';
                }

                foreach ($filterDatum as $key => $value) {
                    if (!is_null($operator)) {
                        $this->query->{$method}($this->table . '.' . $key, $operator, $value);
                    } else {
                        $this->query->{$method}($this->table . '.' . $key, $value);
                    }
                }
            }
        }

        return $this;
    }

    protected function fill(array $data): array
    {
        foreach (array_keys($data) as $field) {
            if (!$this->model->isFillable($field)) {
                unset($data[$field]);
            }
        }

        return $data;
    }
}
