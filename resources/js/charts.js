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

// Attach a general Livewire hook once loaded to re-run chart initialization after every message
document.addEventListener("livewire:load", function () {
    if (typeof Livewire !== "undefined" && Livewire.hook) {
        try {
            Livewire.hook("message.processed", (message, component) => {
                // Small delay so DOM updates settle
                setTimeout(() => {
                    initializeCharts();
                }, 50);
            });
        } catch (e) {
            // ignore if hook not available
            console.warn("Livewire message.processed hook not available", e);
        }
    }
});

function initializeCharts() {
    // Check for analytics page charts
    const chartDataElement = document.getElementById("reports-chart-data");
    // Ensure Chart.js global theme is applied for current (light/dark) mode
    applyChartTheme();
    if (chartDataElement) {
        initAnalyticsCharts(chartDataElement);
    }

    // Check for admin dashboard charts
    const adminChartData = document.getElementById("admin-chart-data");
    if (adminChartData) {
        initAdminDashboardCharts(adminChartData);
    }

    // Check for superadmin dashboard charts
    const superadminChartData = document.getElementById(
        "superadmin-chart-data"
    );
    if (superadminChartData) {
        initSuperadminDashboardCharts(superadminChartData);
    }
}

function initAnalyticsCharts(chartDataElement) {
    let chartData;
    try {
        chartData = JSON.parse(chartDataElement.textContent);
    } catch (e) {
        console.error("Failed to parse chart data:", e);
        return;
    }

    // Initialize any charts for which a canvas exists. This is more robust
    // than relying solely on `reportType` because Livewire DOM morphs can
    // sometimes leave the JSON and canvases briefly out of sync.

    // Helper: attempt init for a canvas id with provided initializer and data
    const tryInit = (canvasId, initFn, data) => {
        const el = document.getElementById(canvasId);
        if (!el) return false;
        try {
            initFn(data);
        } catch (e) {
            console.error(`Failed to init chart ${canvasId}:`, e);
        }
        return true;
    };

    // Track attempts to avoid busy loops on transient states
    chartDataElement._initAttempts = chartDataElement._initAttempts || 0;

    const didAny = [];

    // Overview charts
    didAny.push(
        tryInit(
            "appointmentStatusChart",
            initAppointmentStatusChart,
            chartData.appointmentByStatus
        )
    );
    didAny.push(
        tryInit(
            "patientAgeChart",
            initPatientAgeChart,
            chartData.patientAgeGroups
        )
    );

    // Patients
    didAny.push(
        tryInit("genderChart", initGenderChart, chartData.patientByGender)
    );
    didAny.push(
        tryInit(
            "newRegistrationsChart",
            initNewRegistrationsChart,
            chartData.newRegistrations
        )
    );

    // Appointments
    didAny.push(
        tryInit(
            "appointmentStatusChart2",
            initAppointmentStatusChart2,
            chartData.appointmentByStatus
        )
    );
    didAny.push(
        tryInit(
            "monthlyTrendsChart",
            initMonthlyTrendsChart,
            chartData.appointmentByMonth
        )
    );

    // Medical records
    didAny.push(
        tryInit(
            "recordsMonthlyChart",
            initRecordsMonthlyChart,
            chartData.medicalRecordsByMonth
        )
    );

    const anyPresentNow = didAny.some(Boolean);

    if (!anyPresentNow && chartDataElement._initAttempts < 6) {
        chartDataElement._initAttempts += 1;
        // retry with backoff
        const delay =
            [100, 150, 250, 400, 600, 800][
                chartDataElement._initAttempts - 1
            ] || 800;
        setTimeout(() => initAnalyticsCharts(chartDataElement), delay);
        return;
    }

    // Reset attempts after successful init or exhausted retries
    chartDataElement._initAttempts = 0;
}

/**
 * Get chart colors based on dark mode
 */
function getChartColors() {
    const isDarkMode = document.documentElement.classList.contains("dark");

    return {
        text: isDarkMode ? "#e5e7eb" : "#000000",
        grid: isDarkMode ? "#374151" : "#ffffff",
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

// Apply chart.js-wide theme defaults so all charts inherit correct colors
function applyChartTheme() {
    const colors = getChartColors();

    if (typeof Chart === 'undefined') return;

    // Global default text color and font
    Chart.defaults.color = colors.text;
    Chart.defaults.font = Chart.defaults.font || {};
    Chart.defaults.font.family = Chart.defaults.font.family || 'Inter, system-ui, sans-serif';

    // Legend label color
    Chart.defaults.plugins = Chart.defaults.plugins || {};
    Chart.defaults.plugins.legend = Chart.defaults.plugins.legend || {};
    Chart.defaults.plugins.legend.labels = Chart.defaults.plugins.legend.labels || {};
    Chart.defaults.plugins.legend.labels.color = colors.text;

    // Tooltip colors
    Chart.defaults.plugins.tooltip = Chart.defaults.plugins.tooltip || {};
    Chart.defaults.plugins.tooltip.backgroundColor = colors.background;
    Chart.defaults.plugins.tooltip.titleColor = colors.text;
    Chart.defaults.plugins.tooltip.bodyColor = colors.text;
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

/* ========================================
 * DASHBOARD CHART INITIALIZATION FUNCTIONS
 * ======================================== */

function initAdminDashboardCharts(dataElement) {
    try {
        const traffic = JSON.parse(dataElement.dataset.traffic || "[]");
        const completion = JSON.parse(dataElement.dataset.completion || "[]");
        const gender = JSON.parse(dataElement.dataset.gender || "{}");
        const newPatients = JSON.parse(dataElement.dataset.newPatients || "{}");
        const appointmentStatus = JSON.parse(
            dataElement.dataset.appointmentStatus || "{}"
        );
        const monthlyAppointments = JSON.parse(
            dataElement.dataset.monthlyAppointments || "{}"
        );
        const monthlyConsultations = JSON.parse(
            dataElement.dataset.monthlyConsultations || "{}"
        );

        initAdminTrafficChart(traffic);
        initAdminEmarChart(completion);
        initAdminGenderChart(gender);
        initAdminNewPatientsChart(newPatients);
        initAdminAppointmentStatusChart(appointmentStatus);
        initAdminMonthlyAppointmentsChart(monthlyAppointments);
        initAdminMonthlyConsultationsChart(monthlyConsultations);
    } catch (e) {
        console.error("Failed to initialize admin dashboard charts:", e);
    }
}

function initSuperadminDashboardCharts(dataElement) {
    try {
        const labels = JSON.parse(dataElement.dataset.labels || "[]");
        const counts = JSON.parse(dataElement.dataset.counts || "[]");
        const emarCompletion = parseFloat(dataElement.dataset.emar || "0");
        const claims = JSON.parse(dataElement.dataset.claims || "{}");

        initSuperadminIntakeChart(labels, counts);
        initSuperadminEmarChart(emarCompletion);
        initSuperadminClaimsChart(claims);
    } catch (e) {
        console.error("Failed to initialize superadmin dashboard charts:", e);
    }
}

/* Admin charts */
function initAdminTrafficChart(data) {
    const chartId = "adminTrafficChart";
    destroyChart(chartId);
    const canvas = document.getElementById(chartId);
    if (!canvas || !data || data.length === 0) return;

    const colors = getChartColors();
    const labels = data.map((i) => i.clinic);
    const values = data.map((i) => i.count);

    new Chart(canvas, {
        type: "bar",
        data: {
            labels,
            datasets: [
                {
                    label: "Patients Today",
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
                    ticks: { color: colors.text, stepSize: 1 },
                    grid: { color: colors.grid },
                },
                x: {
                    ticks: { color: colors.text },
                    grid: { color: colors.grid },
                },
            },
            plugins: { legend: { display: false } },
        }),
    });
}

function initAdminEmarChart(data) {
    const chartId = "adminEmarChart";
    destroyChart(chartId);
    const canvas = document.getElementById(chartId);
    if (!canvas || !data || data.length === 0) return;

    const colors = getChartColors();
    const labels = data.map((i) => i.clinic);
    const values = data.map((i) => i.rate);

    new Chart(canvas, {
        type: "bar",
        data: {
            labels,
            datasets: [
                {
                    label: "Completion Rate (%)",
                    data: values,
                    backgroundColor: colors.colors[1],
                    borderColor: colors.colors[1],
                    borderWidth: 1,
                },
            ],
        },
        options: getDefaultOptions({
            scales: {
                y: {
                    beginAtZero: true,
                    max: 100,
                    ticks: { color: colors.text, callback: (v) => v + "%" },
                    grid: { color: colors.grid },
                },
                x: {
                    ticks: { color: colors.text },
                    grid: { color: colors.grid },
                },
            },
            plugins: { legend: { display: false } },
        }),
    });
}

function initAdminGenderChart(data) {
    const chartId = "adminGenderChart";
    destroyChart(chartId);
    const canvas = document.getElementById(chartId);
    if (!canvas || !data || Object.keys(data).length === 0) return;

    const colors = getChartColors();
    const labels = Object.keys(data);
    const values = Object.values(data);

    new Chart(canvas, {
        type: "pie",
        data: {
            labels: labels.map((l) => l.charAt(0).toUpperCase() + l.slice(1)),
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
                    labels: { color: colors.text, padding: 15 },
                },
            },
        }),
    });
}

function initAdminNewPatientsChart(data) {
    const chartId = "adminNewPatientsChart";
    destroyChart(chartId);
    const canvas = document.getElementById(chartId);
    if (!canvas || !data || Object.keys(data).length === 0) return;

    const colors = getChartColors();
    const sorted = Object.entries(data).sort((a, b) =>
        a[0].localeCompare(b[0])
    );
    const labels = sorted.map(([d]) =>
        new Date(d).toLocaleDateString("en-US", {
            month: "short",
            day: "numeric",
        })
    );
    const values = sorted.map(([, v]) => v);

    new Chart(canvas, {
        type: "line",
        data: {
            labels,
            datasets: [
                {
                    label: "New Patients",
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
                    ticks: { color: colors.text, stepSize: 1 },
                    grid: { color: colors.grid },
                },
                x: {
                    ticks: {
                        color: colors.text,
                        maxRotation: 45,
                        minRotation: 45,
                    },
                    grid: { color: colors.grid },
                },
            },
        }),
    });
}

function initAdminAppointmentStatusChart(data) {
    const chartId = "adminAppointmentStatusChart";
    destroyChart(chartId);
    const canvas = document.getElementById(chartId);
    if (!canvas || !data || Object.keys(data).length === 0) return;

    const colors = getChartColors();
    const labels = Object.keys(data);
    const values = Object.values(data);

    new Chart(canvas, {
        type: "doughnut",
        data: {
            labels: labels.map((l) => l.charAt(0).toUpperCase() + l.slice(1)),
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
                    labels: { color: colors.text, padding: 15 },
                },
            },
        }),
    });
}

function initAdminMonthlyAppointmentsChart(data) {
    const chartId = "adminMonthlyAppointmentsChart";
    destroyChart(chartId);
    const canvas = document.getElementById(chartId);
    if (!canvas || !data || Object.keys(data).length === 0) return;

    const colors = getChartColors();
    const sorted = Object.entries(data).sort((a, b) =>
        a[0].localeCompare(b[0])
    );
    const labels = sorted.map(([m]) => {
        const [y, mm] = m.split("-");
        return new Date(y, mm - 1).toLocaleDateString("en-US", {
            year: "numeric",
            month: "short",
        });
    });
    const values = sorted.map(([, v]) => v);

    new Chart(canvas, {
        type: "line",
        data: {
            labels,
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
                    ticks: { color: colors.text, stepSize: 1 },
                    grid: { color: colors.grid },
                },
                x: {
                    ticks: { color: colors.text },
                    grid: { color: colors.grid },
                },
            },
        }),
    });
}

function initAdminMonthlyConsultationsChart(data) {
    const chartId = "adminMonthlyConsultationsChart";
    destroyChart(chartId);
    const canvas = document.getElementById(chartId);
    if (!canvas || !data || Object.keys(data).length === 0) return;

    const colors = getChartColors();
    const sorted = Object.entries(data).sort((a, b) =>
        a[0].localeCompare(b[0])
    );
    const labels = sorted.map(([m]) => {
        const [y, mm] = m.split("-");
        return new Date(y, mm - 1).toLocaleDateString("en-US", {
            year: "numeric",
            month: "short",
        });
    });
    const values = sorted.map(([, v]) => v);

    new Chart(canvas, {
        type: "line",
        data: {
            labels,
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
                    ticks: { color: colors.text, stepSize: 1 },
                    grid: { color: colors.grid },
                },
                x: {
                    ticks: { color: colors.text },
                    grid: { color: colors.grid },
                },
            },
        }),
    });
}

/* Superadmin charts */
function initSuperadminIntakeChart(labels, counts) {
    const chartId = "superadminIntakeChart";
    destroyChart(chartId);
    const canvas = document.getElementById(chartId);
    if (!canvas || !labels || labels.length === 0) return;

    const colors = getChartColors();
    const formatted = labels.map((d) =>
        new Date(d).toLocaleDateString("en-US", {
            month: "short",
            day: "numeric",
        })
    );

    new Chart(canvas, {
        type: "line",
        data: {
            labels: formatted,
            datasets: [
                {
                    label: "Patient Intake",
                    data: counts,
                    borderColor: colors.colors[0],
                    backgroundColor: colors.colors[0] + "33",
                    borderWidth: 3,
                    fill: true,
                    tension: 0.4,
                    pointRadius: 3,
                    pointHoverRadius: 5,
                },
            ],
        },
        options: getDefaultOptions({
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: { color: colors.text, stepSize: 1 },
                    grid: { color: colors.grid },
                },
                x: {
                    ticks: {
                        color: colors.text,
                        maxRotation: 45,
                        minRotation: 45,
                    },
                    grid: { color: colors.grid },
                },
            },
        }),
    });
}

function initSuperadminEmarChart(completionRate) {
    const chartId = "superadminEmarChart";
    destroyChart(chartId);
    const canvas = document.getElementById(chartId);
    if (!canvas) return;

    const colors = getChartColors();

    new Chart(canvas, {
        type: "doughnut",
        data: {
            labels: ["Completed", "Incomplete"],
            datasets: [
                {
                    data: [completionRate, 100 - completionRate],
                    backgroundColor: [colors.colors[1], colors.grid],
                    borderWidth: 2,
                    borderColor: colors.background,
                },
            ],
        },
        options: getDefaultOptions({
            plugins: {
                legend: {
                    position: "bottom",
                    labels: { color: colors.text, padding: 15 },
                },
            },
        }),
    });
}

function initSuperadminClaimsChart(data) {
    const chartId = "superadminClaimsChart";
    destroyChart(chartId);
    const canvas = document.getElementById(chartId);
    if (!canvas || !data || Object.keys(data).length === 0) return;

    const colors = getChartColors();
    const labels = Object.keys(data);
    const values = Object.values(data);

    new Chart(canvas, {
        type: "bar",
        data: {
            labels: labels.map((l) => l.charAt(0).toUpperCase() + l.slice(1)),
            datasets: [
                {
                    label: "Claims",
                    data: values,
                    backgroundColor: colors.colors[2],
                    borderColor: colors.colors[2],
                    borderWidth: 1,
                },
            ],
        },
        options: getDefaultOptions({
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: { color: colors.text, stepSize: 1 },
                    grid: { color: colors.grid },
                },
                x: {
                    ticks: { color: colors.text },
                    grid: { color: colors.grid },
                },
            },
            plugins: { legend: { display: false } },
        }),
    });
}
