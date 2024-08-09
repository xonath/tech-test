<?php

namespace App\Http\Services;

use App\Exceptions\InvalidPostcodeException;
use App\Http\Repositories\PostcodeRepository;
use App\Http\Repositories\StoreRepository;
use App\Http\Requests\AddStoreRequest;
use App\Models\Postcode;
use App\Models\Store;
use Illuminate\Database\Eloquent\Builder;

class StoreService
{
    public function __construct(protected Store $store, protected StoreRepository $storeRepository, protected PostcodeRepository $postcodeRepository)
    {}

    public function AddStore(AddStoreRequest $request): array
    {
        $postcode = $this->postcodeRepository
            ->getPostcode($request->input('postcode'));

        return $this->storeRepository
            ->addStore($this->addStoreDto($request, $postcode));
    }

    /**
     * @throws InvalidPostcodeException
     */
    public function searchNearest(string $type, Postcode $postcodeModel, float $distance): Builder
    {
        return $this->storeRepository
            ->setStoreType($type)
            ->getNearByStores($postcodeModel, $distance);
    }

    /**
     * @throws InvalidPostcodeException
     */
    public function searchCanDeliverTo(string $type, Postcode $postcodeModel): Builder
    {
        return $this->storeRepository
            ->setStoreType($type)
            ->getCanDeliverTo($postcodeModel);
    }

    protected function addStoreDto(AddStoreRequest $request, Postcode $postcode): array
    {

        return [
            'name' => $request->input('name'),
            'latitude' => $postcode->latitude,
            'longitude' => $postcode->longitude,
            'postcode_id' => $postcode->id,
            'is_open' => $request->boolean('is_open'),
            'max_delivery_distance' => $request->input('max_delivery_distance'),
            'store_type' => $request->input('store_type'),
        ];
    }
}
