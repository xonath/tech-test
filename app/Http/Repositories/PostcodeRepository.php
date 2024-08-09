<?php

namespace App\Http\Repositories;

use App\Models\Postcode;

class PostcodeRepository
{
    protected string $storeType;

    public function getPostcode(string $postcode): Postcode
    {
        return Postcode::wherePostcode($postcode)
            ->firstOrFail();
    }
}
