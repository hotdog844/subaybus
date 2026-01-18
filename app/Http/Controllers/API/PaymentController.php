<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Card;
use App\Models\Transaction;
use App\Models\User;

class PaymentController extends Controller
{
    public function tapCard(Request $request)
    {
        // 1. Validate the Input (The ESP32 sends this)
        $request->validate([
            'card_uid' => 'required|string',
            'bus_id'   => 'nullable|integer', // Optional for now
        ]);

        $cardUid = $request->card_uid;
        $fareAmount = 15.00; // Fixed fare for now (We can make this dynamic later)

        // 2. Find the Card in Database
        $card = Card::where('card_uid', $cardUid)->first();

        // SCENARIO A: Card not found (Unregistered)
        if (!$card) {
            return response()->json([
                'status' => 'error',
                'message' => 'Card not registered',
                'code' => '404'
            ], 404);
        }

        // SCENARIO B: Card is blocked or inactive
        if ($card->status !== 'active') {
            return response()->json([
                'status' => 'error',
                'message' => 'Card is inactive',
                'code' => '403'
            ], 403);
        }

        $user = $card->user;

        // SCENARIO C: Not enough money (Insufficient Balance)
        if ($user->wallet_balance < $fareAmount) {
            return response()->json([
                'status' => 'failed',
                'message' => 'Insufficient Balance',
                'balance' => $user->wallet_balance,
                'code' => '402' // Payment Required
            ], 402);
        }

        // SCENARIO D: SUCCESS! (Deduct Money)
        // 1. Deduct from Wallet
        $user->wallet_balance -= $fareAmount;
        $user->save();

        // 2. Log the Transaction (For Admin Panel)
        Transaction::create([
            'user_id' => $user->id,
            'card_id' => $card->id,
            'type' => 'fare_payment',
            'amount' => $fareAmount,
            'description' => 'Bus Ride Payment',
            'reference_id' => 'TRX-' . time(),
        ]);

        return response()->json([
            'status' => 'success',
            'message' => 'Payment Successful',
            'new_balance' => $user->wallet_balance,
            'code' => '200'
        ], 200);
    }
}