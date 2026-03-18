<?php

namespace App\Http\Controllers;

use App\Models\Khoa;
use Illuminate\Http\Request;
use App\Http\Requests\CreateKhoaRequest;

class KhoaController extends Controller
{
    /**
     * Store a newly created Khoa in storage.
     *
     * @param  \App\Http\Requests\CreateKhoaRequest  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(CreateKhoaRequest $request)
    {
        $khoa = Khoa::create($request->validated());

        return response()->json([
            'status' => true,
            'data' => $khoa
        ], 201);
    }
}
