<?php

namespace App\Http\Controllers;

use Inertia\Inertia;
use App\Models\Booking;
use App\Models\Service;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class BookingController extends Controller
{
    public function index()
    {
        return Inertia::render('admin/tables/bookingsTable');
    }
    public function create()
    {
        return Inertia::render('admin/forms/BookingCreateForm');
    }
    public function list(Request $request)
    {
        try {
            $search = $request->input('search');

            $bookings = Booking::with(['user', 'service']);

            if ($search) {
                $bookings->where(function ($query) use ($search) {
                    $query->where('booking_number', 'like', '%' . $search . '%')
                        ->orWhere('status', 'like', '%' . $search . '%')
                        ->orWhereHas('service', function ($q) use ($search) {
                            $q->where('service_name', 'like', '%' . $search . '%');
                        })
                        ->orWhereHas('user', function ($q) use ($search) {
                            $q->where('name', 'like', '%' . $search . '%')
                                ->orWhere('email', 'like', '%' . $search . '%');
                        });
                });
            }

            $bookings = $bookings->orderBy('booking_id', 'desc')->paginate(10);

            $data = $bookings->map(function ($booking) {
                return [
                    'booking_id' => $booking->booking_id,
                    'booking_number' => $booking->booking_number,
                    'user' => $booking->user ? [
                        'id' => $booking->user->id,
                        'name' => $booking->user->name,
                        'email' => $booking->user->email,
                    ] : null,
                    'service' => $booking->service ? [
                        'service_id' => $booking->service->service_id,
                        'service_name' => $booking->service->service_name,
                    ] : null,
                    'scheduled_datetime' => $booking->scheduled_datetime
                        ? $booking->scheduled_datetime->format('Y-m-d H:i:s')
                        : null,
                    'status' => $booking->status,
                    'notes' => $booking->notes,
                    'total_amount' => $booking->total_amount,
                ];
            });

            return response()->json([
                'result' => true,
                'data' => $data,
                'pagination' => [
                    'total' => $bookings->total(),
                    'per_page' => $bookings->perPage(),
                    'current_page' => $bookings->currentPage(),
                    'last_page' => $bookings->lastPage(),
                    'from' => $bookings->firstItem(),
                    'to' => $bookings->lastItem(),
                ],
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'result' => false,
                'message' => "Error in fetching list of bookings. " . $e->getMessage()
            ], 500);
        }
    }

    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'booking_number' => 'required|numeric|unique:bookings,booking_number',
                'user_id' => 'required|exists:users,id',
                'service_id' => 'required|exists:services,service_id',
                'scheduled_datetime' => 'required|date',
                'status' => 'required|string|in:pending,confirmed,completed,cancelled',
                'notes' => 'nullable|string',
                'total_amount' => 'required|numeric|min:0',
            ]);

            $booking = Booking::create($validated);

            return response()->json([
                'result' => true,
                'message' => 'Booking created successfully.',
                'data' => $booking->load(['user', 'service']), 
            ], 201);

        } catch (\Exception $e) {
            return response()->json([
                'result' => false,
                'message' => "Error in storing the booking. " . $e->getMessage()
            ], 500);
        }
    }

    public function edit($booking_id)
    {
        try {
            $booking = Booking::with(['user', 'service']) 
                ->where('booking_id', $booking_id)
                ->firstOrFail();

            return response()->json([
                'result' => true,
                'message' => 'Booking fetched successfully.',
                'data' => $booking
            ], 200);

        } catch (ModelNotFoundException $e) {
            return response()->json([
                'result' => false,
                'message' => "Booking not found."
            ], 404);
        } catch (\Exception $e) {
            return response()->json([
                'result' => false,
                'message' => "Error in fetching the booking. " . $e->getMessage()
            ], 500);
        }
    }

    public function update(Request $request)
    {
        try {
            $validated = $request->validate([
                'booking_id' => 'required|integer',
                'booking_number' => 'required|string|unique:bookings,booking_number,' . $request->booking_id . ',booking_id',
                'user_id' => 'required|exists:users,id',
                'service_id' => 'required|exists:services,service_id',
                'scheduled_datetime' => 'required|date',
                'status' => 'required|string|in:pending,confirmed,completed,cancelled',
                'notes' => 'nullable|string',
                'total_amount' => 'required|numeric|min:0',
            ]);

            $booking = Booking::where('booking_id', $validated['booking_id'])->firstOrFail();

            $booking->update($validated);

            return response()->json([
                'result' => true,
                'message' => 'Booking updated successfully.',
                'data' => $booking->load(['user', 'service']),
            ], 200);

        } catch (ModelNotFoundException $e) {
            return response()->json([
                'result' => false,
                'message' => "Booking not found."
            ], 404);

        } catch (\Exception $e) {
            return response()->json([
                'result' => false,
                'message' => "Error in updating the booking. " . $e->getMessage()
            ], 500);
        }
    }

    public function destroy($booking_id)
    {
        try {
            $booking = Booking::where('booking_id', $booking_id)->firstOrFail();

            $booking->delete();

            return response()->json([
                'result' => true,
                'message' => 'Booking deleted successfully.'
            ], 200);

        } catch (ModelNotFoundException $e) {
            return response()->json([
                'result' => false,
                'message' => 'Booking not found.'
            ], 404);

        } catch (\Exception $e) {
            return response()->json([
                'result' => false,
                'message' => "Error in deleting the booking. " . $e->getMessage()
            ], 500);
        }
    }

    public function bookNow(Request $request)
    {
        try {
            $validated = $request->validate([
                'service_id' => 'required|exists:services,service_id',
                'scheduled_datetime' => 'required|date',
                'notes' => 'nullable|string',
            ]);

            $service = Service::where('service_id', $validated['service_id'])->firstOrFail();
            do {
                $bookingNumber = mt_rand(10000000, 99999999);
            } while (Booking::where('booking_number', $bookingNumber)->exists());

            $booking = Booking::create([
                'booking_number' => $bookingNumber,
                'user_id' => Auth::id(),
                'service_id' => $service->service_id,
                'scheduled_datetime' => $validated['scheduled_datetime'],
                'status' => 'pending',
                'notes' => $validated['notes'] ?? null,
                'total_amount' => $service->price, 
            ]);

            return response()->json([
                'result' => true,
                'message' => 'Booking created successfully!',
                'booking' => $booking
            ], 201);

        } catch (\Exception $e) {
            return response()->json([
                'result' => false,
                'message' => "Error in storing the booking. " . $e->getMessage()
            ], 500);
        }
    }
}
