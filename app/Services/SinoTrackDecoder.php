<?php

namespace App\Services;

class SinoTrackDecoder
{
    // This method will try to decode the incoming hex data
    public function decode(string $hexData): ?array
    {
        // A SinoTrack "Login Packet" starts with '7878' and has protocol number '01'
        if (str_starts_with($hexData, '7878') && substr($hexData, 6, 2) === '01') {
            // The IMEI is 8 bytes (16 hex characters) long, starting at the 8th character
            $imeiHex = substr($hexData, 8, 16);
            // The first digit is '0', so we take the last 15 digits for the actual IMEI.
            $imei = substr(hex2bin($imeiHex), 1);

            return ['type' => 'login', 'imei' => $imei];
        }

        // A SinoTrack "Location Packet" has protocol number '12' or '22'
        $protocolNumber = substr($hexData, 6, 2);
        if (str_starts_with($hexData, '7878') && ($protocolNumber === '12' || $protocolNumber === '22')) {
            // For this example, we assume an IMEI is already known for this connection.
            // In a real app, you'd store the IMEI from the login packet in memory.
            // For now, we'll just parse the coordinates.
            $latHex = substr($hexData, 18, 8);
            $lngHex = substr($hexData, 26, 8);

            $latitude = hexdec($latHex) / 1800000.0;
            $longitude = hexdec($lngHex) / 1800000.0;

            return ['type' => 'location', 'latitude' => $latitude, 'longitude' => $longitude];
        }

        return null; // Return null if we can't decode it
    }
}
