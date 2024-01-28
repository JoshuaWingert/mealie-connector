<?php

namespace JW\Mealie\Request;

use Illuminate\Http\Client\PendingRequest;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Http;

class Mealie
{
    public function connect(): PendingRequest
    {
        
        $response = Http::withToken(config('mealie.api-key'));

        return $response;
    }

    
}