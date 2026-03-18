<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class UploadController extends Controller
{
    /**
     * Handle a generic file upload (images) and return a public URL.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'image' => 'sometimes|image|mimes:jpg,jpeg,png,gif,webp|max:5120', // 5MB
            'file' => 'sometimes|image|mimes:jpg,jpeg,png,gif,webp|max:5120', // Fallback for 'file' field
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors(),
            ], 422);
        }

        try {
            // Get file from either 'image' or 'file' field
            $file = $request->file('image') ?? $request->file('file');

            if (!$file) {
                return response()->json([
                    'status' => false,
                    'message' => 'No file found'
                ], 400);
            }

            // Store in articles directory
            $path = $file->store('articles/images', 'public');

            // Get full URL
            $publicUrl = url(Storage::url($path));

            return response()->json([
                'status' => true,
                'message' => 'File uploaded successfully',
                'data' => [
                    'path' => $publicUrl,
                    'url' => $publicUrl,
                    'file' => basename($path),
                ],
            ], 200);
        } catch (\Throwable $e) {
            Log::error('Upload failed', ['message' => $e->getMessage(), 'trace' => $e->getTraceAsString()]);
            return response()->json([
                'status' => false,
                'message' => 'Could not store file. Please try again later.'
            ], 500);
        }
    }
}
