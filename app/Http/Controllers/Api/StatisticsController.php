<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use App\Http\Resources\StatisticsResource;
use App\Models\Statistics;
use Illuminate\Http\Request;

class StatisticsController extends Controller
{ 
     //
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function getStatistics(Request $request){
        $statistics = Statistics::filter()->get();
        return response(['data' => StatisticsResource::collection($statistics), 'message' => 'Retrieved successfully'], 200);
    }
}