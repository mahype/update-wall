<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ApiRequestLog;

class LogController extends Controller
{
    public function index()
    {
        $logs = ApiRequestLog::with('token')
            ->latest()
            ->paginate(100);

        return view('admin.logs.index', compact('logs'));
    }

    public function show(ApiRequestLog $log)
    {
        $log->load('token');

        return view('admin.logs.show', compact('log'));
    }
}
