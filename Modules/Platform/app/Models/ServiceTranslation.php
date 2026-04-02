<?php

namespace Modules\Platform\Models;

use Modules\Base\Models\BaseModel;

class ServiceTranslation extends BaseModel
{
    protected $fillable = [
        'service_id',
        'locale',
        'name',
        'description',
    ];

    public $timestamps = false;

}
