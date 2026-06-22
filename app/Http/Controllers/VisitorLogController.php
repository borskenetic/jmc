<?php

namespace App\Http\Controllers;

use App\Models\VisitorLog;
use Illuminate\Http\Request;

class VisitorLogController extends Controller
{
    public function index(Request $request)
    {
        $tz = config('app.timezone', 'Asia/Manila');
        $query = VisitorLog::with('visitor')
            ->when($request->from, fn ($q) => $q->whereDate('scanned_at', '>=', $request->from))
            ->when($request->to, fn ($q) => $q->whereDate('scanned_at', '<=', $request->to))
            ->when($request->status, fn ($q) => $q->where('status', strtoupper((string) $request->status)))
            ->when($request->search, function ($q) use ($request) {
                $search = $request->search;
                $q->whereHas('visitor', function ($vq) use ($search) {
                    $vq->where('firstname', 'like', "%{$search}%")
                        ->orWhere('lastname', 'like', "%{$search}%")
                        ->orWhere('organization', 'like', "%{$search}%")
                        ->orWhere('qrcode', 'like', "%{$search}%");
                });
            })
            ->orderByDesc('scanned_at')
            ->orderByDesc('id');

        $logs = (clone $query)->paginate(25)->withQueryString();

        $base = VisitorLog::query()
            ->when($request->from, fn ($q) => $q->whereDate('scanned_at', '>=', $request->from))
            ->when($request->to, fn ($q) => $q->whereDate('scanned_at', '<=', $request->to))
            ->when($request->status, fn ($q) => $q->where('status', strtoupper((string) $request->status)))
            ->when($request->search, function ($q) use ($request) {
                $search = $request->search;
                $q->whereHas('visitor', function ($vq) use ($search) {
                    $vq->where('firstname', 'like', "%{$search}%")
                        ->orWhere('lastname', 'like', "%{$search}%")
                        ->orWhere('organization', 'like', "%{$search}%")
                        ->orWhere('qrcode', 'like', "%{$search}%");
                });
            });

        $today = now($tz)->toDateString();

        $summary = [
            'total' => (clone $base)->count(),
            'in' => (clone $base)->where('status', 'IN')->count(),
            'out' => (clone $base)->where('status', 'OUT')->count(),
            'today' => (clone $base)->whereDate('scanned_at', $today)->count(),
        ];

        return view('visitor_logs.index', compact('logs', 'summary'));
    }
}
