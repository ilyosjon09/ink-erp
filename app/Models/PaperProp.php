<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PaperProp extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function paperType()
    {
        return $this->belongsTo(PaperType::class);
    }
}
