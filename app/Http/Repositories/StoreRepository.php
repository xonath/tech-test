<?php

namespace App\Http\Repositories;

use App\Models\Enums\StoreTypesEnum;
use App\Models\Postcode;
use App\Models\Store;
use Illuminate\Database\Eloquent\Builder;

class StoreRepository
{
    protected const RADIUS_MILES = 3959;

    public function addStore(array $storePayload): array
    {
        return Store::create($storePayload)
            ->toArray();
    }

    public function setStoreType(string $storeType): self
    {
        $this->storeType
            = in_array($storeType, array_column(StoreTypesEnum::cases(), 'value'))
            ? $storeType
            : 'all';

        return $this;
    }

    public function getNearByStores(Postcode $postcode, float $distance)
    {
        return $this->storeTypeFilter()
            ->with('postcode')
            ->withinRadius($postcode->latitude, $postcode->longitude, $distance, self::RADIUS_MILES);
    }

    public function getCanDeliverTo(Postcode $postcode)
    {
        // the group by is a requirement to use the having
        return $this->storeTypeFilter()
            ->with('postcode')
            ->groupBy('id')
            ->canDeliverTo($postcode->latitude, $postcode->longitude, self::RADIUS_MILES);
    }

    protected function storeTypeFilter(): Builder
    {
        return Store::when($this->storeType !== 'all', function (Builder $query) {
                $query->whereStoreType($this->storeType);
            });
    }
}
