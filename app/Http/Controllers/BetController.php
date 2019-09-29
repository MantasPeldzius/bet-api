<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Validator;

class BetController extends Controller
{
	private $request;
	
	public function __construct(Request $request)
	{
		$this->request = $request;
	}
	
	public function makeBet()
	{
	    $betslip = $this->request->json()->all();
	    
	    $no_error = self::validateBetslip($betslip);
	    
	    if ($no_error) {
	        $win_amount = self::calculateWinAmount($betslip);
	        if (!Validator::checkMaxWinAmount($betslip, $win_amount)) {
                $no_error = false;
	        }
	    }
	    
	    if ($no_error) {
	        $player = new PlayerController($betslip['player_id']);
	        if (!$player->findPlayer()) {
	            $player->createPlayer();
	        }
	        if ($player->available()) {
    	        if ($player->enoughBalance((float)$betslip['stake_amount'])) {
    	            $player->longProcess();
    	            $player->updateWinning(self::calculateWinAmount($betslip), $betslip['selections']);
    	        } else {
    	            $no_error = false;
    	            Validator::notEnoughBalance($betslip);
    	        }
	        } else {
	            $no_error = false;
	            Validator::playerUnavailable($betslip);
	        }
	    }

	    if ($no_error) {
	        return response(json_encode (new \stdClass()), 201);
	    } else {
    	    return response($betslip, 400);
	    }
    }
    
    private function validateBetslip(&$betslip)
    {
        if (!Validator::checkStructure($betslip)) {
            return false;
        }
        if (!Validator::checkContent($betslip)) {
            return false;
        }
        return true;
    }
    
    private function calculateWinAmount($betslip)
    {
        $win = (float)$betslip['stake_amount'];
        foreach ($betslip['selections'] as $selection) {
            $win = $win * $selection['odds'];
        }
        return round($win, 2, PHP_ROUND_HALF_UP);
    }
    
}
