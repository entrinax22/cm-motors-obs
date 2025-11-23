<?php

namespace App\Http\Controllers;

use Inertia\Inertia;
use App\Models\Booking;
use App\Models\Payment;
use Illuminate\Http\Request;

class PaymentsController extends Controller
{
    public function index()
    {
        return Inertia::render('admin/tables/paymentsTable');
    }
    public function create()
    {
        return Inertia::render('admin/forms/PaymentsForm');
    }

    public function list(Request $request)
    {
        try {
            $search = $request->input('search');

            $payments = Payment::with(['user', 'booking']);

            if ($search) {
                $payments->where(function ($query) use ($search) {
                    $query->where('payment_id', 'like', "%{$search}%")
                          ->orWhere('payment_method', 'like', "%{$search}%")
                          ->orWhereHas('booking', function ($q) use ($search) {
                              $q->where('booking_number', 'like', "%{$search}%");
                          })
                          ->orWhereHas('user', function ($q) use ($search) {
                              $q->where('name', 'like', "%{$search}%")
                                ->orWhere('email', 'like', "%{$search}%");
                          });
                });
            }

            $payments = $payments->orderBy('payment_id', 'desc')->paginate(10);

            $data = $payments->map(function ($payment) {
                return [
                    'payment_id' => $payment->payment_id,
                    'user_name' => $payment->user->name,
                    'booking_number' => $payment->booking->booking_number ?? 'N/A',
                    'amount' => $payment->amount,
                    'payment_proof' => $payment->payment_proof,
                    'reference_number' => $payment->reference_number,
                    'status' => $payment->status,
                    'created_at' => $payment->created_at->toDateTimeString(),
                ];
            });

            return response()->json([
                'result' => true,
                'data' => $data,
                'pagination' => [
                    'total' => $payments->total(),
                    'per_page' => $payments->perPage(),
                    'current_page' => $payments->currentPage(),
                    'last_page' => $payments->lastPage(),
                    'from' => $payments->firstItem(),
                    'to' => $payments->lastItem(),
                ],
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'result' => false,
                'message' => "Error in fetching list of payments. " . $e->getMessage()
            ], 500);
        }
    }

    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'booking_id' => 'required|numeric',
                'amount' => 'required|numeric|min:0',
                'reference_number' => 'required|string|max:255',
                'payment_proof' => 'required|image|mimes:jpeg,png,jpg,gif|max:20488', 
            ]);

            if ($request->hasFile('payment_proof')) {
                $file = $request->file('payment_proof');
                $path = $file->store('payments', 'public'); 
                $validated['payment_proof'] = $path;
            } else {
                $validated['payment_proof'] = 'N/A';
            }
            $user = Auth()->user();

            Payment::create([
                'user_id' => $user->id,
                'booking_id' => $validated['booking_id'],
                'amount' => $validated['amount'],
                'reference_number' => $validated['reference_number'],
                'payment_proof' => $validated['payment_proof'],
                'status' => 'pending',
            ]);

            Booking::where('booking_id', $validated['booking_id'])->update(['status' => 'confirmed']);

            return response()->json([
                'result' => true,
                'message' => 'Payment stored successfully!',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'result' => false,
                'message' => "Error in storing payment. " . $e->getMessage()
            ], 500);
        }
    }

    public function edit($payment_id)
    {
        try{
            $payment = Payment::with(['user', 'booking'])->findOrFail($payment_id);
            $data = [
                'payment_id' => $payment->payment_id,
                'user_id' => $payment->user->id,
                'booking_id' => $payment->booking->booking_id,
                'amount' => $payment->amount,
                'reference_number' => $payment->reference_number,
                'payment_proof' => $payment->payment_proof,
                'status' => $payment->status,
                'created_at' => $payment->created_at,
            ];
            return response()->json([
                'result' => true,
                'data' => $data,
                'message' => 'Payment data fetched successfully!',
            ]);
        }catch(\Exception $e){
            return response()->json([
                'result' => false,
                'message' => "Error in fetching payment data. " . $e->getMessage()
            ], 500);
        }
    }

    public function update(Request $request)
    {
        try {
            $validated = $request->validate([
                'payment_id' => 'required|numeric',
                'reference_number' => 'required|string|max:255',
                'amount' => 'required|numeric|min:0',
                'status' => 'required|string|in:pending,approved,rejected',
            ]);

            $payment = Payment::findOrFail($validated['payment_id']);
            $payment->reference_number = $validated['reference_number'];
            $payment->amount = $validated['amount'];
            $payment->status = $validated['status'];
            $payment->save();

            return response()->json([
                'result' => true,
                'message' => 'Payment updated successfully!',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'result' => false,
                'message' => "Error in updating payment. " . $e->getMessage()
            ], 500);
        }
    }

    public function destroy(Request $request)
    {
        try {
            $payment_id = $request->input('payment_id');
            $payment = Payment::findOrFail($payment_id);
            $payment->delete();

            return response()->json([
                'result' => true,
                'message' => 'Payment deleted successfully!',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'result' => false,
                'message' => "Error in deleting payment. " . $e->getMessage()
            ], 500);
        }
    }

}
