<?php

namespace App\Core;

use Illuminate\Support\Facades\Response;
use Illuminate\Support\ServiceProvider;
use App\Core\Commands\MakeCollection;
use App\Core\Commands\MakeConcern;
use App\Core\Commands\MakeContract;
use App\Core\Commands\MakeCriteria;
use App\Core\Commands\MakeException;
use App\Core\Commands\MakeFilter;
use App\Core\Commands\MakeModel;
use App\Core\Commands\MakeRepository;
use App\Core\Commands\MakeRequest;
use App\Core\Commands\MakeService;
use App\Core\Commands\MakeTrait;
use App\Core\Commands\MakeEnum;
use App\Core\Commands\MakeHelper;

class CoreServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {
        if ($this->app->runningInConsole()) {
            $this->commands([
                MakeCollection::class,
                MakeConcern::class,
                MakeEnum::class,
                MakeContract::class,
                MakeCriteria::class,
                MakeException::class,
                MakeFilter::class,
                MakeHelper::class,
                MakeModel::class,
                MakeRepository::class,
                MakeRequest::class,
                MakeService::class,
                MakeTrait::class,
            ]);
        }

        $this->registerResponseMacros();
    }

    private function registerResponseMacros()
    {
        Response::macro('success', function ($data = null) {
            if (is_null($data)) {
                $data = [ 'success' => true ];
            }
            return response()->json($data, 200);
        });

        Response::macro('successWithoutData', function () {
            return response()->json([ 'success' => true ], 200);
        });

        Response::macro('error', function ($data, $statusCode = 400) {
            return response()->json($data, $statusCode);
        });
    }
}
