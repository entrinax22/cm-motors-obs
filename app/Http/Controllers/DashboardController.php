<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Booking;
use App\Models\Service;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{

    public function index(Request $request)
    {
        try {
            $range = (int) $request->query('range', 1); // 1, 3, or 6
            
            if (!in_array($range, [1, 3, 6])) {
                $range = 1;
            }

            // Quick stats
            $totalBookings = Booking::where('status', 'completed')->count();
            $totalServices = Service::where('is_active', true)->count();
            $totalCustomers = User::where('is_admin', 0)->count();

            // Top 5 services
            $topServices = Booking::where('status', 'completed')
                ->select('service_id', DB::raw('COUNT(*) as total'))
                ->groupBy('service_id')
                ->orderByDesc('total')
                ->with('service')
                ->take(5)
                ->get()
                ->map(fn($b) => [
                    'name' => $b->service->service_name ?? 'N/A',
                    'count' => $b->total
                ]);

            // Date range
            $startDate = now()->subMonths($range - 1)->startOfMonth();
            $endDate = now()->endOfMonth();

            $labels = collect();
            $barData = collect();

            if ($range === 1) {
                // DAILY: list each day
                $current = $startDate->copy();
                while ($current <= $endDate) {
                    $labels->push($current->format('Y-m-d'));
                    $current->addDay();
                }

                $dailyData = Booking::where('status', 'completed')
                    ->whereBetween('scheduled_datetime', [$startDate, $endDate])
                    ->get()
                    ->groupBy(fn($b) => $b->scheduled_datetime->format('Y-m-d'))
                    ->map(fn($group) => $group->count());

                $barData = $labels->map(fn($d) => $dailyData[$d] ?? 0);
            } else {
                // MONTHLY: list month names
                $months = collect();
                $current = $startDate->copy();
                while ($current <= $endDate) {
                    $months->push($current->format('F Y')); // e.g., "December 2025"
                    $current->addMonth();
                }

                $monthlyData = Booking::where('status', 'completed')
                    ->whereBetween('scheduled_datetime', [$startDate, $endDate])
                    ->get()
                    ->groupBy(fn($b) => $b->scheduled_datetime->format('F Y'))
                    ->map(fn($group) => $group->count());

                $labels = $months;
                $barData = $months->map(fn($m) => $monthlyData[$m] ?? 0);
            }

            return response()->json([
                'result' => true,
                'range' => $range,
                'labels' => $labels,
                'data' => $barData,
                'totalBookings' => $totalBookings,
                'totalServices' => $totalServices,
                'totalCustomers' => $totalCustomers,
                'topServices' => $topServices,
                'pieChart' => [
                    'completed' => Booking::where('status', 'completed')->count(),
                    'pending'   => Booking::where('status', 'pending')->count(),
                    'canceled'  => Booking::where('status', 'canceled')->count()
                ],
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'result' => false,
                'message' => "Error: " . $e->getMessage()
            ]);
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
