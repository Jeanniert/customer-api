<?php

namespace App\Http\Controllers;

use App\Models\regions;
use App\Models\ActivityLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;


class RegionsController extends Controller
{

    /**
     * @OA\Get(
     *     path="/api/v1/regions",
     *     tags={"Regions"},
     *     summary="Retrieve all regions",
     *     description="Get a list of all regions.",
     *     @OA\Response(
     *         response=200,
     *         description="A list of regions",
     *    
     *     )
     * )
     */
    public function index()
    {
        $region = regions::paginate(15);
        return response()->json($region);
    }
/**
 * @OA\Post(
 *     path="/api/v1/regions",
 *     tags={"Regions"},
 *     summary="Create a new region",
 *     description="Create a new region with description and status.",
 *     @OA\Parameter(
 *         name="description",
 *         in="query",
 *         required=true,
 *         @OA\Schema(type="string", maxLength=90),
 *         description="Region description"
 *     ),
 *     @OA\Parameter(
 *         name="status",
 *         in="query",
 *         required=true,
 *         @OA\Schema(type="string", enum={"A", "I", "trash"}),
 *         description="Status of the region"
 *     ),
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\JsonContent(
 *             required={"description", "status"},
 *             @OA\Property(property="description", type="string", maxLength=90, example="New Region Description"),
 *             @OA\Property(property="status", type="string", enum={"A", "I", "trash"}, example="A")
 *         )
 *     ),
 *     @OA\Response(
 *         response=201,
 *         description="Region created successfully"
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
        $validatedData = [
            'description' => 'required|string|max:90',
            'status' => 'required|in:A,I,trash'
        ];

        $validator = Validator::make($request->input(), $validatedData);
        if ($validator->fails()) {
            $errors = implode(', ', $validator->errors()->all());
            ActivityLog::create([
                'user_id' => auth()->id(),
                'action' => 'register_regions_failed',
                'details' => 'Intento de registro fallido: ' . $errors,
                'ip_address' => $request->ip()
            ]);
            return response()->json([
                'status' => false,
                'errors' => $validator->errors()->all()
            ], 400);
        }

        $region = new regions($request->input());
        $region->save();
        ActivityLog::create([
            'user_id' => auth()->id(),
            'action' => 'register_regions_successful',
            'details' => 'Registro exitoso: ' . $region->description,
            'ip_address' => $request->ip()
        ]);

        return response()->json([
            'status' => true,
            'message' => 'Successfully register'
        ], 200);
    }


 /**
 * @OA\Put(
 *     path="/api/v1/regions/{id}",
 *     tags={"Regions"},
 *     summary="Update data region",
 *     description="Update an existing region's description and status.",
 *     @OA\Parameter(
 *         name="id",
 *         in="path",
 *         required=true,
 *         @OA\Schema(type="integer"),
 *         description="Region ID"
 *     ),
 *     @OA\Parameter(
 *         name="description",
 *         in="query",
 *         required=true,
 *         @OA\Schema(type="string", maxLength=90),
 *         description="Region description"
 *     ),
 *     @OA\Parameter(
 *         name="status",
 *         in="query",
 *         required=true,
 *         @OA\Schema(type="string", enum={"A", "I", "trash"}),
 *         description="Status of the region"
 *     ),
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\JsonContent(
 *             required={"description", "status"},
 *             @OA\Property(property="description", type="string", maxLength=90, example="Updated Region Description"),
 *             @OA\Property(property="status", type="string", enum={"A", "I", "trash"}, example="I")
 *         )
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Region updated successfully"
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
 *         description="Region not found",
 *         @OA\JsonContent(
 *             @OA\Property(property="status", type="boolean", example=false),
 *             @OA\Property(property="message", type="string", example="Region not found")
 *         )
 *     )
 * )
 */
    public function update(Request $request, regions $regions)
    {
        $validatedData = [
            'description' => 'string|max:90',
            'status' => 'in:A,I,trash'
        ];

        $validator = Validator::make($request->input(), $validatedData);

        if ($validator->fails()) {
            $errors = implode(', ', $validator->errors()->all());
            ActivityLog::create([
                'user_id' => auth()->id(),
                'action' => 'updated_regions_failed',
                'details' => 'Intento de actualizacion fallido: ' . $errors,
                'ip_address' => $request->ip()
            ]);
            return response()->json([
                'status' => false,
                'errors' => $validator->errors()->all()
            ], 400);
        }

        $regions->update($request->input());
        ActivityLog::create([
            'user_id' => auth()->id(),
            'action' => 'updated_regions_successful',
            'details' => 'Región actualizada: ' . $request->description,
            'ip_address' => $request->ip()
        ]);

        return response()->json([
            'status' => true,
            'message' => 'Successfully updated'
        ], 200);
    }

    /**
     * @OA\Delete(
     *     path="/api/v1/regions/{id}",
     *     tags={"Regions"},
     *     summary="Delete region",
     *     description="Delete an existing region by ID.",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer"),
     *         description="Region ID"
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Region deleted successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Region deleted successfully")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Region not found",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="Region not found")
     *         )
     *     )
     * )
     */
    public function destroy($id)
    {
        $regions = regions::find($id);
        ActivityLog::create([
            'user_id' => auth()->id(),
            'action' => 'deleted_regions_successful',
            'details' => 'Región eliminada: ' . $regions->description,
            'ip_address' => request()->ip()
        ]);
        $regions->delete();

        return response()->json([
            'status' => true,
            'message' => 'Successfully eliminated'
        ], 200);
    }
}
