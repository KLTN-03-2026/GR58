<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class DinhKemPhieuKhamResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'              => $this->id,
            'phieu_kham_id'   => $this->phieu_kham_id,
            'ten_file'        => $this->ten_file,
            'duong_dan'       => $this->duong_dan,
            'loai_mime'       => $this->loai_mime,
            'kich_thuoc'      => $this->kich_thuoc,
            'url'             => $this->url,
            'nguoi_upload_id' => $this->nguoi_upload_id,
            'created_at'      => $this->created_at?->format('Y-m-d H:i:s'),
        ];
    }
}
