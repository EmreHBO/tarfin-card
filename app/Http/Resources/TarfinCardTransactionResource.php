<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class TarfinCardTransactionResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request): array
    {
        return [
            'amount'        => $this->amount,
            'currency_code' => $this->currency_code,
        ];
    }
}
