const net = require('net');
const mysql = require('mysql');

// 1. DATABASE CONFIGURATION
const db = mysql.createConnection({
    host: '127.0.0.1',
    user: 'root',
    password: '', 
    database: 'ebus_tracking' // Ensure this matches your .env
});

db.connect((err) => {
    if (err) console.error('❌ DB Error:', err);
    else console.log('✅ Connected to Database');
});

// --- HELPER: CONVERT NMEA TO DECIMAL ---
// Input: 1127.7395 -> Output: 11.462325
function nmeaToDecimal(nmea) {
    if (!nmea) return 0;
    
    // The dot separates minutes decimal.
    // The LAST 2 digits before the dot are whole minutes.
    // Everything before that is Degrees.
    const dotIndex = nmea.indexOf('.');
    if (dotIndex === -1) return 0;

    const degrees = parseFloat(nmea.substring(0, dotIndex - 2));
    const minutes = parseFloat(nmea.substring(dotIndex - 2));

    return degrees + (minutes / 60);
}

const server = net.createServer((socket) => {
    console.log('🔌 Device Connected');

    socket.on('data', (data) => {
        // 1. Convert HEX to STRING (Text)
        // Your device sends: *HQ,9176466392,V8,123232,A,1127.7395,N...
        const rawText = data.toString(); 
        
        console.log('📥 RECEIVED:', rawText.trim());

        // 2. CHECK IF IT IS AN *HQ PACKET
        if (rawText.startsWith('*HQ')) {
            try {
                const parts = rawText.split(',');

                // Index 1: Device ID (9176466392)
                const imei = parts[1];

                // Index 4: Validity (A = Valid, V = Void)
                const status = parts[4];

                if (status !== 'A') {
                    console.log(`⚠️ GPS Signal Lost (Status: ${status}) - Waiting for lock...`);
                    return;
                }

                // Index 5 & 7: Coordinates (NMEA Format)
                // 1127.7395 -> Convert to 11.46...
                const latRaw = parts[5];
                const lngRaw = parts[7];

                const lat = nmeaToDecimal(latRaw);
                const lng = nmeaToDecimal(lngRaw);

                console.log(`📍 UPDATE: Bus ${imei} is at ${lat.toFixed(6)}, ${lng.toFixed(6)}`);

                // 3. SURGICAL DATABASE UPDATE
                // Updates location/time but PROTECTS Driver/Route info
                const sql = `
                    UPDATE buses 
                    SET 
                        lat = ?, 
                        lng = ?, 
                        last_seen = NOW(), 
                        updated_at = NOW() 
                    WHERE plate_number = ?
                `;

                db.query(sql, [lat, lng, imei], (err, result) => {
                    if (err) console.error('DB Error:', err);
                    else if (result.affectedRows === 0) {
                        console.log(`⚠️ ID ${imei} not found in DB. Check Admin Panel!`);
                    } else {
                        console.log('✅ Location Saved!');
                    }
                });

            } catch (e) {
                console.error('Parsing Error:', e);
            }
        }
    });

    socket.on('error', (err) => console.error('Socket Error:', err.message));
});

const PORT = 5000;
server.listen(PORT, () => {
    console.log(`🚀 ASCII GPS Server running on Port ${PORT}`);
});