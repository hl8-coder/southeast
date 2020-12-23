<?php

namespace App\Http\Controllers;

use App\Http\Requests\Request;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    public function removeThousandsNumber(&$request, $key)
    {
        $data = $request->all();
        if (isset($data['filter'][$key])) {
            $data['filter'][$key] = remove_thousands_number($data['filter'][$key]);
        }
        $request->replace($data);
    }

    public function paginate($request, $collection)
    {
        $perPage = $request->per_page ?? 20;
        $page = $request->page ?? 1;
        return  new LengthAwarePaginator($collection->forPage($page, $perPage), $collection->count(), $perPage, $page);
    }
}
