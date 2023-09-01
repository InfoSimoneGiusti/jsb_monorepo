<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Facades\DB;

class Player extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'game_id'
    ];

    public function sessions() : BelongsToMany {
        return $this->belongsToMany(Session::class)->withPivot(['correct_answer', 'timestamp', 'answer']);
    }

    public function game() : BelongsTo {
        return $this->belongsTo(Game::class);
    }


    //TODO da rifare con una query diretta al db che ritorno utenti e relativo punteggio
    public function getScore(Game $game) {

        $session_ids = [];
        foreach ($game->sessions as $session) {
            $session_ids[] = $session->id;
        }

        return DB::table('player_session')
                    ->whereIn('session_id', $session_ids)
                    ->where('player_id', $this->id)
                    ->where('correct_answer', true)
                    ->count();

    }

}
