<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class RfidController extends Controller
{
    public function tapCard(Request $request)
    {
        $uid = $request->input('uid'); // Get UID from ESP32
        
        // Get Bus ID (Default to '1' if the ESP32 doesn't send it for some reason)
        $busId = $request->input('bus_id', 1); 

        // 1. Find the card in database
        $card = DB::table('rfid_cards')->where('uid', $uid)->first();

        if (!$card) {
            return response()->json([
                'status' => 'error',
                'message' => 'UNKNOWN CARD'
            ]);
        }

        // 2. Determine Fare Logic
        // Regular = 10.20
        // Students/Seniors/PWD = 8.16
        if ($card->user_type === 'regular') {
            $fare = 10.20;
        } else {
            // This covers 'student', 'senior', 'pwd'
            $fare = 8.16;
        }

        // 3. Check Balance
        if ($card->balance < $fare) {
            return response()->json([
                'status' => 'error',
                'message' => 'INSUFFICIENT BAL',
                'balance' => $card->balance
            ]);
        }

        // 4. Deduct & Save
        DB::table('rfid_cards')->where('id', $card->id)->update([
            'balance' => $card->balance - $fare,
            'updated_at' => Carbon::now()
        ]);

        // 5. Create Log (Digital Receipt)
        DB::table('transaction_logs')->insert([
            'rfid_uid' => $uid,
            'transaction_type' => 'PAYMENT',
            'amount' => $fare,
            // Dynamic Location: "Bus 01", "Bus 02", etc.
            'location' => 'Bus ' . str_pad($busId, 2, '0', STR_PAD_LEFT), 
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now()
        ]);

        // 6. Reply to ESP32
        return response()->json([
            'status' => 'success',
            'type' => strtoupper($card->user_type), 
            'deducted' => $fare,
            'new_balance' => $card->balance - $fare
        ]);
    }
}