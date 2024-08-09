<?php

namespace App\Models\Enums;

enum StoreTypesEnum: string
{
    case TAKEAWAY = 'takeaway';
    case SHOP = 'shop';
    case RESTAURANT = 'restaurant';
}
