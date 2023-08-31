<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Player extends Model
{
    use HasFactory;

    protected $fillable = [
        'name'
    ];

    public function sessions() : BelongsToMany {
        return $this->belongsToMany(Session::class)->withPivot(['correct_answer', 'timestamp']);
    }

}
