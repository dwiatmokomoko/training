<?php

namespace App\Http\Controllers;

class MasterDataController extends Controller
{
    public function __invoke()
    {
        return view('masters.index', [
            'resources' => MasterCrudController::resourceCards(),
        ]);
    }
}
