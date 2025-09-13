<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class QueryLogs extends Model
{
    protected $fillable = [
        'query',
        'execution_time',
    ];
}
