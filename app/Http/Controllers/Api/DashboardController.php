<?php
namespace App\Http\Controllers\Api;
use App\Http\Controllers\Controller;
use App\Services\FinancialService;

class DashboardController extends Controller
{
    public function __construct(private FinancialService $finance) {}
    public function summary(){ return response()->json($this->finance->getTodaySummary()); }
    public function chart(){ return response()->json($this->finance->hourlyChart()); }
}
