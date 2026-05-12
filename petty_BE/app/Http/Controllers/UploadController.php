<?php

namespace App\Http\Controllers;

use App\Helpers\ImageHelper;
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
        Log::info('Upload request received', [
            'has_image' => $request->hasFile('image'),
            'has_file' => $request->hasFile('file'),
            'all_files' => array_keys($request->allFiles()),
        ]);

        $validator = Validator::make($request->all(), [
            'image' => 'sometimes|image|mimes:jpg,jpeg,png,gif,webp|max:5120',
            'file'  => 'sometimes|mimes:jpg,jpeg,png,gif,webp,pdf|max:10240',
        ]);

        if ($validator->fails()) {
            Log::error('Upload validation failed', ['errors' => $validator->errors()]);
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
                Log::error('No file found in request');
                return response()->json([
                    'status' => false,
                    'message' => 'No file found'
                ], 400);
            }

            $folder = ($file->getMimeType() === 'application/pdf') ? 'staff/documents' : 'staff/images';
            $path = $file->store($folder, 'public');

            Log::info('File stored successfully', [
                'path' => $path,
                'folder' => $folder,
            ]);

            // Client should store 'path' in DB, display 'url' in UI
            $publicUrl = ImageHelper::resolveUrl($path);

            Log::info('Upload successful', ['path' => $path, 'url' => $publicUrl]);

            return response()->json([
                'status' => true,
                'message' => 'File uploaded successfully',
                'data' => [
                    'path' => $path,
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
