<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Session extends Model
{
    use HasFactory;

    protected $fillable = [
        'game_id',
        'question',
        'timestamp',
        'end_timestamp',
        'closed'
    ];

    protected $casts = [
        'closed' => 'boolean'
    ];
    public function game() : BelongsTo {
        return $this->belongsTo(Game::class);
    }

    public function players() : BelongsToMany {
        return $this->belongsToMany(Player::class)->withPivot(['correct_answer', 'timestamp']);
    }

}
