<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Tracking Kendaraan PUPR</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <!-- Tailwind -->
    <script src="https://cdn.tailwindcss.com"></script>
    <!-- Leaflet -->
    <link rel="stylesheet" href="https://unpkg.com/leaflet/dist/leaflet.css" />
    <script src="https://unpkg.com/leaflet/dist/leaflet.js"></script>
</head>

<body class="bg-gray-100">

    <!-- Header -->
    <header class="bg-blue-700 text-white shadow p-4 flex items-center justify-between">
        <h1 class="text-xl font-bold">🚗 Tracking Kendaraan Dinas PUPR</h1>
        <span class="text-sm">Update realtime dari DB</span>
    </header>

    <!-- Layout -->
    <div class="flex h-[calc(100vh-64px)]">
        <!-- Sidebar -->
        <aside class="w-80 bg-white border-r p-4 overflow-y-auto">
            <h2 class="text-lg font-semibold mb-4">Daftar Kendaraan</h2>
            <ul id="vehicle-list" class="space-y-3"></ul>
        </aside>

        <!-- Map -->
        <main class="flex-1">
            <div id="map" class="w-full h-full"></div>
        </main>
    </div>

    <script>
        const map = L.map('map').setView([-6.889836, 107.640471], 9);
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            maxZoom: 19,
        }).addTo(map);

        // ikon mobil
        const carIcon = L.icon({
            iconUrl: "https://cdn-icons-png.flaticon.com/512/61/61168.png",
            iconSize: [32, 32],
            iconAnchor: [16, 16],
            popupAnchor: [0, -16]
        });

        const markers = {};
        const polylines = {};

        function getColorForVehicle(vehicleId) {
            // daftar warna tetap biar konsisten (maks 10 kendaraan, sisanya random)
            const colors = [
                "blue", "red", "green", "orange", "purple",
                "brown", "pink", "black", "teal", "cyan"
            ];
            const index = vehicleId % colors.length;
            return colors[index];
        }

        async function loadData() {
            const res = await fetch('/tracking/data');
            const vehicles = await res.json();

            const listEl = document.getElementById("vehicle-list");
            listEl.innerHTML = "";

            vehicles.forEach(v => {
                if (!v.positions || v.positions.length === 0) return;

                // ambil trip terakhir
                const last = v.positions[v.positions.length - 1];
                const lastLat = parseFloat(last.end_latitude ?? last.start_latitude);
                const lastLon = parseFloat(last.end_longitude ?? last.start_longitude);

                // marker
                if (!markers[v.vehicle_id]) {
                    markers[v.vehicle_id] = L.marker([lastLat, lastLon], {
                            icon: carIcon
                        })
                        .addTo(map)
                        .bindPopup(`<b>${v.registration}</b><br>Lat: ${lastLat}<br>Lon: ${lastLon}`);
                } else {
                    markers[v.vehicle_id].setLatLng([lastLat, lastLon])
                        .setPopupContent(`<b>${v.registration}</b><br>Lat: ${lastLat}<br>Lon: ${lastLon}`);
                }

                // polyline (riwayat perjalanan berdasarkan start & end setiap trip)
                const coords = [];
                v.positions.forEach(p => {
                    if (p.start_latitude && p.start_longitude) {
                        coords.push([parseFloat(p.start_latitude), parseFloat(p.start_longitude)]);
                    }
                    if (p.end_latitude && p.end_longitude) {
                        coords.push([parseFloat(p.end_latitude), parseFloat(p.end_longitude)]);
                    }
                });

                if (coords.length > 0) {
                    const color = getColorForVehicle(v.vehicle_id);
                    if (!polylines[v.vehicle_id]) {
                        polylines[v.vehicle_id] = L.polyline(coords, {
                            color: color,
                            weight: 3
                        }).addTo(map);
                    } else {
                        polylines[v.vehicle_id].setLatLngs(coords);
                        polylines[v.vehicle_id].setStyle({
                            color: color
                        });
                    }
                }

                // sidebar list
                const li = document.createElement("li");
                li.className = "p-3 rounded border bg-gray-50 hover:bg-gray-100 cursor-pointer";
                li.innerHTML = `<div class="font-semibold">${v.registration}</div>
                    <div class="text-xs text-gray-600">Lat: ${lastLat.toFixed(5)} | Lon: ${lastLon.toFixed(5)}</div>`;
                li.onclick = () => map.setView([lastLat, lastLon], 15);
                listEl.appendChild(li);
            });
        }


        loadData();
        setInterval(loadData, 5000); // refresh tiap 5 detik
    </script>
</body>

</html>
