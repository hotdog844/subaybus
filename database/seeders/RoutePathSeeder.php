<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class RoutePathSeeder extends Seeder
{
    public function run()
    {
        // --- ROUTE 1: PdP Green (Terminal -> Tanza via City Proper) ---
        // REAL GPS BUS will use this route
        $greenRoute = json_encode([
            [11.559740, 122.751550], // Roxas City Integrated Transport Terminal
            [11.562688, 122.749760], // Pueblo de Panay Commercial Area
            [11.559131, 122.751619], // Security Bank
            [11.565131, 122.748460], // Robinsons Place Roxas
            [11.582410, 122.753089], // Capiz Bridge
            [11.571781, 122.753940], // Roxas Avenue
            [11.573603, 122.756563], // Hemingway Street
            [11.574762, 122.757158], // DPWH, Camansi Street
            [11.575332, 122.758540], // Villareal Stadium
            [11.577635, 122.756034], // Capiz State University (CapSU)
            [11.577298, 122.754678], // Capiz Doctors Hospital
            [11.584476, 122.759119], // Legaspi Street
            [11.578519, 122.759883], // Tiza Bridge
            [11.580420, 122.763335], // Gov. A. Balgos Avenue
            [11.581891, 122.786628], // Roxas City Welcome Arc (Brgy. Tanza)
        ]);

        DB::table('routes')->insertOrIgnore([
            'name' => 'PdP Green Route',
            'color' => '#00b894', // Green
            'path_data' => $greenRoute,
            'created_at' => now(), 'updated_at' => now(),
        ]);

        // --- ROUTE 2: PdP Red (Terminal -> Culasi via Airport) ---
        // GHOST BUS A
        $redRoute = json_encode([
            [11.559740, 122.751550], // Roxas City Integrated Transport Terminal
            [11.562688, 122.749760], // Pueblo de Panay Commercial Area
            [11.559131, 122.751619], // Security Bank
            [11.565131, 122.748460], // Robinsons Place Roxas
            [11.569007, 122.754693], // KM 1 Lawaan (Hercor College)
            [11.578196, 122.751106], // Railway Street
            [11.577469, 122.753760], // Dancorr
            [11.574372, 122.752699], // Calipay Area
            [11.582685, 122.748931], // Rizal Street
            [11.579953, 122.746979], // Jumbo Bridge
            [11.584748, 122.752331], // Hughes Street (Roxas City Hall)
            [11.584226, 122.746812], // PhilHealth
            [11.584045, 122.746592], // Gov. Hernandez Avenue
            [11.584691, 122.751270], // Burgos Street (Petron Gas Station)
            [11.594043, 122.747741], // SM City Roxas
            [11.598193, 122.748545], // Roxas City Airport
            [11.597767, 122.744825], // Arnaldo Boulevard
            [11.606372, 122.735395], // Brgy. Baybay
            [11.605351, 122.737912], // People’s Park
            [11.604748, 122.709621], // Port of Culasi
        ]);

        DB::table('routes')->insertOrIgnore([
            'name' => 'PdP Red Route',
            'color' => '#ff7675', // Red
            'path_data' => $redRoute,
            'created_at' => now(), 'updated_at' => now(),
        ]);

        // --- ROUTE 3: PdP Blue (Terminal -> Libas) ---
        // GHOST BUS B
        $blueRoute = json_encode([
            [11.559740, 122.751550], // Roxas City Integrated Transport Terminal
            [11.562688, 122.749760], // Pueblo de Panay Commercial Area
            [11.559131, 122.751619], // Security Bank
            [11.565131, 122.748460], // Robinsons Place Roxas
            [11.569007, 122.754693], // KM 1 Lawaan (Hercor College)
            [11.578196, 122.751106], // Railway Street
            [11.577469, 122.753760], // Dancorr
            [11.574372, 122.752699], // Calipay Area
            [11.582685, 122.748931], // Rizal Street
            [11.579953, 122.746979], // Jumbo Bridge
            [11.584748, 122.752331], // Hughes Street (Roxas City Hall)
            [11.584226, 122.746812], // PhilHealth
            [11.584045, 122.746592], // Gov. Hernandez Avenue
            [11.585237, 122.745513], // Capelco
            [11.588753, 122.733599], // Capsu Dayao
            [11.594970, 122.721383], // Brgy. Libas
        ]);

        DB::table('routes')->insertOrIgnore([
            'name' => 'PdP Blue Route',
            'color' => '#0984e3', // Blue
            'path_data' => $blueRoute,
            'created_at' => now(), 'updated_at' => now(),
        ]);

        // --- ROUTE 4: UV Express (Roxas -> Pontevedra) ---
        // GHOST BUS C (New)
        $uvRoute = json_encode([
            [11.583931, 122.762469], // Roxas City Pontevedra UV Transport Cooperative (RCPUVTC)
            [11.584445, 122.769162], // Banica (Roxas)
            [11.581891, 122.786628], // Tanza
            [11.558243, 122.795606], // Poblacion Ilawod
            [11.556060, 122.793917], // Panay (Church)
            [11.554141, 122.790575], // Poblacion Ilaya
            [11.510668, 122.746427], // Brgy Cabugao
            [11.496369, 122.819764], // Agbalo
            [11.456400, 122.827784], // Bailan
            [11.481331, 122.832687], // Pontevedra (Town Proper)
        ]);

        DB::table('routes')->insertOrIgnore([
            'name' => 'RCPUVTC (Pontevedra)',
            'color' => '#e17055', // Burnt Orange
            'path_data' => $uvRoute,
            'created_at' => now(), 'updated_at' => now(),
        ]);
    }
}