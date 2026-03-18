<?php
$content = file_get_contents('app/Http/Controllers/PhieuChiController.php');

$pattern = '/\/\/ Validate dữ liệu\s*\$validator = Validator::make\(\$request->all\(\), \[.*?\]\);\s*if \(\$validator->fails\(\)\) \{\s*return response\(\)->json\(\[\s*\'status\' => false,\s*\'message\' => \'Dữ liệu không hợp lệ\',\s*\'errors\' => \$validator->errors\(\),\s*\], 422\);\s*\}/s';

$count = 0;
// We only want to replace the first occurrence (which is store). Wait, preg_replace replaces all by default if we don't supply limit, or we can use limit. Wait, the rules and messages are different. Let's just use string replacement.
$start = strpos($content, '// Validate dữ liệu');
$end = strpos($content, 'DB::beginTransaction();', $start);
$chunk = substr($content, $start, $end - $start);
$content = substr_replace($content, "\n        ", $start, $end - $start);

// update method
$start2 = strpos($content, '// Validate dữ liệu', $start + 1);
$end2 = strpos($content, 'DB::beginTransaction();', $start2);
if ($start2 !== false) {
    $content = substr_replace($content, "\n        ", $start2, $end2 - $start2);
}

// For thanhToan
$start3 = strpos($content, '// Validate dữ liệu', $start2 + 1);
$end3 = strpos($content, 'DB::beginTransaction();', $start3);
if ($start3 !== false) {
    // Maybe keep this one because we didn't make a request for it. Or maybe ThanhToanPhieuChiRequest?
}

file_put_contents('app/Http/Controllers/PhieuChiController.php', $content);
