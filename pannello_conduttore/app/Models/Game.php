<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\DB;

class Game extends Model
{
    use HasFactory;

    protected $fillable = [
        'closed'
    ];

    protected $casts = [
        'closed' => 'boolean'
    ];

    public function sessions() : HasMany {
        return $this->hasMany(Session::class);
    }

    public function players() : HasMany {
        return $this->hasMany(Player::class);
    }


    public function getPlayersWithScore() {

        $game_id = $this->id;

        return DB::table('players')
            ->select('players.name as player_name', DB::raw('IFNULL(SUM(player_session.correct_answer), 0) as score'))
            ->leftJoin('player_session', 'players.id', '=', 'player_session.player_id')
            ->leftJoin('sessions', function ($join) use ($game_id) {
                $join->on('player_session.session_id', '=', 'sessions.id')
                    ->where('sessions.game_id', '=', $game_id);
            })
            ->groupBy('players.id')
            ->get();

    }
}
