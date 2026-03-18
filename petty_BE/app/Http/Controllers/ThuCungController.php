<?php

namespace App\Http\Controllers;

use App\Models\ThuCung;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;

class ThuCungController extends Controller
{
    // Safety limit when returning all records in one response
    private const MAX_RETURN_ALL = 1000;

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        // filters
        $perPageParam = $request->get('per_page', null);

        $query = ThuCung::query();

        $user = $request->user();

        // only show pets of the authenticated customer when the column exists
        if (Schema::hasColumn('thu_cungs', 'khach_hang_id')) {
            if ($user) {
                $query->where('khach_hang_id', $user->id);
            } else {
                // if no auth, return empty set to prevent leaking
                $query->whereRaw('0 = 1');
            }
        } else {
            // fallback: if the column does not exist, return all only when authenticated
            if (! $user) {
                $query->whereRaw('0 = 1');
            }
        }

        if ($request->filled('loai_thu_cung')) {
            $query->where('loai_thu_cung', $request->get('loai_thu_cung'));
        }

        if ($request->filled('giong_thu_cung')) {
            $query->where('giong_thu_cung', $request->get('giong_thu_cung'));
        }

        // return-all logic
        if ($this->shouldReturnAll($request, $perPageParam)) {
            $total = $query->count();

            if ($total > self::MAX_RETURN_ALL && ! $request->boolean('force')) {
                return response()->json([
                    'status' => false,
                    'message' => \Illuminate\Support\Facades\Lang::get('messages.result_too_large'),
                    'meta' => ['total' => $total, 'max_allowed' => self::MAX_RETURN_ALL]
                ], 413);
            }

            $items = $query->get();

            $transformed = $items->map(fn($item) => $this->formatThuCung($item));

            return response()->json([
                'status' => true,
                'data' => $transformed,
                'meta' => [
                    'total' => $transformed->count(),
                    'returned' => $transformed->count(),
                ],
            ]);
        }

        $perPage = (int) ($perPageParam ?? 15);
        if ($perPage <= 0) {
            $perPage = 15;
        }

        $results = $query->paginate($perPage);

        // transform paginated items
        $results->getCollection()->transform(fn($item) => $this->formatThuCung($item));

        return response()->json([
            'status' => true,
            'data' => $results
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $rules = [
            'ten_thu_cung' => 'required|string|max:255',
            'loai_thu_cung' => 'required|string|max:255',
            'giong_thu_cung' => 'required|string|max:255',
            'tuoi_thu_cung' => 'required|date',
            'gioi_tinh' => 'required|string|max:50',
            'can_nang' => 'required|string|max:50',
        ];

        if ($request->hasFile('anh_dai_dien')) {
            $rules['anh_dai_dien'] = 'nullable|image|max:2048';
        } else {
            $rules['anh_dai_dien'] = 'nullable|string|max:255';
        }

        $data = $request->validate($rules);

        // handle image input robustly: file upload or string path or empty to unset
        if ($request->hasFile('anh_dai_dien')) {
            $file = $request->file('anh_dai_dien');
            if ($file->isValid()) {
                $path = $file->store('thu_cungs', 'public');
                $data['anh_dai_dien'] = $path;
            }
        } elseif ($request->exists('anh_dai_dien')) {
            $val = $request->get('anh_dai_dien');
            if (trim((string)$val) === '') {
                $data['anh_dai_dien'] = null;
            } else {
                $data['anh_dai_dien'] = $val;
            }
        }

        // assign to authenticated customer if available and the column exists
        if ($request->user() && Schema::hasColumn('thu_cungs', 'khach_hang_id')) {
            $data['khach_hang_id'] = $request->user()->id;
        }

        try {
            $thuCung = ThuCung::create($data);
            return response()->json([
                'status' => true,
                'data' => $this->formatThuCung($thuCung)
            ], 201);
        } catch (\Throwable $e) {
            Log::error('ThuCung store error: ' . $e->getMessage(), ['exception' => $e]);
            return response()->json(['status' => false, 'message' => \Illuminate\Support\Facades\Lang::get('messages.pet_save_error')], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(ThuCung $thuCung)
    {
        $user = request()->user();
        if (Schema::hasColumn('thu_cungs', 'khach_hang_id')) {
            if (! $user || $thuCung->khach_hang_id !== $user->id) {
                return response()->json(['status' => false, 'message' => \Illuminate\Support\Facades\Lang::get('messages.forbidden')], 403);
            }
        } else {
            if (! $user) {
                return response()->json(['status' => false, 'message' => \Illuminate\Support\Facades\Lang::get('messages.forbidden')], 403);
            }
        }

        return response()->json([
            'status' => true,
            'data' => $this->formatThuCung($thuCung)
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, ThuCung $thuCung)
    {
        // ownership check: only owner can update when the column exists
        $user = $request->user();
        if (Schema::hasColumn('thu_cungs', 'khach_hang_id')) {
            if (! $user || $thuCung->khach_hang_id !== $user->id) {
                return response()->json(['status' => false, 'message' => \Illuminate\Support\Facades\Lang::get('messages.forbidden')], 403);
            }
        } else {
            // if the column does not exist, require authentication but skip owner comparison
            if (! $user) {
                return response()->json(['status' => false, 'message' => \Illuminate\Support\Facades\Lang::get('messages.forbidden')], 403);
            }
        }

        $rules = [
            'ten_thu_cung' => 'sometimes|required|string|max:255',
            'loai_thu_cung' => 'sometimes|required|string|max:255',
            'giong_thu_cung' => 'sometimes|required|string|max:255',
            'tuoi_thu_cung' => 'sometimes|required|date',
            'gioi_tinh' => 'sometimes|required|string|max:50',
            'can_nang' => 'sometimes|required|string|max:50',
        ];

        if ($request->hasFile('anh_dai_dien')) {
            $rules['anh_dai_dien'] = 'nullable|image|max:2048';
        } else {
            $rules['anh_dai_dien'] = 'nullable|string|max:255';
        }

        $data = $request->validate($rules);

        $oldImg = $thuCung->anh_dai_dien ?? null;

        // handle image input: file upload, explicit key present (even empty), or omitted
        if ($request->hasFile('anh_dai_dien')) {
            $file = $request->file('anh_dai_dien');
            if ($file->isValid()) {
                $path = $file->store('thu_cungs', 'public');
                $data['anh_dai_dien'] = $path;

                // delete old stored image if present and not an external URL
                if ($oldImg && ! preg_match('#^https?://#i', $oldImg)) {
                    if (Storage::disk('public')->exists($oldImg)) {
                        Storage::disk('public')->delete($oldImg);
                    }
                }
            }
        } elseif ($request->exists('anh_dai_dien')) {
            $val = $request->get('anh_dai_dien');
            if (trim((string)$val) === '') {
                // remove existing stored image
                $data['anh_dai_dien'] = null;
                if ($oldImg && ! preg_match('#^https?://#i', $oldImg)) {
                    if (Storage::disk('public')->exists($oldImg)) {
                        Storage::disk('public')->delete($oldImg);
                    }
                }
            } else {
                // set to provided path/string and delete old stored image if switching
                $data['anh_dai_dien'] = $val;
                if ($oldImg && ! preg_match('#^https?://#i', $oldImg) && $oldImg !== $val) {
                    if (Storage::disk('public')->exists($oldImg)) {
                        Storage::disk('public')->delete($oldImg);
                    }
                }
            }
        }

        // Prevent changing ownership via update
        if (array_key_exists('khach_hang_id', $data)) {
            unset($data['khach_hang_id']);
        }

        if (empty($data)) {
            return response()->json([
                'status' => true,
                'message' => \Illuminate\Support\Facades\Lang::get('messages.no_changes'),
                'data' => $this->formatThuCung($thuCung)
            ]);
        }

        try {
            $thuCung->update($data);
            return response()->json([
                'status' => true,
                'message' => \Illuminate\Support\Facades\Lang::get('messages.update_success'),
                'data' => $this->formatThuCung($thuCung->fresh())
            ]);
        } catch (\Throwable $e) {
            Log::error('ThuCung update error: ' . $e->getMessage(), ['exception' => $e, 'id' => $thuCung->id]);
            return response()->json(['status' => false, 'message' => \Illuminate\Support\Facades\Lang::get('messages.pet_update_error')], 500);
        }
    }
    /**
     * Remove the specified resource from storage.
     */
    public function destroy(ThuCung $thuCung)
    {
        $user = request()->user();
        if (Schema::hasColumn('thu_cungs', 'khach_hang_id')) {
            if (! $user || $thuCung->khach_hang_id !== $user->id) {
                return response()->json(['status' => false, 'message' => \Illuminate\Support\Facades\Lang::get('messages.forbidden')], 403);
            }
        } else {
            if (! $user) {
                return response()->json(['status' => false, 'message' => \Illuminate\Support\Facades\Lang::get('messages.forbidden')], 403);
            }
        }

        // delete stored image if exists and it's a storage path
        $img = $thuCung->anh_dai_dien ?? null;
        if ($img && ! preg_match('#^https?://#i', $img)) {
            // assume path like 'thu_cungs/file.jpg'
            if (Storage::disk('public')->exists($img)) {
                Storage::disk('public')->delete($img);
            }
        }

        $thuCung->delete();

        return response()->json([
            'status' => true,
            'message' => \Illuminate\Support\Facades\Lang::get('messages.pet_deleted')
        ]);
    }

    /**
     * Determine whether the request intends to retrieve all records.
     */
    private function shouldReturnAll(Request $request, $perPageParam): bool
    {
        if ($request->boolean('all')) {
            return true;
        }

        if (is_string($perPageParam) && strtolower($perPageParam) === 'all') {
            return true;
        }

        if (is_numeric($perPageParam) && (int)$perPageParam <= 0) {
            return true;
        }

        return false;
    }

    /**
     * Format a ThuCung model for API responses, adding absolute image URL.
     */
    private function formatThuCung(ThuCung $thuCung): array
    {
        $data = $thuCung->toArray();

        $img = $thuCung->anh_dai_dien ?? null;
        if ($img) {
            if (preg_match('#^https?://#i', $img)) {
                $data['anh_dai_dien_url'] = $img;
            } else {
                $data['anh_dai_dien_url'] = url(Storage::url($img));
            }
        } else {
            $data['anh_dai_dien_url'] = null;
        }

        return $data;
    }
}
