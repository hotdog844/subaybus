<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use thiagoalessio\TesseractOCR\TesseractOCR;
use App\Models\User; 
use Illuminate\Support\Facades\Log;

class OCRController extends Controller
{
    public function verifyStudentId(Request $request)
    {
        // 1. Validate: User must send an image and their name
        $request->validate([
            'id_image' => 'required|image|mimes:jpeg,png,jpg|max:5048',
            'student_name' => 'required|string', 
        ]);

        // 2. Save the Image Temporarily
        if($request->hasFile('id_image')){
            $image = $request->file('id_image');
            // Create a unique name to prevent overwriting
            $imageName = time().'_'.$image->getClientOriginalName();  
            $image->move(public_path('uploads/ids'), $imageName);
            $imagePath = public_path('uploads/ids') . '/' . $imageName;
        } else {
            return response()->json(['status' => 'error', 'message' => 'No image uploaded']);
        }

        // 3. The "Magic": Read the Text using Tesseract
        try {
            $ocr = new TesseractOCR($imagePath);
            // This path must match where you installed it on your laptop
            $ocr->executable('C:\Program Files\Tesseract-OCR\tesseract.exe'); 
            
            // Allow Tesseract to read English
            $ocr->lang('eng');
            
            $scannedText = $ocr->run();
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error', 
                'message' => 'OCR Failed: ' . $e->getMessage()
            ]);
        }

        // 4. Clean the Data (Convert to Uppercase for easier matching)
        $cleanScannedText = strtoupper($scannedText);
        $inputName = strtoupper($request->student_name);

        // ======================================================
        // 5. SMART MATCHING LOGIC (The Fix)
        // ======================================================
        
        // Step A: Break the typed name into pieces 
        // Example: "Kenneth Desales" -> ["KENNETH", "DESALES"]
        $nameParts = explode(' ', $inputName);
        
        $matchCount = 0;
        $requiredMatches = 0;

        foreach ($nameParts as $part) {
            // Clean up the word (remove extra spaces)
            $cleanPart = trim($part);

            // Skip empty parts (in case user typed double spaces)
            if (empty($cleanPart)) continue;

            $requiredMatches++; // We need to find this word

            // Check if this specific word exists anywhere in the scanned text
            if (str_contains($cleanScannedText, $cleanPart)) {
                $matchCount++;
            }
        }

        // Step B: Decision Time
        // We require ALL parts of the name to be found (Sequence doesn't matter)
        if ($matchCount >= $requiredMatches && $requiredMatches > 0) {
            
            // Log success for your analytics
            Log::info("Student Verified: " . $inputName);

            // OPTIONAL: If you want to update the User in the database automatically:
            // $user = User::where('name', $request->student_name)->first();
            // if ($user) { $user->is_verified = true; $user->save(); }

            return response()->json([
                'status' => 'success', 
                'message' => 'ID Verified! Name found on card.',
                'detected_text' => $scannedText 
            ]);
        } else {
            return response()->json([
                'status' => 'fail', 
                'message' => 'Verification Failed. Name mismatch.',
                'debug_info' => "Found $matchCount out of $requiredMatches words.",
                'detected_text' => $scannedText // Show this to the panel!
            ]);
        }
    }
}