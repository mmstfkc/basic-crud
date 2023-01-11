Projenin kurulumu için aşağıdaki komutu çalıştırmalısınız.

```bash
  composer require mmstfkc/basic-crud
```

(Opsiyonel)
Hata mesajlarının Paketteki şekilde gelmesi istenirse aşağıdaki aşamaları takip edebilirsiniz.

Aşağıdaki dosyaya gidip, komutları ekliyoruz. <br><br>
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

# Paketin Kullanımı

Paket kurulumunu tamamladıktan sonra yapmamız gereken işlemler sırası ile şunlardır.

## Controller

Bir Controller oluşturup bu controllara ModelController classını extend ediyoruz. 
<br><br> :warning:
ModelController Sizin kendi dizininizde bulunan "App\Http\Controllers\Controller" tarafından extend edilmektedir.
Orada yaptığınız değişiklikler paketinde etkilenmesine sebep olacaktır. :warning:
<br><br>Örnek Controller dosyanız aşağıdaki gibi olabilir.

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

Bu endpointlere istek atabilmemiz için aşağıdaki komutu route/api.php dosyasına ekleyebilirsiniz.
<br><br>Örnek api.pyp dosyanız aşağıdaki gibi olabilir.

```php
<?php

use App\Http\Controllers\User\UserController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::group(['prefix' => 'users'], function () {
    Route::get('', [UserController::class, 'modelIndex']);
    Route::get('{id}', [UserController::class, 'modelDetail']);
    Route::post('', [UserController::class, 'modelStore']);
    Route::put('{id}', [UserController::class, 'modelUpdate']);
    Route::delete('{id}', [UserController::class, 'modelDelete']);
});
```

Kendi index fonksiyonlarınızı yazmak isterseniz aşağıdaki gibi bir kullanım sağlayabilirsiniz.

### Index

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

