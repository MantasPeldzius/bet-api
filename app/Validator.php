<?php

namespace App;

class Validator
{
    
    private const MIN_STAKE_AMOUNT = 0.3;
    private const MAX_STAKE_AMOUNT = 10000;
    private const MIN_SELECTIONS = 1;
    private const MAX_SELECTIONS = 20;
    private const MIN_ODDS = 1;
    private const MAX_ODDS = 10000;
    private const MAX_WIN_AMOUNT = 20000;
    
    public static function checkStructure(&$betslip)
    {
        if (!self::checkGlobalStructure($betslip)) {
            $betslip['errors'][] = self::getErrorArray(1);
            return false;
        }
        if (!self::checkSelectionStructure($betslip)) {
            $betslip['errors'][] = self::getErrorArray(1);
            return false;
        }
        return true;
    }
    
    public static function checkContent(&$betslip)
    {
        return (self::checkMainDefaults($betslip) && self::checkSelections($betslip));
    }
    
    private static function checkGlobalStructure($betslip)
    {
        if (!isset($betslip['player_id']) || !isset($betslip['stake_amount']) || !isset($betslip['selections'])) {
            return false;
        } elseif (!is_int($betslip['player_id']) && $betslip['player_id'] > 0) {
            return false;
        } elseif (!is_string($betslip['stake_amount']) || !preg_match('/^[0-9]+(\.[0-9]{1,2})?$/', $betslip['stake_amount'])) {
            return false;
        }
        return true;
    }
    
    private static function checkSelectionStructure($betslip)
    {
        if (!is_array($betslip['selections'])) {
            return false;
        } else {
            foreach ($betslip['selections'] as $selection) {
                if (!isset($selection['id']) || !isset($selection['odds'])) {
                    return false;
                } elseif (!is_int($selection['id']) && $selection['id'] > 0) {
                    return false;
                } elseif (!is_string($selection['odds']) || !preg_match('/^[0-9]+(\.[0-9]{1,3})?$/', $selection['odds'])) {
                    return false;
                }
            }
        }
        return true;
    }
    
    private static function checkMainDefaults(&$betslip) {
        $no_error = true;
        if ((float)$betslip['stake_amount'] < self::MIN_STAKE_AMOUNT) {
            $betslip['errors'][] = self::getErrorArray(2);
            $no_error = false;
        }
        if ((float)$betslip['stake_amount'] > self::MAX_STAKE_AMOUNT) {
            $betslip['errors'][] = self::getErrorArray(3);
            $no_error = false;
        }
        if (count($betslip['selections']) < self::MIN_SELECTIONS) {
            $betslip['errors'][] = self::getErrorArray(4);
            $no_error = false;
        }
        if (count($betslip['selections']) > self::MAX_SELECTIONS) {
            $betslip['errors'][] = self::getErrorArray(5);
            $no_error = false;
        }
        return $no_error;
    }
    
    private static function checkSelections(&$betslip)
    {
        $no_error = true;
        // for searching for duplicate selection ids
        $selection_ids = [];
        foreach ($betslip['selections'] as $key => $selection) {
            if ((float)$selection['odds'] < self::MIN_ODDS) {
                $betslip['selections'][$key]['errors'][] = self::getErrorArray(6);
                $no_error = false;
            }
            if ((float)$selection['odds'] > self::MAX_ODDS) {
                $betslip['selections'][$key]['errors'][] = self::getErrorArray(7);
                $no_error = false;
            }
            if (isset($selection_ids[$selection['id']])) {
                if ($selection_ids[$selection['id']]['multiple'] === false) {
                    $selection_ids[$selection['id']]['multiple'] = true;
                    $betslip['selections'][$selection_ids[$selection['id']]['first_key']]['errors'][] = self::getErrorArray(8);
                    $betslip['selections'][$key]['errors'][] = self::getErrorArray(8);
                } else {
                    $betslip['selections'][$key]['errors'][] = self::getErrorArray(8);
                }
                $no_error = false;
            } else {
                $selection_ids[$selection['id']] = [
                    'multiple' => false,
                    'first_key' => $key
                ];
            }
        }
        return $no_error;
    }
    
    public static function checkMaxWinAmount(&$betslip, $win_amount)
    {
        if ($win_amount > self::MAX_WIN_AMOUNT) {
            $betslip['errors'][] = self::getErrorArray(9);
            return false;
        }
        return true;
    }
    
    public static function playerUnavailable(&$betslip)
    {
        $betslip['errors'][] = self::getErrorArray(10);
    }
    
    public static function notEnoughBalance(&$betslip)
    {
        $betslip['errors'][] = self::getErrorArray(11);
    }
    
    private static function getErrorArray($error = 0)
    {
        return ['code' => $error, 'message' => self::getErrorMessage($error)];
    }
    
    private static function getErrorMessage($error = 0)
    {
        switch ($error) {
            case 0:
                return 'Unknown error';
            case 1:
                return 'Betslip structure mismatch';
            case 2:
                return 'Minimum stake amount is ' . self::MIN_STAKE_AMOUNT;
            case 3:
                return 'Maximum stake amount is ' . self::MAX_STAKE_AMOUNT;
            case 4:
                return 'Minimum number of selections is ' . self::MIN_SELECTIONS;
            case 5:
                return 'Maximum number of selections is ' . self::MAX_SELECTIONS;
            case 6:
                return 'Minimum odds are ' . self::MIN_ODDS;
            case 7:
                return 'Maximum odds are ' . self::MAX_ODDS;
            case 8:
                return 'Duplicate selection found';
            case 9:
                return 'Maximum win amount is ' . self::MAX_WIN_AMOUNT;
            case 10:
                return 'Your previous action is not finished yet';
            case 11:
                return 'Insufficient balance';
            default:
                return 'Unknown error';
        }
    }
}
