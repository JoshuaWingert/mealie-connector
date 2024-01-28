<?php

namespace JW\Mealie\Request;

use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;

class Mealplan
{
    public function getMealPlan(array $params = []): Collection
    {
        if ($params === []) {
            $params = [
                'start_date' => Carbon::now()->subDays(config('mealie.start_date_offset'))->format('Y-m-d'),
                'end_date' => Carbon::now()->addDays(config('mealie.end_date_offset'))->format('Y-m-d'),
                'orderBy' => 'date',
                'orderDirection' => 'asc',
            ];
        }

        $connect = new Mealie;

        $response = $connect->connect()
            ->withQueryParameters($params)
            ->get($this->getMealPlanUrl())
            ->collect();
        
        return collect($response['items'])->groupBy('date');
    }

    public function getMealPlanUrl(): string
    {
        return config('mealie.host') . '/api/groups/mealplans';
    }
}