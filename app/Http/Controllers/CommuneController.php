<?php

namespace App\Http\Controllers;

use App\Models\Commune;
use Illuminate\Http\Request;
use App\Models\ActivityLog;
use Illuminate\Support\Facades\Validator;

class CommuneController extends Controller
{
    /**
     * @OA\Get(
     *     path="/api/v1/communes",
     *     tags={"Communes"},
     *     summary="Retrieve all communes",
     *     description="Get a list of all communes.",
     *     @OA\Response(
     *         response=200,
     *         description="A list of communes"
     *     )
     * )
     */
    public function index()
    {
        $communes = Commune::with(['region'])->paginate(15);
        return response()->json($communes);
    }

/**
 * @OA\Post(
 *     path="/api/v1/communes",
 *     tags={"Communes"},
 *     summary="Create a new commune",
 *     description="Create a new commune with id_reg, description and status.",
 *     @OA\Parameter(
 *         name="id_reg",
 *         in="query",
 *         required=true,
 *         @OA\Schema(type="integer"),
 *         description="Region ID"
 *     ),
 *     @OA\Parameter(
 *         name="description",
 *         in="query",
 *         required=true,
 *         @OA\Schema(type="string"),
 *         description="Commune description"
 *     ),
 *     @OA\Parameter(
 *         name="status",
 *         in="query",
 *         required=true,
 *         @OA\Schema(type="string", enum={"A", "I", "trash"}),
 *         description="Status of the commune"
 *     ),
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\JsonContent(
 *             required={"id_reg", "description", "status"},
 *             @OA\Property(property="id_reg", type="integer", example=1),
 *             @OA\Property(property="description", type="string", maxLength=90, example="New Commune Description"),
 *             @OA\Property(property="status", type="string", enum={"A", "I", "trash"}, example="A")
 *         )
 *     ),
 *     @OA\Response(
 *         response=201,
 *         description="Commune created successfully"
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
            'id_reg' => 'required|exists:regions,id',
            'description' => 'required|string|max:90',
            'status' => 'required|in:A,I,trash'
        ];

        $validator = Validator::make($request->input(), $validatedData);
        if ($validator->fails()) {
            $errors = implode(', ', $validator->errors()->all());
            ActivityLog::create([
                'user_id' => auth()->id(),
                'action' => 'register_communes_failed',
                'details' => 'Intento de registro fallido: ' . $errors,
                'ip_address' => $request->ip()
            ]);
            return response()->json([
                'status' => false,
                'errors' => $validator->errors()->all()
            ], 400);
        }

        $commune = new Commune($request->input());
        $commune->save();
        ActivityLog::create([
            'user_id' => auth()->id(),
            'action' => 'register_communes_successful',
            'details' => 'Registro exitoso: ' . $commune->description,
            'ip_address' => $request->ip()
        ]);

        return response()->json([
            'status' => true,
            'message' => 'Successfully register'
        ], 200);
    }

    /**
 * @OA\Put(
 *     path="/api/v1/communes/{id}",
 *     tags={"Communes"},
 *     summary="Update data commune",
 *     description="Update an existing commune's id_reg, description and status.",
 *     @OA\Parameter(
 *         name="id",
 *         in="path",
 *         required=true,
 *         @OA\Schema(type="integer"),
 *         description="Commune ID"
 *     ),
 *     @OA\Parameter(
 *         name="id_reg",
 *         in="query",
 *         required=true,
 *         @OA\Schema(type="integer"),
 *         description="Region ID"
 *     ),
 *     @OA\Parameter(
 *         name="description",
 *         in="query",
 *         required=true,
 *         @OA\Schema(type="string"),
 *         description="Commune description"
 *     ),
 *     @OA\Parameter(
 *         name="status",
 *         in="query",
 *         required=true,
 *         @OA\Schema(type="string", enum={"A", "I", "trash"}),
 *         description="Status of the commune"
 *     ),
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\JsonContent(
 *             required={"id_reg", "description", "status"},
 *             @OA\Property(property="id_reg", type="integer", example=1),
 *             @OA\Property(property="description", type="string", maxLength=90, example="Updated Commune Description"),
 *             @OA\Property(property="status", type="string", enum={"A", "I", "trash"}, example="I")
 *         )
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Commune updated successfully"
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
 *         description="Commune not found",
 *         @OA\JsonContent(
 *             @OA\Property(property="status", type="boolean", example=false),
 *             @OA\Property(property="message", type="string", example="Commune not found")
 *         )
 *     )
 * )
 */
    public function update(Request $request, Commune $commune)
    {
        $validatedData = [
            'region_id' => 'exists:regions,id',
            'description' => 'string|max:90',
            'status' => 'in:A,I,trash'
        ];

        $validator = Validator::make($request->input(), $validatedData);
        if ($validator->fails()) {
            $errors = implode(', ', $validator->errors()->all());
            ActivityLog::create([
                'user_id' => auth()->id(),
                'action' => 'updated_communes_failed',
                'details' => 'Intento de actualizacion fallido: ' . $errors,
                'ip_address' => $request->ip()
            ]);
            return response()->json([
                'status' => false,
                'errors' => $validator->errors()->all()
            ], 400);
        }

        $commune->update($request->input());
        ActivityLog::create([
            'user_id' => auth()->id(),
            'action' => 'updated_commune_successful',
            'details' => 'Municipio actualizado: ' . $commune->description,
            'ip_address' => $request->ip()
        ]);

        return response()->json([
            'status' => true,
            'message' => 'Successfully updated'
        ], 200);
    }


    /**
 * @OA\Delete(
 *     path="/api/v1/communes/{id}",
 *     tags={"Communes"},
 *     summary="Delete commune",
 *     description="Delete an existing commune by ID.",
 *     @OA\Parameter(
 *         name="id",
 *         in="path",
 *         required=true,
 *         @OA\Schema(type="integer"),
 *         description="Commune ID"
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Commune deleted successfully",
 *         @OA\JsonContent(
 *             @OA\Property(property="status", type="boolean", example=true),
 *             @OA\Property(property="message", type="string", example="Commune deleted successfully")
 *         )
 *     ),
 *     @OA\Response(
 *         response=404,
 *         description="Commune not found",
 *         @OA\JsonContent(
 *             @OA\Property(property="status", type="boolean", example=false),
 *             @OA\Property(property="message", type="string", example="Commune not found")
 *         )
 *     )
 * )
 */
    public function destroy($id)
    {
        $commune = Commune::find($id);
        ActivityLog::create([
            'user_id' => auth()->id(),
            'action' => 'deleted_regions_successful',
            'details' => 'Municipio eliminada: ' . $commune->description,
            'ip_address' => request()->ip()
        ]);
        $commune->delete();

        return response()->json([
            'status' => true,
            'message' => 'Successfully eliminated'
        ], 200);
    }
}
