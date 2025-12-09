/**
 * Chart.js Initialization Module
 *
 * This module initializes all charts on the analytics page.
 * It reads data from the #reports-chart-data JSON script tag
 * and creates responsive charts with dark mode support.
 */

document.addEventListener("DOMContentLoaded", function () {
    initializeCharts();
});

// Re-initialize charts after Livewire navigation
document.addEventListener("livewire:navigated", function () {
    initializeCharts();
});

// Re-initialize charts after Livewire component updates (for tab changes)
// This event fires when Livewire updates the DOM (e.g., when changing tabs)
document.addEventListener("livewire:update", function () {
    setTimeout(() => {
        initializeCharts();
    }, 50);
});

// Also listen to the newer Livewire v3 morph events if available
document.addEventListener("livewire:init", function () {
    Livewire.hook("morph.updated", ({ el, component }) => {
        // Only reinitialize if this is the reports component
        if (document.getElementById("reports-chart-data")) {
            setTimeout(() => {
                initializeCharts();
            }, 50);
        }
    });
});

function initializeCharts() {
    // Read chart data from the JSON script tag
    const chartDataElement = document.getElementById("reports-chart-data");
    if (!chartDataElement) return;

    let chartData;
    try {
        chartData = JSON.parse(chartDataElement.textContent);
    } catch (e) {
        console.error("Failed to parse chart data:", e);
        return;
    }

    // Check which report type is active
    const reportType = chartData.reportType;

    // Initialize charts based on report type
    if (reportType === "overview") {
        initAppointmentStatusChart(chartData.appointmentByStatus);
        initPatientAgeChart(chartData.patientAgeGroups);
    } else if (reportType === "patients") {
        initGenderChart(chartData.patientByGender);
        initNewRegistrationsChart(chartData.newRegistrations);
    } else if (reportType === "appointments") {
        initAppointmentStatusChart2(chartData.appointmentByStatus);
        initMonthlyTrendsChart(chartData.appointmentByMonth);
    } else if (reportType === "medical_records") {
        initRecordsMonthlyChart(chartData.medicalRecordsByMonth);
    }
}

/**
 * Get chart colors based on dark mode
 */
function getChartColors() {
    const isDarkMode = document.documentElement.classList.contains("dark");

    return {
        text: isDarkMode ? "#e5e7eb" : "#374151",
        grid: isDarkMode ? "#374151" : "#e5e7eb",
        background: isDarkMode ? "#1f2937" : "#ffffff",
        colors: [
            "#3b82f6", // blue
            "#10b981", // green
            "#f59e0b", // yellow
            "#ef4444", // red
            "#8b5cf6", // purple
            "#ec4899", // pink
            "#06b6d4", // cyan
            "#f97316", // orange
        ],
    };
}

/**
 * Get default chart options with dark mode support
 */
function getDefaultOptions(additionalOptions = {}) {
    const colors = getChartColors();

    return {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
            legend: {
                labels: {
                    color: colors.text,
                    font: {
                        family: "Inter, system-ui, sans-serif",
                    },
                },
            },
            tooltip: {
                backgroundColor: colors.background,
                titleColor: colors.text,
                bodyColor: colors.text,
                borderColor: colors.grid,
                borderWidth: 1,
            },
        },
        ...additionalOptions,
    };
}

/**
 * Destroy existing chart instance if it exists
 */
function destroyChart(chartId) {
    const canvas = document.getElementById(chartId);
    if (!canvas) return;

    const existingChart = Chart.getChart(canvas);
    if (existingChart) {
        existingChart.destroy();
    }
}

/**
 * Initialize Appointment Status Chart (Overview)
 */
function initAppointmentStatusChart(data) {
    const chartId = "appointmentStatusChart";
    destroyChart(chartId);

    const canvas = document.getElementById(chartId);
    if (!canvas || !data || Object.keys(data).length === 0) return;

    const colors = getChartColors();
    const labels = Object.keys(data);
    const values = Object.values(data);

    new Chart(canvas, {
        type: "doughnut",
        data: {
            labels: labels.map(
                (label) => label.charAt(0).toUpperCase() + label.slice(1)
            ),
            datasets: [
                {
                    data: values,
                    backgroundColor: colors.colors,
                    borderWidth: 2,
                    borderColor: colors.background,
                },
            ],
        },
        options: getDefaultOptions({
            plugins: {
                legend: {
                    position: "bottom",
                    labels: {
                        color: colors.text,
                        padding: 15,
                        font: {
                            family: "Inter, system-ui, sans-serif",
                        },
                    },
                },
            },
        }),
    });
}

/**
 * Initialize Patient Age Chart
 */
function initPatientAgeChart(data) {
    const chartId = "patientAgeChart";
    destroyChart(chartId);

    const canvas = document.getElementById(chartId);
    if (!canvas || !data || Object.keys(data).length === 0) return;

    const colors = getChartColors();
    const labels = Object.keys(data);
    const values = Object.values(data);

    new Chart(canvas, {
        type: "bar",
        data: {
            labels: labels,
            datasets: [
                {
                    label: "Patients",
                    data: values,
                    backgroundColor: colors.colors[0],
                    borderColor: colors.colors[0],
                    borderWidth: 1,
                },
            ],
        },
        options: getDefaultOptions({
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        color: colors.text,
                        stepSize: 1,
                    },
                    grid: {
                        color: colors.grid,
                    },
                },
                x: {
                    ticks: {
                        color: colors.text,
                    },
                    grid: {
                        color: colors.grid,
                    },
                },
            },
            plugins: {
                legend: {
                    display: false,
                },
            },
        }),
    });
}

/**
 * Initialize Gender Chart
 */
function initGenderChart(data) {
    const chartId = "genderChart";
    destroyChart(chartId);

    const canvas = document.getElementById(chartId);
    if (!canvas || !data || Object.keys(data).length === 0) return;

    const colors = getChartColors();
    const labels = Object.keys(data);
    const values = Object.values(data);

    new Chart(canvas, {
        type: "pie",
        data: {
            labels: labels.map(
                (label) => label.charAt(0).toUpperCase() + label.slice(1)
            ),
            datasets: [
                {
                    data: values,
                    backgroundColor: colors.colors.slice(0, labels.length),
                    borderWidth: 2,
                    borderColor: colors.background,
                },
            ],
        },
        options: getDefaultOptions({
            plugins: {
                legend: {
                    position: "bottom",
                    labels: {
                        color: colors.text,
                        padding: 15,
                        font: {
                            family: "Inter, system-ui, sans-serif",
                        },
                    },
                },
            },
        }),
    });
}

/**
 * Initialize New Registrations Chart
 */
function initNewRegistrationsChart(data) {
    const chartId = "newRegistrationsChart";
    destroyChart(chartId);

    const canvas = document.getElementById(chartId);
    if (!canvas || !data || Object.keys(data).length === 0) return;

    const colors = getChartColors();

    // Sort data by date
    const sortedEntries = Object.entries(data).sort((a, b) =>
        a[0].localeCompare(b[0])
    );
    const labels = sortedEntries.map(([date]) => {
        const d = new Date(date);
        return d.toLocaleDateString("en-US", {
            month: "short",
            day: "numeric",
        });
    });
    const values = sortedEntries.map(([, count]) => count);

    new Chart(canvas, {
        type: "line",
        data: {
            labels: labels,
            datasets: [
                {
                    label: "New Registrations",
                    data: values,
                    borderColor: colors.colors[0],
                    backgroundColor: colors.colors[0] + "33",
                    borderWidth: 2,
                    fill: true,
                    tension: 0.4,
                },
            ],
        },
        options: getDefaultOptions({
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        color: colors.text,
                        stepSize: 1,
                    },
                    grid: {
                        color: colors.grid,
                    },
                },
                x: {
                    ticks: {
                        color: colors.text,
                        maxRotation: 45,
                        minRotation: 45,
                    },
                    grid: {
                        color: colors.grid,
                    },
                },
            },
        }),
    });
}

/**
 * Initialize Appointment Status Chart 2 (Appointments page)
 */
function initAppointmentStatusChart2(data) {
    const chartId = "appointmentStatusChart2";
    destroyChart(chartId);

    const canvas = document.getElementById(chartId);
    if (!canvas || !data || Object.keys(data).length === 0) return;

    const colors = getChartColors();
    const labels = Object.keys(data);
    const values = Object.values(data);

    new Chart(canvas, {
        type: "doughnut",
        data: {
            labels: labels.map(
                (label) => label.charAt(0).toUpperCase() + label.slice(1)
            ),
            datasets: [
                {
                    data: values,
                    backgroundColor: colors.colors,
                    borderWidth: 2,
                    borderColor: colors.background,
                },
            ],
        },
        options: getDefaultOptions({
            plugins: {
                legend: {
                    position: "bottom",
                    labels: {
                        color: colors.text,
                        padding: 15,
                        font: {
                            family: "Inter, system-ui, sans-serif",
                        },
                    },
                },
            },
        }),
    });
}

/**
 * Initialize Monthly Trends Chart
 */
function initMonthlyTrendsChart(data) {
    const chartId = "monthlyTrendsChart";
    destroyChart(chartId);

    const canvas = document.getElementById(chartId);
    if (!canvas || !data || Object.keys(data).length === 0) return;

    const colors = getChartColors();

    // Sort data by month
    const sortedEntries = Object.entries(data).sort((a, b) =>
        a[0].localeCompare(b[0])
    );
    const labels = sortedEntries.map(([month]) => {
        const [year, monthNum] = month.split("-");
        const date = new Date(year, monthNum - 1);
        return date.toLocaleDateString("en-US", {
            year: "numeric",
            month: "short",
        });
    });
    const values = sortedEntries.map(([, count]) => count);

    new Chart(canvas, {
        type: "line",
        data: {
            labels: labels,
            datasets: [
                {
                    label: "Appointments",
                    data: values,
                    borderColor: colors.colors[1],
                    backgroundColor: colors.colors[1] + "33",
                    borderWidth: 3,
                    fill: true,
                    tension: 0.4,
                    pointRadius: 4,
                    pointHoverRadius: 6,
                },
            ],
        },
        options: getDefaultOptions({
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        color: colors.text,
                        stepSize: 1,
                    },
                    grid: {
                        color: colors.grid,
                    },
                },
                x: {
                    ticks: {
                        color: colors.text,
                    },
                    grid: {
                        color: colors.grid,
                    },
                },
            },
        }),
    });
}

/**
 * Initialize Records Monthly Chart
 */
function initRecordsMonthlyChart(data) {
    const chartId = "recordsMonthlyChart";
    destroyChart(chartId);

    const canvas = document.getElementById(chartId);
    if (!canvas || !data || Object.keys(data).length === 0) return;

    const colors = getChartColors();

    // Sort data by month
    const sortedEntries = Object.entries(data).sort((a, b) =>
        a[0].localeCompare(b[0])
    );
    const labels = sortedEntries.map(([month]) => {
        const [year, monthNum] = month.split("-");
        const date = new Date(year, monthNum - 1);
        return date.toLocaleDateString("en-US", {
            year: "numeric",
            month: "short",
        });
    });
    const values = sortedEntries.map(([, count]) => count);

    new Chart(canvas, {
        type: "line",
        data: {
            labels: labels,
            datasets: [
                {
                    label: "Consultations",
                    data: values,
                    borderColor: colors.colors[4],
                    backgroundColor: colors.colors[4] + "33",
                    borderWidth: 3,
                    fill: true,
                    tension: 0.4,
                    pointRadius: 4,
                    pointHoverRadius: 6,
                },
            ],
        },
        options: getDefaultOptions({
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        color: colors.text,
                        stepSize: 1,
                    },
                    grid: {
                        color: colors.grid,
                    },
                },
                x: {
                    ticks: {
                        color: colors.text,
                    },
                    grid: {
                        color: colors.grid,
                    },
                },
            },
        }),
    });
}
