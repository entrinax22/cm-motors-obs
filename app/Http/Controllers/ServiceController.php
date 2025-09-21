<?php

namespace App\Http\Controllers;

use Inertia\Inertia;
use App\Models\Service;
use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\Storage;

class ServiceController extends Controller
{
    public function index()
    {
        return Inertia::render('admin/tables/servicesTable');
    }

    public function create()
    {
        return Inertia::render('admin/forms/ServiceCreateForm');
    }

    public function list(Request $request)
    {
        try{
            $search = $request->input('search');
            $services = Service::query();
            if ($search) {
                $services->where(function ($query) use ($search) {
                    $query->where('service_name', 'like', '%' . $search . '%')
                          ->orWhere('desciption', 'like', '%' . $search . '%')
                          ->orWhere('price', 'like', '%' . $search . '%');
                });
            }
            $services = $services->orderBy('service_id', 'desc')->paginate(10);
            $data = $services->map(function ($service) {
                return [
                    'service_id' => $service->service_id,
                    'service_name' => $service->service_name,
                    'description' => $service->description,
                    'price' => $service->price,
                    'duration_minutes' => $service->duration_minutes,
                    'is_active' => $service->is_active,
                    'image' => $service->image,
                ];
            });
            return response()->json([
                'result' => true,
                'data' => $data,
                'pagination' => [
                    'total' => $services->total(),
                    'per_page' => $services->perPage(),
                    'current_page' => $services->currentPage(),
                    'last_page' => $services->lastPage(),
                    'from' => $services->firstItem(),
                    'to' => $services->lastItem(),
                ],
            ]);
        }catch(\Exception $e){
            return response()->json([
                'result' => false,
                'message' => "Error in fetching list of services." . $e->getMessage()
            ]);
        }
    }

    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'service_name' => 'required|string|max:255',
                'description' => 'required|string',
                'price' => 'required|numeric|min:0',
                'duration_minutes' => 'required|integer|min:1',
                'is_active' => 'nullable|boolean',
                'image' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            ]);

            $imagePath = null;
            if ($request->hasFile('image')) {
                $imagePath = $request->file('image')->store('services', 'public');
            }

            $service = Service::create([
                'service_name' => $validated['service_name'],
                'description' => $validated['description'],
                'price' => $validated['price'],
                'duration_minutes' => $validated['duration_minutes'],
                'is_active' => $validated['is_active'] ?? true,
                'image' => $imagePath,
            ]);

            return response()->json([
                'result' => true,
                'message' => 'Service created successfully.',
                'data' => $service
            ], 201);

        } catch (\Exception $e) {
            return response()->json([
                'result' => false,
                'message' => "Error in storing the service. " . $e->getMessage()
            ], 500);
        }
    }

    public function edit($service_id)
    {
        try {
            $service = Service::where('service_id', $service_id)->firstOrFail();

            return response()->json([
                'result' => true,
                'message' => 'Service fetched successfully.',
                'data' => $service
            ], 200);

        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'result' => false,
                'message' => "Service not found."
            ], 404);
        } catch (\Exception $e) {
            return response()->json([
                'result' => false,
                'message' => "Error in fetching the service. " . $e->getMessage()
            ], 500);
        }
    }

    public function update(Request $request)
    {
        try {
            $validated = $request->validate([
                'service_id' => 'required|integer',
                'service_name' => 'required|string|max:255',
                'description' => 'required|string',
                'price' => 'required|numeric|min:0',
                'duration_minutes' => 'required|integer|min:1',
                'is_active' => 'required|in:0,1',
                'image' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            ]);

            $service = Service::where('service_id', $validated['service_id'])->firstOrFail();

            if ($request->hasFile('image')) {
                if ($service->image && Storage::disk('public')->exists($service->image)) {
                    Storage::disk('public')->delete($service->image);
                }

                $validated['image'] = $request->file('image')->store('services', 'public');
            }

            $service->update([
                'service_name' => $validated['service_name'],
                'description' => $validated['description'],
                'price' => $validated['price'],
                'duration_minutes' => $validated['duration_minutes'],
                'is_active' => $validated['is_active'],
                'image' => $validated['image'] ?? $service->image,
            ]);

            return response()->json([
                'result' => true,
                'message' => 'Service updated successfully.',
                'data' => $service,
            ], 200);

        } catch (ModelNotFoundException $e) {
            return response()->json([
                'result' => false,
                'message' => "Service not found."
            ], 404);

        } catch (\Exception $e) {
            return response()->json([
                'result' => false,
                'message' => "Error in updating the service. " . $e->getMessage()
            ], 500);
        }
    }

    public function destroy($service_id)
    {
        try {
            $service = Service::where('service_id', $service_id)->firstOrFail();

            $service->delete();

            return response()->json([
                'result' => true,
                'message' => 'Service deleted successfully.'
            ], 200);

        } catch (ModelNotFoundException $e) {
            return response()->json([
                'result' => false,
                'message' => 'Service not found.'
            ], 404);

        } catch (\Exception $e) {
            return response()->json([
                'result' => false,
                'message' => "Error in deleting the service. " . $e->getMessage()
            ], 500);
        }
    }

    public function selectList()
    {
        try {
            $users = Service::select('service_id', 'service_name', 'price')
                ->where('is_active', true)
                ->get();

            return response()->json([
                'result' => true,
                'data' => $users
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'result' => false,
                'message' => "Error in fetching list of services. " . $e->getMessage()
            ], 500);
        }
    }
}
