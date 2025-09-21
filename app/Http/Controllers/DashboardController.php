<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Booking;
use App\Models\Service;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{

    public function index()
    {
        try {
            // Quick stats
            $totalBookings = Booking::where('status', 'completed')->count();
            $totalServices = Service::where('is_active', true)->count();
            $totalCustomers = User::where('is_admin', 0)->count();

            // Top services (most booked, completed bookings only)
            $topServices = Booking::where('status', 'completed')
                ->select('service_id', DB::raw('COUNT(*) as total'))
                ->groupBy('service_id')
                ->orderByDesc('total')
                ->with('service')
                ->take(5) // top 5
                ->get()
                ->map(function ($b) {
                    return [
                        'name' => $b->service->service_name ?? 'N/A',
                        'count' => $b->total
                    ];
                });

            // Prepare last 6 months (backend ensures matching labels/data)
            $months = collect();
            $labels = collect();
            for ($i = 5; $i >= 0; $i--) {
                $month = now()->subMonths($i);
                $months->push($month->format('Y-m')); // for mapping bookings
                $labels->push($month->format('M'));   // chart labels
            }

            // Bookings grouped by month
            $bookingsData = Booking::where('status', 'completed')
                ->whereHas('service', fn($q) => $q->where('is_active', true))
                ->where('scheduled_datetime', '>=', now()->subMonths(5)->startOfMonth())
                ->get()
                ->groupBy(fn($b) => $b->scheduled_datetime->format('Y-m'))
                ->map(fn($monthBookings) => $monthBookings->count());

            // Fill missing months with 0
            $barData = $months->map(fn($m) => $bookingsData[$m] ?? 0);

            // Pie chart overview (by status)
            $pieChart = [
                'completed' => Booking::where('status', 'completed')->count(),
                'pending'   => Booking::where('status', 'pending')->count(),
                'canceled'  => Booking::where('status', 'canceled')->count()
            ];

            return response()->json([
                'result' => true,
                'totalBookings' => $totalBookings,
                'totalServices' => $totalServices,
                'totalCustomers' => $totalCustomers,
                'topServices' => $topServices,
                'barChart' => [
                    'labels' => $labels,
                    'data'   => $barData,
                ],
                'pieChart' => $pieChart
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'result' => false,
                'message' => "Error fetching dashboard data: " . $e->getMessage()
            ], 500);
        }
    }


    public function generateReport()
    {
        try {
            // Prepare last 6 months
            $months = collect();
            for ($i = 5; $i >= 0; $i--) {
                $months->push(now()->subMonths($i)->format('Y-m'));
            }

            // Get bookings for the last 6 months
            $bookings = Booking::with(['service', 'user'])
                ->where('scheduled_datetime', '>=', now()->subMonths(5)->startOfMonth())
                ->orderBy('scheduled_datetime', 'desc')
                ->get()
                ->map(function ($b) {
                    return [
                        'booking_id' => $b->booking_id,
                        'customer_name' => $b->user?->name ?? 'Unknown',
                        'customer_email' => $b->user?->email ?? 'N/A',
                        'service_name' => $b->service?->service_name ?? 'Unknown',
                        'status' => ucfirst($b->status),
                        'scheduled_datetime' => $b->scheduled_datetime->format('Y-m-d H:i'),
                        'total_amount' => $b->total_amount
                    ];
                });

            // Popular service (most booked completed bookings)
            $popularService = Booking::where('status', 'completed')
                ->select('service_id', DB::raw('COUNT(*) as total'))
                ->groupBy('service_id')
                ->orderByDesc('total')
                ->with('service')
                ->first();

            $popularServiceData = [
                'name' => $popularService?->service?->service_name ?? 'N/A',
                'count' => $popularService?->total ?? 0
            ];

            return response()->json([
                'result' => true,
                'bookings' => $bookings,
                'popularService' => $popularServiceData
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'result' => false,
                'message' => "Error generating report: " . $e->getMessage()
            ], 500);
        }
    }

}
