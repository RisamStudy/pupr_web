import "./bootstrap";
import Alpine from "alpinejs";
import flatpickr from "flatpickr";
import Chart from "chart.js/auto";
import L from "leaflet";
import "leaflet/dist/leaflet.css";

// import "flatpickr/dist/flatpickr.min.css";

window.flatpickr = flatpickr;

window.Chart = Chart;

window.Alpine = Alpine;

window.L = L;

Alpine.start();
