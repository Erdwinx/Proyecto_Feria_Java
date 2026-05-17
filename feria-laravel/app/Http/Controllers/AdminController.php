<?php

namespace App\Http\Controllers;

use App\Models\Ticket;
use App\Models\ScanLogEntry;
use App\Models\Customer;
use Carbon\Carbon;
use Illuminate\View\View;

class AdminController extends Controller
{
    public function dashboard(): View
    {
        $totalTickets = cache()->remember('admin_total_tickets', 300, fn () => Ticket::count());
        $totalScannedTickets = cache()->remember('admin_total_scanned_tickets', 300, fn () => Ticket::where('escaneado', true)->count());
        $totalCustomers = cache()->remember('admin_total_customers', 300, fn () => Customer::count());
        $totalScans = cache()->remember('admin_total_scans', 300, fn () => ScanLogEntry::count());

        $ticketsByType = cache()->remember('admin_tickets_by_type', 300, fn () => Ticket::selectRaw('tipo_evento, COUNT(*) as count')
            ->groupBy('tipo_evento')
            ->get());

        $recentScans = cache()->remember('admin_recent_scans', 120, fn () => ScanLogEntry::selectRaw('ticket_id, nombre, scanned_at_epoch_seconds')
            ->orderByDesc('scanned_at_epoch_seconds')
            ->limit(10)
            ->get());

        $availableTickets = $totalTickets - $totalScannedTickets;

        $scansByDate = cache()->remember('admin_scans_by_date', 300, function () {
            return ScanLogEntry::query()
                ->get(['scanned_at_epoch_seconds'])
                ->groupBy(function (ScanLogEntry $scan) {
                    $timestamp = (int) $scan->scanned_at_epoch_seconds;
                    return Carbon::createFromTimestamp($timestamp)->format('Y-m-d');
                })
                ->map(function ($group, string $scanDate) {
                    return (object) [
                        'scan_date' => $scanDate,
                        'count' => $group->count(),
                    ];
                })
                ->sortByDesc('count')
                ->take(7)
                ->values();
        });

        return view('admin', [
            'totalTickets' => $totalTickets,
            'totalScannedTickets' => $totalScannedTickets,
            'availableTickets' => $availableTickets,
            'totalCustomers' => $totalCustomers,
            'totalScans' => $totalScans,
            'ticketsByType' => $ticketsByType,
            'recentScans' => $recentScans,
            'scansByDate' => $scansByDate,
        ]);
    }

    /**
     * Panel view used for /boletos and /panel. Shows registro and revenue.
     */
    public function panel(): View
    {
        $totalTickets = cache()->remember('panel_total_tickets', 300, fn () => Ticket::count());
        $totalCustomers = cache()->remember('panel_total_customers', 300, fn () => Customer::count());

        // Tickets grouped by category to compute revenue using config prices
        $ticketsByCategory = cache()->remember('panel_tickets_by_category', 300, fn () => Ticket::selectRaw('category, COUNT(*) as count')
            ->groupBy('category')
            ->get()
        );

        $prices = config('prices.tickets', ['general' => 0, 'grada' => 0, 'vip' => 0]);

        $totalRevenue = 0;
        foreach ($ticketsByCategory as $row) {
            $cat = $row->category ?? 'general';
            $count = (int) $row->count;
            $price = $prices[$cat] ?? $prices['general'] ?? 0;
            $totalRevenue += $count * $price;
        }

        return view('boletos', [
            'totalTickets' => $totalTickets,
            'totalCustomers' => $totalCustomers,
            'totalRevenue' => $totalRevenue,
        ]);
    }
}
