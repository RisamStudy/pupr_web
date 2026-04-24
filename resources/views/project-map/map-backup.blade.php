<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Tracking Kendaraan Dummy</title>

  <!-- Tailwind -->
  <script src="https://cdn.tailwindcss.com"></script>

  <!-- Leaflet -->
  <link rel="stylesheet" href="https://unpkg.com/leaflet/dist/leaflet.css"/>
  <script src="https://unpkg.com/leaflet/dist/leaflet.js"></script>

  <style>
    html, body { height: 100%; }
    #map { height: 100%; width: 100%; }
  </style>
</head>
<body class="bg-gray-100">

  <!-- Header -->
  <header class="bg-blue-700 text-white shadow p-4 flex items-center justify-between">
    <h1 class="text-xl font-bold">🚗 Tracking Kendaraan Dinas PUPR (Dummy)</h1>
    <span class="text-sm">Update tiap 5 detik</span>
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
      <div id="map"></div>
    </main>
  </div>

  <script>
    // Inisialisasi Map
    const map = L.map('map').setView([-6.200000, 106.816666], 13);
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
      maxZoom: 19,
    }).addTo(map);

    // Custom icon mobil
    const carRed = L.icon({
      iconUrl: "https://cdn-icons-png.flaticon.com/512/61/61168.png",
      iconSize: [32, 32],
      iconAnchor: [16, 16],
      popupAnchor: [0, -16]
    });
    const carGreen = L.icon({
      iconUrl: "https://cdn-icons-png.flaticon.com/512/3097/3097144.png",
      iconSize: [32, 32],
      iconAnchor: [16, 16],
      popupAnchor: [0, -16]
    });
    const carBlue = L.icon({
      iconUrl: "https://cdn-icons-png.flaticon.com/512/743/743922.png",
      iconSize: [32, 32],
      iconAnchor: [16, 16],
      popupAnchor: [0, -16]
    });

    // Dummy data kendaraan
    const vehicles = [
      { id: 1, name: "Mobil Dinas 1", lat: -6.21, lon: 106.81, color: "red", icon: carRed, path: [] },
      { id: 2, name: "Mobil Dinas 2", lat: -6.19, lon: 106.82, color: "green", icon: carGreen, path: [] },
      { id: 3, name: "Mobil Dinas 3", lat: -6.20, lon: 106.84, color: "blue", icon: carBlue, path: [] }
    ];

    const markers = {};
    const polylines = {};

    const listEl = document.getElementById("vehicle-list");

    // Render awal
    function renderMarkers() {
      listEl.innerHTML = "";
      vehicles.forEach(v => {
        v.path.push([v.lat, v.lon]);

        markers[v.id] = L.marker([v.lat, v.lon], { icon: v.icon }).addTo(map)
          .bindPopup(`<b>${v.name}</b><br>Lat: ${v.lat}<br>Lon: ${v.lon}`);

        polylines[v.id] = L.polyline(v.path, { color: v.color }).addTo(map);

        const li = document.createElement("li");
        li.className = "p-3 rounded border bg-gray-50 hover:bg-gray-100 cursor-pointer";
        li.innerHTML = `<div class="font-semibold">${v.name}</div>
                        <div class="text-xs text-gray-600">Lat: ${v.lat.toFixed(5)} | Lon: ${v.lon.toFixed(5)}</div>`;
        li.onclick = () => map.setView([v.lat, v.lon], 15);
        li.dataset.id = v.id;
        listEl.appendChild(li);
      });
    }

    // Update posisi dummy
    function updateMarkers() {
      vehicles.forEach(v => {
        v.lat += (Math.random() - 0.5) * 0.002;
        v.lon += (Math.random() - 0.5) * 0.002;

        markers[v.id].setLatLng([v.lat, v.lon])
          .setPopupContent(`<b>${v.name}</b><br>Lat: ${v.lat.toFixed(5)}<br>Lon: ${v.lon.toFixed(5)}`);

        v.path.push([v.lat, v.lon]);
        polylines[v.id].setLatLngs(v.path);

        const li = listEl.querySelector(`[data-id="${v.id}"]`);
        if (li) {
          li.querySelector(".text-gray-600").textContent =
            `Lat: ${v.lat.toFixed(5)} | Lon: ${v.lon.toFixed(5)}`;
        }
      });
    }

    renderMarkers();
    setInterval(updateMarkers, 5000); // update tiap 5 detik
  </script>
</body>
</html>
