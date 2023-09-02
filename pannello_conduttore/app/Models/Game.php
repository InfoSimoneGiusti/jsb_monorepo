<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

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


    public function getPlayersStatus() {

        $game_id = $this->id;

        $currentSession = Session::getCurrentSession($this);

        $players = $this->players;

        $result = [];

        foreach ($players as $player) {

            $isVolunteer = false;

            if ( $currentSession &&  $currentSession->volunteer_id == $player->id) {
                $isVolunteer = true;
            }

            $count = DB::table('player_session')
                ->join('sessions', 'sessions.id', '=', 'player_session.session_id')
                ->join('games', 'games.id', '=', 'sessions.game_id')
                ->where('game_id', $game_id)
                ->where('player_id', $player->id)
                ->sum('correct_answer');

            $alreadyAnswered = false;
            if ($currentSession && $currentSession->players->contains($player)) {
                $pivotData = $currentSession->players->find($player->id)->pivot;
                if ($pivotData->correct_answer !== null) {
                    $alreadyAnswered = true;
                }
            }

            $result[] = [
                'plain_player_id' => $player->id,
                'player_name' => $player->name,
                'volunteer' => $isVolunteer,
                'score' => $count,
                'alreadyAnswered' => $alreadyAnswered
            ];
        }


        usort($result, function($a, $b) {
            return $a['score'] < $b['score'] ? 1 : -1;
        });

        return $result;

    }

    static function getOpenedGame() {
        return Game::where('closed', false)->orderBy('created_at', 'desc')->first();
    }
}
