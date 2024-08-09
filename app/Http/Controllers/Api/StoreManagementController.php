<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\AddStoreRequest;
use App\Http\Services\StoreService;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Response;

class StoreManagementController extends Controller
{
    public function __construct(protected StoreService $storeService)
    {}

    public function addStore(AddStoreRequest $request): JsonResponse
    {
        try {
            $store = $this->storeService->addStore($request);
            Log::info('STORE_ADDED', ['request' => $request, 'store' => $store]);

            return Response::json(['store_id' => $store['id']], 200);
        } catch (\Exception $e) {
            Log::error('STORE_ADDING_ERROR', ['error' => $e->getMessage()]);

            return Response::json(['error' => $e->getMessage()], 400);
        }
    }
}
