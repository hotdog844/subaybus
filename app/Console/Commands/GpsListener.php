<?php

namespace App\Console\Commands;

use App\Models\BusLocation;
use App\Services\SinoTrackDecoder;
use React\Socket\ConnectionInterface;
use React\Socket\SocketServer;
use Illuminate\Console\Command;

class GpsListener extends Command
{
    /**
     * The name and signature of the console command.
     *
     * Run this with: php artisan gps:listener
     *
     * @var string
     */
    protected $signature = 'gps:listener';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Listen for GPS tracker data on a TCP socket';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $port = 8090;
        $socket = new SocketServer('0.0.0.0:' . $port);
        $decoder = new SinoTrackDecoder();
        $connections = new \SplObjectStorage(); // To store IMEI for each connection

        $this->info("✅ GPS Listener started on port " . $port);

        $socket->on('connection', function (ConnectionInterface $connection) use ($decoder, $connections) {
            $remoteAddr = $connection->getRemoteAddress();
            $this->info("🔗 New connection from: " . $remoteAddr);

            $connection->on('data', function ($data) use ($connection, $decoder, $connections, $remoteAddr) {
                $hexData = bin2hex($data);
                $this->info("📩 Received from {$remoteAddr}: " . $hexData);

                $decoded = $decoder->decode($hexData);

                if ($decoded) {
                    if ($decoded['type'] === 'login') {
                        // When a tracker logs in, store its IMEI
                        $connections->offsetSet($connection, $decoded['imei']);
                        $this->info("✅ Tracker with IMEI {$decoded['imei']} logged in.");
                    } elseif ($decoded['type'] === 'location' && $connections->offsetExists($connection)) {
                        // If it's a location packet, get the stored IMEI
                        $imei = $connections->offsetGet($connection);

                        BusLocation::create([
                            'imei' => $imei,
                            'latitude' => $decoded['latitude'],
                            'longitude' => $decoded['longitude'],
                        ]);

                        $this->info("📍 Saved location for IMEI {$imei}: {$decoded['latitude']}, {$decoded['longitude']}");
                    }
                }
            });

            $connection->on('close', function () use ($connections, $remoteAddr, $connection) {
                if ($connections->offsetExists($connection)) {
                    $connections->offsetUnset($connection);
                }
                $this->info("❌ Connection closed from: " . $remoteAddr);
            });
        });
    }
}
