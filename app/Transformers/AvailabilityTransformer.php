<?php

namespace App\Transformers;

use App\Models\Availability;
use League\Fractal\TransformerAbstract;

class AvailabilityTransformer extends TransformerAbstract
{
    public function transform(Availability $availability)
    {
        return [
            'id' => (int) $availability->id,
            'availability_id' => $availability->availability->id,
            'availability_type' => get_class($availability->availability),
            'from' => $availability->from,
            'to' => $availability->to,
        ];
    }
}
