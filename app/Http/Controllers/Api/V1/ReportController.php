<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\StoreReportRequest;
use App\Services\ReportIngestionService;
use Illuminate\Http\JsonResponse;

class ReportController extends Controller
{
    public function __construct(
        private ReportIngestionService $ingestionService
    ) {}

    public function store(StoreReportRequest $request): JsonResponse
    {
        $apiToken = $request->attributes->get('api_token');

        $report = $this->ingestionService->ingest(
            $request->validated(),
            $apiToken
        );

        return response()->json([
            'status' => 'ok',
            'report_id' => $report->id,
            'machine_id' => $report->machine_id,
        ], 201);
    }
}
