<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ThongBaoResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'loai' => $this->loai,
            'tieu_de' => $this->tieu_de,
            'noi_dung' => $this->noi_dung,
            'da_doc' => $this->da_doc,
            'reference_type' => $this->reference_type,
            'reference_id' => $this->reference_id,
            'created_at' => $this->created_at?->toIso8601String(),
        ];
    }
}
