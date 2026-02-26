<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use Illuminate\Http\Request;

class SettingsController extends Controller
{
    public function index()
    {
        $notificationsEnabled = Setting::get(Setting::NOTIFICATIONS_ENABLED, '1') === '1';
        $notifyStatuses       = Setting::getJson(Setting::NOTIFICATIONS_STATUSES, ['security', 'error', 'updates']);

        return view('admin.settings.index', compact('notificationsEnabled', 'notifyStatuses'));
    }

    public function update(Request $request)
    {
        $request->validate([
            'notify_statuses'   => 'nullable|array',
            'notify_statuses.*' => 'in:security,error,updates,stale',
        ]);

        Setting::set(Setting::NOTIFICATIONS_ENABLED, $request->has('notifications_enabled') ? '1' : '0');
        Setting::set(Setting::NOTIFICATIONS_STATUSES, $request->input('notify_statuses', []));

        return redirect()->route('admin.settings.index')
            ->with('success', 'Benachrichtigungseinstellungen gespeichert.');
    }
}
