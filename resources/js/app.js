// Frontend entry - expose Chart.js globally so Blade/Livewire views can use it
import Chart from "chart.js/auto";

// expose to window for inline scripts
if (typeof window !== "undefined") {
    window.Chart = Chart;
}

// import chart initializers (reads data from DOM and initializes charts)
import "./charts";

// You can add additional imports here (axios, Alpine, Livewire hooks, etc.)
// Alpine for lightweight interactivity (used by searchable-dropdown)
import Alpine from "alpinejs";
if (typeof window !== "undefined") {
    // Only initialize Alpine if it's not already present (avoids multiple instances warnings)
    if (!window.Alpine) {
        window.Alpine = Alpine;
        Alpine.start();
    } else {
        // ensure we expose the imported Alpine reference if needed
        window.Alpine = window.Alpine || Alpine;
    }
}
