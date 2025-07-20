<?php

namespace App\Http\Controllers;

use App\Http\Requests\Employee\StoreEmployeeRequest;
use App\Http\Requests\Employee\UpdateEmployeeRequest;
use App\Http\Resources\UserResource;
use App\Repositories\Contracts\UserRepositoryInterface;
use Illuminate\Http\Request;

class AdminController extends Controller
{

    protected $users;
    public function __construct(UserRepositoryInterface $users)
    {
        $this->users = $users;
    }

    /**
     * @OA\Post(
     *     path="/api/admin/create-employee",
     *     tags={"Admin"},
     *     summary="Create a new employee",
     *     operationId="createEmployee",
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(ref="#/components/schemas/StoreEmployeeRequest")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Employee created successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Tạo nhân viên thành công"),
     *         )
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Validation error",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="Validation error"),
     *             @OA\Property(property="errors", type="object", additionalProperties={"string"})
     *         )
     *     )
     * )
     */
    public function createEmployee(StoreEmployeeRequest $request)
    {
        $this->users->createEmployee($request->validated());
        return response()->json([
            'status' => true,
            'message' => 'Tạo nhân viên thành công',
        ]);
    }

    /**
     * @OA\Put(
     *     path="/api/admin/update-employee/{id}",
     *     tags={"Admin"},
     *     summary="Update an existing employee",
     *     operationId="updateEmployee",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(ref="#/components/schemas/UpdateEmployeeRequest")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Employee updated successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Cập nhật nhân viên thành công"),
     *         )
     *     ),
     * )
     */
    public function updateEmployee(UpdateEmployeeRequest $request, int $id)
    {
        $this->users->updateEmployee($id, $request->validated());
        return response()->json([
            'status' => true,
            'message' => 'Cập nhật nhân viên thành công',
        ]);
    }

    /**
     * @OA\Get(
     *     path="/api/admin/employees",
     *     tags={"Admin"},
     *     summary="Get all employees",
     *     operationId="getAllEmployees",
     *     @OA\Response(
     *         response=200,
     *         description="List of employees",
     *         @OA\JsonContent(
     *             type="array",
     *             @OA\Items(ref="#/components/schemas/User")
     *         )
     *     )
     * )
     */
    public function index()
    {
        $employees = $this->users->getAllEmployees();
        return UserResource::collection($employees);
    }
}
