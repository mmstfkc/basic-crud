You should run the following command for installation of the project.

```bash
  composer require mmstfkc/basic-crud
```

(Optional)
If you want the error messages to be displayed as in the package, you can follow the steps below.

Go to the file below and add the commands. <br><br>
app/Exceptions/Handler.php:56

```php

    protected $dontReport = [
        ...
        BasicCrudException::class
    ];

    public function render($request, Throwable $e): Response|JsonResponse
    {
        [$message, $statusCode, $code, $errors] = $this->getBasicCrudExceptionExceptionFields($e);

        if (App::environment() == 'production') {
            $errors = '';
        }

        return response()->json(
            [
                'status' => false,
                'code' => $code,
                'errors' => $errors,
                'message' => $message,
            ],
            $statusCode
        );
    }

    private function getBasicCrudExceptionExceptionFields(Throwable $exception): array
    {
        $errors = $exception->getMessage();
        $message = Str::snake(class_basename($exception));
        $statusCode = 500;
        $code = 1008;

        switch (get_class($exception)) {
            case BasicCrudException::class:
                $message = 'validation';
                $errors = $exception->getErrors();
                $statusCode = 422;
                $code = 1002;
                break;
        }

        return [
            $message,
            $statusCode,
            $code,
            $errors,
        ];
    }
```

# Usage of the Package

After completing the package installation, the necessary steps to follow are as follows:

## Controller

We create a controller and extend ModelController class to these controllers.

:warning:
ModelController is extended from "App\Http\Controllers\Controller" located in your own directory.
Any changes you make there will affect the package. :warning:

Example of your Controller file can be as follows.

```php
<?php

namespace App\Http\Controllers\User;

use App\Models\User;
use Mmstfkc\BasicCrud\app\Http\Controllers\ModelController;

class UserController extends ModelController
{
    public function __construct()
    {
        parent::__construct(User::class);
    }
}
```

## Route

To be able to make requests to these endpoints, you can add the following command to your route/api.php file.

Example api.php file can be as follows:

```php
<?php

use App\Http\Controllers\User\UserController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::group(['prefix' => 'users'], function () {
    Route::get('', [UserController::class, 'modelIndex']);
    Route::get('{id}', [UserController::class, 'modelDetail']);
    Route::post('', [UserController::class, 'modelStore']);
    Route::put('{id}', [UserController::class, 'modelUpdate']);
    Route::delete('{id}', [UserController::class, 'modelDelete']);
});
```

To write your own functions, you can use the following example usage:

```php
<?php

namespace App\Http\Controllers\User;

use App\Http\Requests\User\MyRequest;
use App\Models\User;

use Mmstfkc\BasicCrud\app\Http\Controllers\ModelController;

class UserController extends ModelController
{
    /**
     *
     */
    public function __construct()
    {
        parent::__construct(User::class);
    }

    /**
     * @param MyRequest $request
     * @return mixed
     */
    public function index(MyRequest $request): mixed
    {
        return $this->repository->index($request->validated());
    }

    /**
     * @param $id
     * @return mixed
     */
    public function detail($id): mixed
    {
        return $this->repository->detail($id);
    }

    /**
     * @param MyRequest $request
     * @return mixed
     */
    public function store(MyRequest $request): mixed
    {
        return $this->repository->store($request->validated());
    }

    /**
     * @param MyRequest $request
     * @param $id
     * @return mixed
     */
    public function update(MyRequest $request, $id): mixed
    {
        return $this->repository->update($request->validated(), $id);
    }

    /**
     * @param $id
     * @return mixed
     */
    public function delete($id): mixed
    {
        return $this->repository->delete($id);
    }
}
```

You can define your own functions inside your controller class that extends the ModelController.<br> 
After defining your function, you can access it using the appropriate HTTP method and route in your routes/api.php file.

This project is licensed under the MIT License. See the [LICENSE.md](LICENSE.md) file for details.
