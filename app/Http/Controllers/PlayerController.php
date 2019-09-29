<?php

namespace App\Http\Controllers;

use App\Player;
use App\Bet;
use App\BetSelection;
use App\BalanceTransaction;

class PlayerController extends Controller
{
    private const DEFAULT_BALANCE = 1000;
    private const LONG_PROCESS = 5;
    private $player = false;
    private $player_id = false;
    private $balance = false;
    public $stake_amount = false;
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct($id)
    {
        $this->player_id = $id;
    }
    
    public function findPlayer()
    {
        $this->player = Player::find($this->player_id);
        if (!$this->player) {
            return false;
        } else {
            return true;
        }
    }
    
    public function createPlayer()
    {
        $this->player = Player::create(['id' => $this->player_id]);
    }
    
    public function available()
    {
        if ($this->player->active === 1) {
            return false;
        }
        return true;
    }
    
    public function enoughBalance($stake_amount) 
    {
        $this->stake_amount = $stake_amount;
        return ((float)$this->player->balance >= $this->stake_amount);
    }
    
    public function updateWinning($win_amount, $selections)
    {
        $bet = Bet::create(['stake_amount' => $this->stake_amount]);
        foreach ($selections as $selection) {
            BetSelection::create([
                'bet_id' => $bet->id,
                'selection_id' => $selection['id'],
                'odds' => $selection['odds'],
            ]);
        }
        $final_balance = round($this->player->balance - $this->stake_amount + $win_amount, 2);
        BalanceTransaction::create([
            'player_id' => $this->player->id,
            'amount' => $final_balance,
            'amount_before' => $this->player->balance,
        ]);
        return $this->player->update(['balance' => $final_balance]);
    }
    
    public function longProcess()
    {
        $this->player->update(['active' => 1]);
        sleep(rand(1, self::LONG_PROCESS));
        $this->player->update(['active' => 0]);
    }
}
