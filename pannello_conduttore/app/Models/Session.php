<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Facades\Log;

class Session extends Model
{
    use HasFactory;

    protected $fillable = [
        'game_id',
        'volunteer_id',
        'question',
        'timestamp',
        'end_timestamp',
        'interrupt_timestamp',
        'resume_interrupt_timestamp',
        'end_resume_interrupt_timestamp',
        'closed'
    ];

    protected $casts = [
        'closed' => 'boolean',
    ];
    public function game() : BelongsTo {
        return $this->belongsTo(Game::class);
    }

    public function players() : BelongsToMany {
        return $this->belongsToMany(Player::class)->withPivot(['correct_answer', 'timestamp', 'answer']);
    }

    public function volunteer() : BelongsTo {
        return $this->belongsTo(Player::class, 'volunteer_id', 'id');
    }

    static function getCurrentSession(Game $game) : null | Session {
        return Session::where('game_id', $game->id)->where('closed', false)->first();
    }

    public function getRemainingTimer() {
        $server_time = time();
        if ($this->interrupt_timestamp) {
            return $this->end_timestamp - $this->interrupt_timestamp;
        } else {
            return $this->end_timestamp - $server_time;
        }
    }


    public function getVolunteerRemainingTimer() {
        $server_time = time();

        if ($this->end_resume_interrupt_timestamp && $this->resume_interrupt_timestamp) {
            if ($this->interrupt_timestamp) {
                return $this->end_resume_interrupt_timestamp - $this->resume_interrupt_timestamp;
            } else {
                return $this->end_resume_interrupt_timestamp - $server_time;
            }
        } else {
            return null;
        }

    }

}
