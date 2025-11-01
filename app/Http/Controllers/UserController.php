<?php

namespace App\Http\Controllers;

use App\Models\User;
use Inertia\Inertia;
use App\Models\Booking;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Storage;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class UserController extends Controller
{
    public function index()
    {
        return Inertia::render('admin/tables/usersTable');
    }

    public function create()
    {
        return Inertia::render('admin/forms/UserCreateForm');
    }

    public function list(Request $request)
    {
        try{
            $search = $request->input('search');
            $users = User::query();
            if ($search) {
                $users->where(function ($query) use ($search) {
                    $query->where('name', 'like', '%' . $search . '%')
                          ->orWhere('email', 'like', '%' . $search . '%');
                });
            }
            $users = $users->orderBy('id', 'desc')->paginate(10);
            $data = $users->map(function ($user) {
                return [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'phone' => $user->phone,
                    'address' => $user->address,
                    'city' => $user->city,
                    'zip' => $user->zip,
                    'country' => $user->country,
                    'profile_photo_url' => $user->profile_photo_url,
                    'is_admin' => $user->is_admin,
                    'created_at' => $user->created_at->format('Y-m-d H:i:s'),
                ];
            });
            return response()->json([
                'result' => true,
                'data' => $data,
                'pagination' => [
                    'total' => $users->total(),
                    'per_page' => $users->perPage(),
                    'current_page' => $users->currentPage(),
                    'last_page' => $users->lastPage(),
                    'from' => $users->firstItem(),
                    'to' => $users->lastItem(),
                ],
            ]);
        }catch(\Exception $e){
            return response()->json([
                'result' => false,
                'message' => "Error in fetching list of users." . $e->getMessage()
            ]);
        }
    }
    
    public function edit($id)
    {
        try {
            $user = User::where('id', $id)->firstOrFail();

            return response()->json([
                'result' => true,
                'message' => 'User fetched successfully.',
                'data' => $user
            ], 200);

        } catch (ModelNotFoundException $e) {
            return response()->json([
                'result' => false,
                'message' => "User not found."
            ], 404);
        } catch (\Exception $e) {
            return response()->json([
                'result' => false,
                'message' => "Error in fetching the user. " . $e->getMessage()
            ], 500);
        }
    }

    
    public function update(Request $request, $id)
    {
        try {
            $validated = $request->validate([
                'name' => 'required|string|max:255',
                'email' => 'required|email|max:255|unique:users,email,' . $id,
                'is_admin' => 'required|in:0,1',
            ]);

            $user = User::findOrFail($id);

            $user->update([
                'name' => $validated['name'],
                'email' => $validated['email'],
                'is_admin' => $validated['is_admin'],
            ]);

            return response()->json([
                'result' => true,
                'message' => 'User updated successfully.',
                'data' => $user,
            ], 200);

        } catch (ModelNotFoundException $e) {
            return response()->json([
                'result' => false,
                'message' => "User not found."
            ], 404);

        } catch (\Exception $e) {
            return response()->json([
                'result' => false,
                'message' => "Error in updating the user. " . $e->getMessage()
            ], 500);
        }
    }

    public function destroy($id)
    {
        try {
            $user = User::where('id', $id)->firstOrFail();

            $user->delete();

            return response()->json([
                'result' => true,
                'message' => 'User deleted successfully.'
            ], 200);

        } catch (ModelNotFoundException $e) {
            return response()->json([
                'result' => false,
                'message' => 'User not found.'
            ], 404);

        } catch (\Exception $e) {
            return response()->json([
                'result' => false,
                'message' => "Error in deleting the user. " . $e->getMessage()
            ], 500);
        }
    }

    public function selectList()
    {
        try {
            $users = User::select('id', 'name')->get();

            return response()->json([
                'result' => true,
                'data' => $users
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'result' => false,
                'message' => "Error in fetching list of users. " . $e->getMessage()
            ], 500);
        }
    }

    public function myBookings()
    {
        try {
            $user = Auth::user();

            if (!$user) {
                return response()->json([
                    'result' => false,
                    'message' => 'Unauthorized. Please login.'
                ], 401);
            }

            $bookings = Booking::where('user_id', $user->id)
                ->with('service')
                ->latest('scheduled_datetime')
                ->get();

            $data = $bookings->map(function ($booking) {
                return [
                    'booking_id' => $booking->booking_id,
                    'booking_number' => $booking->booking_number,
                    'user_id' => $booking->user_id,
                    'service_id' => $booking->service_id,
                    'scheduled_datetime' => $booking->scheduled_datetime,
                    'status' => $booking->status,
                    'notes' => $booking->notes,
                    'total_amount' => $booking->total_amount,
                    'created_at' => $booking->created_at,
                    'updated_at' => $booking->updated_at,
                    'service' => $booking->service ? [
                        'service_id' => $booking->service->service_id,
                        'service_name' => $booking->service->service_name,
                        'description' => $booking->service->description,
                        'price' => $booking->service->price,
                        'duration_minutes' => $booking->service->duration_minutes,
                        'is_active' => (bool)$booking->service->is_active,
                        'image' => $booking->service->image,
                        'created_at' => $booking->service->created_at,
                        'updated_at' => $booking->service->updated_at,
                    ] : null,
                ];
            });

            return response()->json([
                'result' => true,
                'message' => 'User bookings fetched successfully.',
                'data' => $data
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'result' => false,
                'message' => "Error in fetching booking history of the user. " . $e->getMessage()
            ], 500);
        }
    }

    public function cancelBooking(Request $request)
    {
        try {
            $validated = $request->validate([
                'booking_id' => 'required|integer',
            ]);


            $booking = Booking::where('booking_id', $validated['booking_id'])
                ->where('user_id', auth()->id())
                ->first();

            if (!$booking) {
                return response()->json([
                    'result' => false,
                    'message' => 'Booking not found or does not belong to this user.',
                ], 404);
            }

            if (in_array($booking->status, ['completed', 'cancelled', 'confirmed'])) {
                return response()->json([
                    'result' => false,
                    'message' => 'This booking cannot be cancelled.',
                ], 400);
            }

            $booking->status = 'cancelled';
            $booking->save();

            return response()->json([
                'result' => true,
                'message' => 'Booking cancelled successfully.',
                'data' => $booking,
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'result' => false,
                'message' => 'Error in cancelling the booking of the user. ' . $e->getMessage(),
            ], 500);
        }
    }
    
    public function myProfile()
    {
        try {
            $user = Auth::user();

            // Determine correct photo URL
            $photoUrl = $user->profile_photo_url;

            if ($photoUrl) {
                // Only add asset() if it’s not already a full URL
                if (!str_starts_with($photoUrl, 'http')) {
                    $photoUrl = asset('storage/' . $photoUrl);
                } else {
                    $photoUrl = $photoUrl; // already a full URL
                }
            } else {
                // Default image if no photo is uploaded
                $photoUrl = asset('images/default-profile.png');
            }

            return response()->json([
                'result' => true,
                'data' => [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'phone' => $user->phone,
                    'address' => $user->address,
                    'city' => $user->city,
                    'zip' => $user->zip,
                    'country' => $user->country,
                    'profile_photo_url' => $photoUrl,
                ],
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'result' => false,
                'message' => 'Error in fetching the user details. ' . $e->getMessage(),
            ], 500);
        }
    }


    public function updateProfile(Request $request)
    {
        try {
            $user = Auth::user();

            if ($request->hasFile('profile_photo')) {
                $file = $request->file('profile_photo');
                $filename = time() . '.' . $file->getClientOriginalExtension();
                $file->storeAs('profile_photos', $filename, 'public');
                $user->profile_photo_url = 'profile_photos/' . $filename;
            }

            $user->name = $request->name ?? $user->name;
            $user->phone = $request->phone ?? $user->phone;
            $user->address = $request->address ?? $user->address;
            $user->save();

            return response()->json([
                'result' => true,
                'message' => 'Profile updated successfully.',
                'data' => [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'profile_photo_url' => asset('storage/' . $user->profile_photo_url),
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'result' => false,
                'message' => 'Error updating profile: ' . $e->getMessage(),
            ]);
        }
    }

    public function sendPasswordOtp(Request $request)
    {
        $user = auth()->user();
        $otp = rand(100000, 999999);

        // Store OTP temporarily (for 5 minutes)
        Session::put('password_otp', $otp);
        Session::put('password_otp_expires_at', now()->addMinutes(5));

        // Send OTP using Semaphore
        $response = Http::post('https://api.semaphore.co/api/v4/messages', [
            'apikey' => env('SEMAPHORE_API_KEY'),
            'number' => $user->phone,
            'message' => "Your password reset OTP is: $otp. It will expire in 5 minutes.",
            'sendername' => env('SEMAPHORE_SENDER_NAME', 'JMCMotors'),
        ]);

        return response()->json([
            'result' => true,
            'message' => 'OTP sent successfully!',
            'api_response' => $response->json(),
        ]);
    }

    public function updatePassword(Request $request)
    {
        $request->validate([
            'otp' => 'required|numeric',
            'password' => 'required|string|min:8|confirmed',
        ]);

        $storedOtp = Session::get('password_otp');
        $expiresAt = Session::get('password_otp_expires_at');

        if (!$storedOtp || now()->greaterThan($expiresAt)) {
            return response()->json(['result' => false, 'message' => 'OTP expired. Please request a new one.']);
        }

        if ($request->otp != $storedOtp) {
            return response()->json(['result' => false, 'message' => 'Invalid OTP.']);
        }

        // Update password
        $user = auth()->user();
        $user->password = Hash::make($request->password);
        $user->save();

        // Clear OTP session
        Session::forget(['password_otp', 'password_otp_expires_at']);

        return response()->json(['result' => true, 'message' => 'Password updated successfully.']);
    }

}
