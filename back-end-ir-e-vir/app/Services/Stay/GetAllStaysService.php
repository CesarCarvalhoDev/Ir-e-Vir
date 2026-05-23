<?php

namespace App\Services\Stay;

use App\Models\Stay;

class GetAllStaysService
{
    public function execute()
    {
        $stays = Stay::all();
        return $stays;
    }
}