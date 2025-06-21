<?php

namespace App\Http\Controllers;

use App\Http\Requests\Size\StoreProductSizeRequest;
use App\Http\Resources\ProductSizeResource;
use App\Repositories\Contracts\ProductSizeRepositoryInterface;
use Illuminate\Http\Request;

class ProductSizeController extends Controller
{
    protected $repo;

    public function __construct(ProductSizeRepositoryInterface $repo)
    {
        $this->repo = $repo;
    }

    public function store(StoreProductSizeRequest $request)
    {
        $data = $request->validated();
        $productSize = $this->repo->create($data);
        return response()->json(['data' => $productSize], 201);
    }

    
    public function index()
    {
        return ProductSizeResource::collection($this->repo->all());
    }
}
