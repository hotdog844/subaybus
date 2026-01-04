<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Bus;
use Illuminate\Support\Facades\DB;

class GpsTcpServer extends Command
{
    // COMMAND: php artisan gps:server
    // It will default to port 5000 to match your Ngrok command
    protected $signature = 'gps:server {port=5000}';
    protected $description = 'Listen for SinoTrack ST-901 TCP Data on Port 5000';

    public function handle()
    {
        $port = $this->argument('port');
        $this->info("🚀 Starting GPS Server on Port $port...");

        // Create the "Reception Desk"
        $socket = stream_socket_server("tcp://0.0.0.0:$port", $errno, $errstr);

        if (!$socket) {
            $this->error("Could not start server: $errstr ($errno)");
            return;
        }

        $this->info("✅ Waiting for device connection...");

        // Infinite loop to keep listening
        while ($conn = stream_socket_accept($socket, -1)) {
            $this->info("--- New Connection Detected ---");
            
            // Read the data sent by the device
            $data = fread($conn, 2048);
            $hex = bin2hex($data);
            
            if (empty($hex)) {
                fclose($conn);
                continue;
            }

            $this->info("📥 RAW DATA: " . $hex);

            // ST-901 PROTOCOL PARSING (Simplified)
            // 1. Check for Login Packet (Starts with 7878 + 0D + 01)
            if (str_starts_with($hex, '78780d01')) {
                // The ID is usually byte 4 to 12. 
                // We strip the first 8 chars (78780d01) and take the next 16 chars (8 bytes)
                $rawId = substr($hex, 8, 16);
                $id = ltrim($rawId, '0'); // Remove leading zeros
                $this->info("🔌 Device Logged In! ID: " . $id);
            }
            
            // 2. Check for Location Packet (Starts with 7878 + 1F + 12 or 22)
            // This contains the Lat/Lng
            elseif (str_starts_with($hex, '78781f12') || str_starts_with($hex, '78781f22')) {
                $this->info("📍 Location Packet Received!");
                // (Parsing logic would go here to update DB)
                // For now, let's just confirm we are receiving it.
            }

            fclose($conn);
        }
        fclose($socket);
    }
}