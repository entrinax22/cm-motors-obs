<?php

namespace App\Http\Controllers;

use App\Models\User;
use Inertia\Inertia;
use Illuminate\Http\Request;
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
                    'profile_picture' => $user->profile_picture,
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

}
