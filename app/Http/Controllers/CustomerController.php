<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Customer;
use App\Models\ActivityLog;
use App\Models\regions;
use App\Models\Commune;

class CustomerController extends Controller
{
    /**
 * @OA\Get(
 *     path="/api/v1/customers",
 *     tags={"Customers"},
 *     summary="Retrieve all customers",
 *     description="Get a list of all customers.",
 *     @OA\Response(
 *         response=200,
 *         description="A list of customers"
 *     )
 * )
 */
    public function index()
    {
        $customers = Customer::where('status', 'A')->with(['region', 'commune'])->get();
        $result = $customers->map(function ($customer) {
            return [
                'name' => $customer->name,
                'last_name' => $customer->last_name,
                'address' => $customer->address ?? null,
                'region_description' => $customer->region->description,
                'commune_description' => $customer->commune->description
            ];
        });
        return response()->json($result);
    }

    /**
 * @OA\Post(
 *     path="/api/v1/customers",
 *     tags={"Customers"},
 *     summary="Create a new customer",
 *     description="Create a new customer with the given details.",
 *     @OA\Parameter(
 *         name="dni",
 *         in="query",
 *         required=true,
 *         @OA\Schema(type="string", maxLength=45),
 *         description="Customer DNI"
 *     ),
 *     @OA\Parameter(
 *         name="id_reg",
 *         in="query",
 *         required=true,
 *         @OA\Schema(type="integer"),
 *         description="Region ID"
 *     ),
 *     @OA\Parameter(
 *         name="id_com",
 *         in="query",
 *         required=true,
 *         @OA\Schema(type="integer"),
 *         description="Commune ID"
 *     ),
 *     @OA\Parameter(
 *         name="email",
 *         in="query",
 *         required=true,
 *         @OA\Schema(type="string", maxLength=120),
 *         description="Customer email"
 *     ),
 *     @OA\Parameter(
 *         name="name",
 *         in="query",
 *         required=true,
 *         @OA\Schema(type="string", maxLength=45),
 *         description="Customer name"
 *     ),
 *     @OA\Parameter(
 *         name="last_name",
 *         in="query",
 *         required=true,
 *         @OA\Schema(type="string", maxLength=45),
 *         description="Customer last name"
 *     ),
 *     @OA\Parameter(
 *         name="address",
 *         in="query",
 *         required=false,
 *         @OA\Schema(type="string", maxLength=255),
 *         description="Customer address"
 *     ),
 *     @OA\Parameter(
 *         name="date_reg",
 *         in="query",
 *         required=true,
 *         @OA\Schema(type="string", format="date"),
 *         description="Registration date"
 *     ),
 *     @OA\Parameter(
 *         name="status",
 *         in="query",
 *         required=true,
 *         @OA\Schema(type="string", enum={"A", "I", "trash"}),
 *         description="Customer status"
 *     ),
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\JsonContent(
 *             required={"dni", "id_reg", "id_com", "email", "name", "last_name", "date_reg", "status"},
 *             @OA\Property(property="dni", type="string", maxLength=45, example="12345678"),
 *             @OA\Property(property="id_reg", type="integer", example=1),
 *             @OA\Property(property="id_com", type="integer", example=1),
 *             @OA\Property(property="email", type="string", maxLength=120, example="customer@example.com"),
 *             @OA\Property(property="name", type="string", maxLength=45, example="John"),
 *             @OA\Property(property="last_name", type="string", maxLength=45, example="Doe"),
 *             @OA\Property(property="address", type="string", maxLength=255, example="123 Main St"),
 *             @OA\Property(property="date_reg", type="string", format="date", example="2023-12-07"),
 *             @OA\Property(property="status", type="string", enum={"A", "I", "trash"}, example="A")
 *         )
 *     ),
 *     @OA\Response(
 *         response=201,
 *         description="Customer created successfully"
 *     ),
 *     @OA\Response(
 *         response=400,
 *         description="Validation errors",
 *         @OA\JsonContent(
 *             @OA\Property(property="status", type="boolean", example=false),
 *             @OA\Property(property="errors", type="array", @OA\Items(type="string"))
 *         )
 *     )
 * )
 */
    public function store(Request $request)
    {
        $validatedData = $request->all();

        // Verificar que la región y la comuna están relacionadas
        $region = regions::find($request->input('id_reg'));
        $commune = Commune::find($request->input('id_com'));
        if ($commune->id_reg != $region->id) {
            return response()->json([
                'status' => false,
                'errors' => ['La comuna y la región no están relacionadas.']
            ], 400);
        }

        $customer = Customer::create($validatedData);
        ActivityLog::create([
            'user_id' => auth()->id(),
            'action' => 'register_customer_successful',
            'details' => 'Registro exitoso: ' . $customer->name . ' ' . $customer->last_name,
            'ip_address' => $request->ip()
        ]);

        return response()->json([
            'status' => true,
            'message' => 'Successfully registered'
        ], 200);
    }

    /**
 * @OA\Put(
 *     path="/api/v1/customers/{id}",
 *     tags={"Customers"},
 *     summary="Update data customer",
 *     description="Update an existing customer's details.",
 *     @OA\Parameter(
 *         name="id",
 *         in="path",
 *         required=true,
 *         @OA\Schema(type="integer"),
 *         description="Customer ID"
 *     ),
 *     @OA\Parameter(
 *         name="dni",
 *         in="query",
 *         required=true,
 *         @OA\Schema(type="string", maxLength=45),
 *         description="Customer DNI"
 *     ),
 *     @OA\Parameter(
 *         name="id_reg",
 *         in="query",
 *         required=true,
 *         @OA\Schema(type="integer"),
 *         description="Region ID"
 *     ),
 *     @OA\Parameter(
 *         name="id_com",
 *         in="query",
 *         required=true,
 *         @OA\Schema(type="integer"),
 *         description="Commune ID"
 *     ),
 *     @OA\Parameter(
 *         name="email",
 *         in="query",
 *         required=true,
 *         @OA\Schema(type="string", maxLength=120),
 *         description="Customer email"
 *     ),
 *     @OA\Parameter(
 *         name="name",
 *         in="query",
 *         required=true,
 *         @OA\Schema(type="string", maxLength=45),
 *         description="Customer name"
 *     ),
 *     @OA\Parameter(
 *         name="last_name",
 *         in="query",
 *         required=true,
 *         @OA\Schema(type="string", maxLength=45),
 *         description="Customer last name"
 *     ),
 *     @OA\Parameter(
 *         name="address",
 *         in="query",
 *         required=false,
 *         @OA\Schema(type="string", maxLength=255),
 *         description="Customer address"
 *     ),
 *     @OA\Parameter(
 *         name="date_reg",
 *         in="query",
 *         required=true,
 *         @OA\Schema(type="string", format="date"),
 *         description="Registration date"
 *     ),
 *     @OA\Parameter(
 *         name="status",
 *         in="query",
 *         required=true,
 *         @OA\Schema(type="string", enum={"A", "I", "trash"}),
 *         description="Customer status"
 *     ),
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\JsonContent(
 *             required={"dni", "id_reg", "id_com", "email", "name", "last_name", "date_reg", "status"},
 *             @OA\Property(property="dni", type="string", maxLength=45, example="12345678"),
 *             @OA\Property(property="id_reg", type="integer", example=1),
 *             @OA\Property(property="id_com", type="integer", example=1),
 *             @OA\Property(property="email", type="string", maxLength=120, example="customer@example.com"),
 *             @OA\Property(property="name", type="string", maxLength=45, example="John"),
 *             @OA\Property(property="last_name", type="string", maxLength=45, example="Doe"),
 *             @OA\Property(property="address", type="string", maxLength=255, example="123 Main St"),
 *             @OA\Property(property="date_reg", type="string", format="date", example="2023-12-07"),
 *             @OA\Property(property="status", type="string", enum={"A", "I", "trash"}, example="A")
 *         )
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Customer updated successfully"
 *     ),
 *     @OA\Response(
 *         response=400,
 *         description="Validation errors",
 *         @OA\JsonContent(
 *             @OA\Property(property="status", type="boolean", example=false),
 *             @OA\Property(property="errors", type="array", @OA\Items(type="string"))
 *         )
 *     ),
 *     @OA\Response(
 *         response=404,
 *         description="Customer not found",
 *         @OA\JsonContent(
 *             @OA\Property(property="status", type="boolean", example=false),
 *             @OA\Property(property="message", type="string", example="Customer not found")
 *         )
 *     )
 * )
 */
    public function update(Request $request, $id)
    {
        $validatedData = $request->all();

        if ($request->has('id_reg') && $request->has('id_com')) {
            $region = regions::find($request->input('id_reg'));
            $commune = Commune::find($request->input('id_com'));
            if ($commune->id_reg != $region->id) {
                return response()->json([
                    'status' => false,
                    'errors' => ['La comuna y la región no están relacionadas.']
                ], 400);
            }
        }

        $customer = Customer::findOrFail($id);
        $customer->update($validatedData);

        ActivityLog::create([
            'user_id' => auth()->id(),
            'action' => 'update_customer_successful',
            'details' => 'Actualización exitosa: ' . $customer->name . ' ' . $customer->last_name,
            'ip_address' => $request->ip()
        ]);

        return response()->json([
            'status' => true,
            'message' => 'Successfully updated'
        ], 200);
    }

    /**
 * @OA\Delete(
 *     path="/api/v1/customers/{id}",
 *     tags={"Customers"},
 *     summary="Delete customer",
 *     description="Delete an existing customer by ID.",
 *     @OA\Parameter(
 *         name="id",
 *         in="path",
 *         required=true,
 *         @OA\Schema(type="integer"),
 *         description="Customer ID"
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Customer deleted successfully",
 *         @OA\JsonContent(
 *             @OA\Property(property="status", type="boolean", example=true),
 *             @OA\Property(property="message", type="string", example="Customer deleted successfully")
 *         )
 *     ),
 *     @OA\Response(
 *         response=404,
 *         description="Customer not found",
 *         @OA\JsonContent(
 *             @OA\Property(property="status", type="boolean", example=false),
 *             @OA\Property(property="message", type="string", example="Customer not found")
 *         )
 *     )
 * )
 */
    public function destroy( $id)
    {
        $customer = Customer::findOrFail($id);

        if ($customer->status == 'trash') {
            return response()->json([
                'status' => false,
                'errors' => ['Registro no existe.']
            ], 400);
        }

        $customer->update(['status' => 'trash']);
        ActivityLog::create([
            'user_id' => auth()->id(),
            'action' => 'delete_customer_successful',
            'details' => 'Registro eliminado: ' . $customer->name . ' ' . $customer->last_name,
            'ip_address' => request()->ip()
        ]);

        return response()->json([
            'status' => true,
            'message' => 'Successfully deleted'
        ], 200);
    }
}
