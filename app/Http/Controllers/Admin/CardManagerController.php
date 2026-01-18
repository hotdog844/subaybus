<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Card;
use App\Models\Transaction;

class CardManagerController extends Controller
{
    // 1. Show the list of users and their cards
    public function index()
    {
        $users = User::with('card')->get(); // Get users and their attached card info
        return view('admin.cards.index', compact('users'));
    }

    // 2. Add Load (Top Up)
    public function topUp(Request $request, $userId)
    {
        $request->validate(['amount' => 'required|numeric|min:1']);
        
        $user = User::findOrFail($userId);
        $amount = $request->amount;

        // Add to wallet
        $user->wallet_balance += $amount;
        $user->save();

        // Log transaction
        Transaction::create([
            'user_id' => $user->id,
            'type' => 'load_topup',
            'amount' => $amount,
            'description' => 'Admin Manual Top-up',
            'reference_id' => 'ADM-' . time()
        ]);

        return back()->with('success', 'Load added successfully!');
    }

    // 3. Assign a physical RFID Card to a user
    public function assignCard(Request $request, $userId)
    {
        $request->validate(['card_uid' => 'required|string|unique:cards,card_uid']);
        
        Card::create([
            'user_id' => $userId,
            'card_uid' => $request->card_uid,
            'status' => 'active'
        ]);

        return back()->with('success', 'Card assigned successfully!');
    }
}