<?php

namespace App\Api\V1\Controllers;

use App\Traits\HasApiResponses;
use App\Traits\PaginationMeta;

abstract class ApiController
{
    use HasApiResponses;
    use PaginationMeta;
}
