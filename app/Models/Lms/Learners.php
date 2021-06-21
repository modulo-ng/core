<?php

namespace App\Models\Lms;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Learners extends Model
{
    use SoftDeletes;
    protected $table = 'lms_learners';
    protected $guarded  = [];
}
