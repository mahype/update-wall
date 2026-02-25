<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\StoreReportRequest;
use App\Models\ApiRequestLog;
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
        $validated = $request->validated();

        $result = $this->ingestionService->ingest($validated, $apiToken);
        $report = $result['report'];

        $responseData = [
            'status' => 'ok',
            'report_id' => $report->id,
            'machine_id' => $report->machine_id,
        ];

        ApiRequestLog::create([
            'ip'       => $request->ip(),
            'status'   => 'success',
            'token_id' => $apiToken->id,
            'hostname' => $validated['hostname'],
            'detail'   => $result['is_new_machine'] ? 'new_machine' : 'existing_machine',
            'payload'  => $validated,
            'response' => $responseData,
        ]);

        return response()->json($responseData, 201);
    }
}
