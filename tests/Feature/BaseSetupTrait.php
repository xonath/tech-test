<?php

namespace Tests\Feature;

use App\Models\Postcode;
use App\Models\Store;
use Illuminate\Foundation\Testing\WithFaker;

trait BaseSetupTrait
{
    use WithFaker;

    protected const POSTCODES = [
        // postcode, latitude, longitude
        ['AB11QH',57.146432,-2.111087], ['AB11QJ',57.146369,-2.111649], ['AB11QL',57.146287,-2.112541],
        ['AB11QN',57.145765,-2.114026], ['AB11QP',57.145736,-2.115828], ['AB11QQ',57.147051,-2.111584],
        ['AB11QR',57.144937,-2.105414], ['AB11QS',57.145206,-2.105696], ['ZE10TX',60.138906,-1.27654],
        ['ZE10TY',60.154283,-1.151691], ['ZE10TZ',60.137058,-1.232122], ['ZE10UA',60.138394,-1.278091],
        ['ZE10UB',60.139427,-1.278176],
    ];

    Protected const STORES = [
        // name, postcode, max distance delivered, store type
        ['Tesco', 'AB11QN', 0.5, 'shop'],
        ['Spar', 'AB11QH', 2.4, 'shop'],
        ['Pizza Hut', 'ZE10TY', 500, 'takeaway'],
        ['Dominos', 'AB11QS', 6, 'takeaway'],
        ['Spar', 'ZE10TY', 5, 'shop'],
    ];

    public function setupBaseData(): void
    {
        // postcodes
        foreach (self::POSTCODES as $postcode) {
            Postcode::create([
                'postcode' => $postcode[0],
                'latitude' => $postcode[1],
                'longitude' => $postcode[2],
            ]);
        }

        $postcodes = Postcode::all();

        foreach(self::STORES as $store) {
            $postcode = $postcodes->where('postcode', '=', $store['1'])->first();

            Store::create([
                'name' => $store['0'],
                'latitude' => $postcode->latitude,
                'longitude' => $postcode->longitude,
                'is_open' => $this->faker->boolean,
                'store_type' => $store['3'],
                'max_delivery_distance' => $store['2'],
                'postcode_id' => $postcode->id,
            ]);
        }
    }
}
