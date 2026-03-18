<?php

$content = file_get_contents('app/Http/Controllers/PhieuKhamController.php');

// Replace standard JSON responses with PhieuKhamResource
$content = str_replace(
    'return response()->json([
                \'message\' => \'Thêm phiếu khám thành công\',
                \'data\' => $phieuKham,
            ], 201);',
    'return response()->json([
                \'message\' => \'Thêm phiếu khám thành công\',
                \'data\' => new \App\Http\Resources\PhieuKhamResource($phieuKham),
            ], 201);',
    $content
);

$content = str_replace(
    'return response()->json([
                \'message\' => \'Lấy danh sách phiếu khám thành công\',
                \'data\' => $phieuKhams->items(),
                \'pagination\' => [
                    \'total\' => $phieuKhams->total(),
                    \'per_page\' => $phieuKhams->perPage(),
                    \'current_page\' => $phieuKhams->currentPage(),
                    \'last_page\' => $phieuKhams->lastPage(),
                ]
            ], 200);',
    'return response()->json([
                \'message\' => \'Lấy danh sách phiếu khám thành công\',
                \'data\' => \App\Http\Resources\PhieuKhamResource::collection($phieuKhams->items()),
                \'pagination\' => [
                    \'total\' => $phieuKhams->total(),
                    \'per_page\' => $phieuKhams->perPage(),
                    \'current_page\' => $phieuKhams->currentPage(),
                    \'last_page\' => $phieuKhams->lastPage(),
                ]
            ], 200);',
    $content
);

// We won't fully touch show() because it has a complex custom payload, but wait, Phase 3 says we should. We will leave show() as is for now or simplify.
// Let's modify getByDoctor to use Resource.
$content = str_replace(
    'return response()->json([
                \'success\' => true,
                \'message\' => \'Lấy danh sách phiếu khám của bác sĩ thành công\',
                \'data\' => $phieuKhams->items(),',
    'return response()->json([
                \'success\' => true,
                \'message\' => \'Lấy danh sách phiếu khám của bác sĩ thành công\',
                \'data\' => \App\Http\Resources\PhieuKhamResource::collection($phieuKhams->items()),',
    $content
);

// update function
$content = str_replace(
    'return response()->json([
                \'success\' => true,
                \'message\' => \'Cập nhật phiếu khám thành công\',
                \'data\' => $phieuKham
            ], 200);',
    'return response()->json([
                \'success\' => true,
                \'message\' => \'Cập nhật phiếu khám thành công\',
                \'data\' => new \App\Http\Resources\PhieuKhamResource($phieuKham)
            ], 200);',
    $content
);

file_put_contents('app/Http/Controllers/PhieuKhamController.php', $content);
