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
        
        // 1. Find the card in database
        // We use DB::table just for quick testing, but Models are better
        $card = DB::table('rfid_cards')->where('uid', $uid)->first();

        if (!$card) {
            return response()->json([
                'status' => 'error',
                'message' => 'UNKNOWN CARD'
            ]);
        }

        // 2. Determine Fare
        $fare = ($card->user_type == 'student') ? 12.00 : 15.00;

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

        // 5. Create Log (For Panel Analytics)
        DB::table('transaction_logs')->insert([
            'rfid_uid' => $uid,
            'transaction_type' => 'PAYMENT',
            'amount' => $fare,
            'location' => 'Bus 01', // Static for now
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