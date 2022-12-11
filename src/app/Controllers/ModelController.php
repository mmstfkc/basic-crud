<?php

namespace Mmstfkc\BasicCrud\app\Controllers;

use App\Http\Controllers\Controller;
use Mmstfkc\BasicCrud\App\Requests\IndexRequest;

class ModelController extends Controller
{
    public string $modelName;

    protected ?string $indexRequest;

    /**
     * @param string $modelName
     * @param string $indexRequest
     */
    public function __construct(string $modelName, string $indexRequest = null)
    {
        $this->modelName = $modelName;
        $this->indexRequest = $indexRequest;
    }


    public function index(IndexRequest $request)
    {
        dd($request);


        dd('index');
    }

    public function detail()
    {
        dd('detail');
    }

    public function store()
    {
        dd('store');
    }

    public function update()
    {
        dd('update');
    }

    public function delete()
    {
        dd('delete');
    }
}
