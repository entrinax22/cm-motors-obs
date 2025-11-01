<?php

namespace App\Http\Controllers;

use Exception;
use App\Models\User;
use Illuminate\Http\Request;
use App\Services\SemaphoreService;

class ContactUsController extends Controller
{
    protected $semaphore;

    public function __construct(SemaphoreService $semaphore){
        $this->semaphore = $semaphore;
    }

    public function contactUs(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'message' => 'required|string|max:1000',
        ]);

        try {
            $name = $request->name;
            $email = $request->email;
            $userMessage = $request->message;

            $admins = User::where('is_admin', 1)->pluck('phone');

            if ($admins->isEmpty()) {
                return response()->json([
                    'result' => false,
                    'message' => 'No admin numbers found to send SMS.'
                ]);
            }

         
            $smsContent = "New contact message from $name ($email): $userMessage";

            foreach ($admins as $mobile) {
                $this->semaphore->sendSMS($mobile, $smsContent);
            }

            return response()->json([
                'result' => true,
                'message' => 'Your message has been sent successfully!'
            ]);

        } catch (Exception $e) {
            return response()->json([
                'result' => false,
                'message' => "Error in sending the request: " . $e->getMessage()
            ]);
        }
    }
}
