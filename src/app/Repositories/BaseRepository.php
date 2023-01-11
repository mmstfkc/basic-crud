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


    /**
     * @param string $modelName
     */
    public function __construct(string $modelName)
    {
        $this->model = app($modelName);
        $this->table = $this->model->getTable();
        $this->setQuery();
    }

    /**
     * @return Builder
     */
    public function getQuery(): Builder
    {
        return $this->query;
    }

    /**
     * @return void
     */
    public function setQuery(): void
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

        $orderBy = data_get($data, 'order_by', [['id' => 'asc']]);

        $where = data_get($data, 'where');
        $whereIn = data_get($data, 'where_in');
        $whereNotIn = data_get($data, 'where_not_in');
        $like = data_get($data, 'like');
        $ilike = data_get($data, 'ilike');

        $this->filter(
            [
                'where' => $where,
                'whereIn' => $whereIn,
                'whereNotIn' => $whereNotIn,
                'like' => $like,
                'ilike' => $ilike,
            ]);

        $this->sort($orderBy);

        return $this->paginate($limit, $page);
    }

    /**
     * @param int $id
     * @return mixed
     */
    public function detail(int $id): mixed
    {
        return $this->model->where('id', $id)->first();
    }

    /**
     * @param array $data
     * @return mixed
     */
    public function store(array $data): mixed
    {
        return $this->model->create($data);
    }

    /**
     * @param array $data
     * @param int $id
     * @return mixed
     */
    public function update(array $data, int $id): mixed
    {
        return $this->model->where('id', $id)->first()->update($data);
    }

    /**
     * @param int $id
     * @return mixed
     */
    public function delete(int $id): mixed
    {
        return $this->model->where('id', $id)->first()->delete();
    }

    /**
     * @param int $limit
     * @param int $page
     * @return LengthAwarePaginator
     */
    public function paginate(int $limit = 6, int $page = 1): LengthAwarePaginator
    {
        return $this->query->paginate($limit, ['*'], 'page', $page);
    }

    /**
     * @param array $filters
     * @return $this
     */
    protected function filter(array $filters): static
    {
        foreach ($filters as $filterKey => $filterValues) {
            if ($filterValues) {
                foreach ($filterValues as $filterValue) {
                    foreach ($filterValue as $key => $value) {
                        if ($filterKey == 'like' || $filterKey == 'ilike') {
                            $this->query->where($this->table . '.' . $key, $filterKey, $value);
                        } else {
                            $this->query->{$filterKey}($this->table . '.' . $key, $value);
                        }
                    }
                }
            }
        }

        return $this;
    }

    /**
     * @param array $orderBy
     * @return $this
     */
    protected function sort(array $orderBy): static
    {
        foreach ($orderBy as $orderByItem) {
            foreach ($orderByItem as $key => $value) {
                $this->query->orderByRaw($key . ' ' . $value);
            }
        }
        return $this;
    }

    /**
     * @param array $data
     * @return array
     */
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
