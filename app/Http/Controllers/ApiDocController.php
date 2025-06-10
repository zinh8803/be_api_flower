<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

/**
 * @OA\Info(
 *     title="Flower Shop API",
 *     version="1.0.0",
 *     description="API documentation for the Flower Shop system"
 * )
 * 
 * @OA\Server(
 *     url=L5_SWAGGER_CONST_HOST,
 *     description="Main API server"
 * )
 *
*
 * @OA\SecurityScheme(
 *     type="http",
 *     description="Nhập token JWT vào ô Authorization",
 *     name="Authorization",
 *     in="header",
 *     scheme="bearer",
 *     bearerFormat="JWT",
 *     securityScheme="bearerAuth"
 * )
 */

class ApiDocController extends Controller
{
    //
}
