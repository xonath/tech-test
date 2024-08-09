<?php

namespace App\Http\Controllers\Api;

use App\Exceptions\InvalidPostcodeException;
use App\Http\Controllers\Controller;
use App\Http\Services\PostcodeService;
use App\Http\Services\StoreService;
use App\Models\Postcode;
use Illuminate\Http\JsonResponse;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Response;

class SearchController extends Controller
{
    protected const RESULTS_PER_PAGE = 10;

    protected Postcode $postcodeModel;

    public function __construct(protected PostCodeService $postCodeService, protected StoreService $storeService)
    {
    }

    public function searchNearest(string $type, string $postcode, float $distance): JsonResponse
    {
        try {
            $postcodeModel = $this->getPostcodeModel($postcode);

            $response = $this->storeService
                ->searchNearest($type, $postcodeModel, $distance)
                ->orderby('distance')
                ->paginate(self::RESULTS_PER_PAGE);

            return $this->transformedStoreResponse($response);
        } catch (InvalidPostcodeException $e) {
            $this->logError(__METHOD__, $e->getMessage());

            return Response::json(['error' => $e->getMessage()], 400);
        }
    }

    public function searchDelivery(string $type, string $postcode): JsonResponse
    {
        try {
            $postcodeModel = $this->getPostcodeModel($postcode);

            $response = $this->storeService
                ->searchCanDeliverTo($type, $postcodeModel)
                ->orderby('distance')
                ->paginate(self::RESULTS_PER_PAGE);

            return $this->transformedStoreResponse($response);
        } catch (InvalidPostcodeException $e) {
            $this->logError(__METHOD__, $e->getMessage());

            return Response::json(['error' => $e->getMessage()], 400);
        }
    }

    /**
     * @throws InvalidPostcodeException
     */
    protected function getPostcodeModel(string $postcode): Postcode
    {
        return $this->postCodeService->getPostcodeModel($postcode);
    }

    protected function logError(string $method, string $errorMessage): void
    {
        Log::error('ERROR: ' . $method, [
            'error' => $errorMessage,
            'uri' => request()->fullUrl() . '?' .request()->query(),
        ]);
    }

    protected function transformedStoreResponse(LengthAwarePaginator $storesCollection): JsonResponse
    {
        $transformedStores = $storesCollection->map(function ($store) {
            return [
                'store_id' => $store->id,
                'name' => $store->name,
                'latitude' => $store->latitude,
                'longitude' => $store->longitude,
                'postcode' => $store->postcode->postcode,
                'is_open' => $store->is_open,
                'store_type' => $store->store_type,
                'max_delivery_distance' => $store->max_delivery_distance,
                'distance' => round($store->distance,2),
            ];
        });

        return response()->json(
            array_merge($storesCollection->toArray(), ['data' => $transformedStores])
        );
    }
}
