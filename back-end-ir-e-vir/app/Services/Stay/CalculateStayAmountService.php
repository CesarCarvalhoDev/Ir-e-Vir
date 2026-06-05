<?php

namespace App\Services\Stay;

use App\Models\Stay;
use Carbon\Carbon;

class CalculateStayAmountService
{
    public function execute(Stay $stay): float
    {
        $entry = Carbon::parse($stay->entry);

        $exit = now();

        $hours = ceil($entry->diffInMinutes($exit) / 60);

        return $hours * 10;
    }
}
