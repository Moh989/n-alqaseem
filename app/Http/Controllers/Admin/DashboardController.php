<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use App\Models\ContactMessage;
use App\Models\Service;
use App\Services\Admin\PendingItems;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function __invoke(PendingItems $pending): View
    {
        return view('admin.dashboard', [
            'pending' => $pending->all(),
            'unreadCount' => ContactMessage::whereNull('read_at')->whereNull('archived_at')->count(),
            'recentMessages' => ContactMessage::whereNull('archived_at')->latest()->limit(5)->get(),
            'servicesCount' => Service::published()->count(),
            'activity' => AuditLog::with('user')->latest('created_at')->limit(8)->get(),
        ]);
    }
}
