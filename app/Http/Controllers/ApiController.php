<?php

namespace App\Http\Controllers;

use Dingo\Api\Routing\Helpers;
use Illuminate\Database\Eloquent\Collection;

class ApiController extends Controller
{
    use Helpers;

    public function filterByCurrency($currency, $collection)
    {
        if (!is_array($collection) && !$collection instanceof Collection) {
            return collect();
        }

        if (is_array($collection)) {
            $collection = collect($collection);
        }

        return $collection->filter(function($value) use ($currency) {

            return in_array($currency, $value->currencies);
        });
    }
}
