<?php

namespace App\Http\Services;

use App\Exceptions\InvalidPostcodeException;
use App\Http\Repositories\PostcodeRepository;
use App\Models\Postcode;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class PostcodeService
{
    public function __construct(protected PostcodeRepository $postcodeRepository)
    {}

    public function getPostcodeModel(string $postcode): Postcode
    {
        try {
            return $this->postcodeRepository->getPostcode($postcode);
        } catch (ModelNotFoundException $e) {
            throw new InvalidPostcodeException('Invalid postcode entered');
        }
    }
}
