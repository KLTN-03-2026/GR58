<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ImageController extends Controller
{
    /**
     * Upload image to storage and return URL
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function upload(Request $request)
    {
        // Validate the request
        $request->validate([
            'image' => 'required|image|mimes:jpeg,png,jpg,gif,webp|max:5120', // 5MB
        ]);

        try {
            // Get the uploaded file
            $image = $request->file('image');

            // Generate unique filename
            $filename = time() . '_' . uniqid() . '.' . $image->getClientOriginalExtension();

            // Store the file in public/uploads/articles directory
            $path = $image->storeAs('uploads/articles', $filename, 'public');

            // Return the stored file URL
            $imageUrl = asset('storage/' . $path);

            return response()->json([
                'status' => true,
                'message' => 'Tải lên ảnh thành công',
                'data' => [
                    'url' => $imageUrl,
                    'path' => $path,
                    'filename' => $filename,
                ],
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Lỗi khi tải lên ảnh: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Delete image from storage
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function delete(Request $request)
    {
        $request->validate([
            'path' => 'required|string',
        ]);

        try {
            $path = $request->input('path');

            // Remove 'uploads/articles/' prefix if present for storage deletion
            $storagePath = str_replace('storage/', '', $path);

            if (\Storage::disk('public')->exists($storagePath)) {
                \Storage::disk('public')->delete($storagePath);

                return response()->json([
                    'status' => true,
                    'message' => 'Xóa ảnh thành công',
                ], 200);
            }

            return response()->json([
                'status' => false,
                'message' => 'Tệp không tồn tại',
            ], 404);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Lỗi khi xóa ảnh: ' . $e->getMessage(),
            ], 500);
        }
    }
}
