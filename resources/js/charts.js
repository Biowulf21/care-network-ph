// charts.js
// Initializes Chart.js charts using data exposed in DOM data-* attributes.

function parseJSON(v) {
    try {
        return JSON.parse(v);
    } catch (e) {
        return null;
    }
}

function initAdmin() {
    const container = document.getElementById("admin-chart-data");
    if (!container || typeof window.Chart === "undefined") return;

    const traffic = parseJSON(container.dataset.traffic) || [];
    const completion = parseJSON(container.dataset.completion) || [];

    const trafficLabels = traffic.map((t) => t.clinic);
    const trafficData = traffic.map((t) => t.count);

    const tCtx = document.getElementById("adminTrafficChart");
    if (tCtx) {
        const createTrafficChart = () => {
            if (
                window.adminTrafficChart &&
                typeof window.adminTrafficChart.destroy === "function"
            ) {
                try {
                    window.adminTrafficChart.destroy();
                } catch (e) {
                    /* ignore */
                }
                window.adminTrafficChart = null;
            }

            window.adminTrafficChart = new window.Chart(tCtx.getContext("2d"), {
                type: "bar",
                data: {
                    labels: trafficLabels,
                    datasets: [
                        {
                            label: "Today",
                            data: trafficData,
                            backgroundColor: "#3b82f6",
                        },
                    ],
                },
                options: { responsive: true, maintainAspectRatio: false },
            });
        };

        if (
            window.adminTrafficChart &&
            window.adminTrafficChart.data &&
            Array.isArray(window.adminTrafficChart.data.datasets) &&
            window.adminTrafficChart.data.datasets[0]
        ) {
            window.adminTrafficChart.data.labels = trafficLabels;
            window.adminTrafficChart.data.datasets[0].data = trafficData;
            window.adminTrafficChart.update();
        } else {
            createTrafficChart();
        }
    }

    const cCtx = document.getElementById("adminEmarChart");
    const cLabels = completion.map((c) => c.clinic);
    const cData = completion.map((c) => c.rate);
    if (cCtx) {
        const createEmarChart = () => {
            if (
                window.adminEmarChart &&
                typeof window.adminEmarChart.destroy === "function"
            ) {
                try {
                    window.adminEmarChart.destroy();
                } catch (e) {
                    /* ignore */
                }
                window.adminEmarChart = null;
            }

            window.adminEmarChart = new window.Chart(cCtx.getContext("2d"), {
                type: "line",
                data: {
                    labels: cLabels,
                    datasets: [
                        {
                            label: "% Complete",
                            data: cData,
                            borderColor: "#10b981",
                            backgroundColor: "rgba(16,185,129,0.15)",
                            fill: true,
                        },
                    ],
                },
                options: { responsive: true, maintainAspectRatio: false },
            });
        };

        if (
            window.adminEmarChart &&
            window.adminEmarChart.data &&
            Array.isArray(window.adminEmarChart.data.datasets) &&
            window.adminEmarChart.data.datasets[0]
        ) {
            window.adminEmarChart.data.labels = cLabels;
            window.adminEmarChart.data.datasets[0].data = cData;
            window.adminEmarChart.update();
        } else {
            createEmarChart();
        }
    }
}

function initSuperadmin() {
    const container = document.getElementById("superadmin-chart-data");
    if (!container || typeof window.Chart === "undefined") return;

    const labels = parseJSON(container.dataset.labels) || [];
    const counts = parseJSON(container.dataset.counts) || [];
    const emarCompletion = Number(container.dataset.emar) || 0;
    const claims = parseJSON(container.dataset.claims) || {};

    const intakeCtx = document.getElementById("intakeChart");
    if (intakeCtx) {
        const displayLabels = labels.map((d) =>
            new Date(d).toLocaleDateString()
        );
        if (window.intakeChart) {
            window.intakeChart.data.labels = displayLabels;
            window.intakeChart.data.datasets[0].data = counts;
            window.intakeChart.update();
        } else {
            window.intakeChart = new window.Chart(intakeCtx.getContext("2d"), {
                type: "line",
                data: {
                    labels: displayLabels,
                    datasets: [
                        {
                            label: "Patients",
                            data: counts,
                            borderColor: "#3b82f6",
                            backgroundColor: "rgba(59,130,246,0.15)",
                            fill: true,
                        },
                    ],
                },
                options: { responsive: true, maintainAspectRatio: false },
            });
        }
    }

    const emarCtx = document.getElementById("emarCompletionChart");
    if (emarCtx) {
        const completed = emarCompletion;
        const incomplete = Math.max(0, 100 - completed);
        if (window.emarChart) {
            window.emarChart.data.datasets[0].data = [completed, incomplete];
            window.emarChart.update();
        } else {
            window.emarChart = new window.Chart(emarCtx.getContext("2d"), {
                type: "doughnut",
                data: {
                    labels: ["Completed", "Incomplete"],
                    datasets: [
                        {
                            data: [completed, incomplete],
                            backgroundColor: ["#10b981", "#ef4444"],
                        },
                    ],
                },
                options: { responsive: true, maintainAspectRatio: false },
            });
        }
    }

    const claimsCtx = document.getElementById("claimsChart");
    const claimLabels = Object.keys(claims || {});
    const claimData = claimLabels.map((k) => claims[k]);
    if (claimsCtx) {
        if (window.claimsChart) {
            window.claimsChart.data.labels = claimLabels.length
                ? claimLabels
                : ["pending", "approved", "rejected"];
            window.claimsChart.data.datasets[0].data = claimData.length
                ? claimData
                : [0, 0, 0];
            window.claimsChart.update();
        } else {
            window.claimsChart = new window.Chart(claimsCtx.getContext("2d"), {
                type: "bar",
                data: {
                    labels: claimLabels.length
                        ? claimLabels
                        : ["pending", "approved", "rejected"],
                    datasets: [
                        {
                            label: "Claims",
                            data: claimData.length ? claimData : [0, 0, 0],
                            backgroundColor: ["#f59e0b", "#10b981", "#ef4444"],
                        },
                    ],
                },
                options: { responsive: true, maintainAspectRatio: false },
            });
        }
    }
}

function initAll() {
    initAdmin();
    initSuperadmin();
}

// Wire to DOM and Livewire lifecycle
document.addEventListener("DOMContentLoaded", initAll);
if (window.Livewire) {
    document.addEventListener("livewire:load", initAll);
    document.addEventListener("livewire:update", initAll);
    if (window.Livewire.hook) {
        window.Livewire.hook("message.processed", initAll);
    }
}

export { initAll };

// expose for manual debugging from console
if (typeof window !== "undefined") {
    window.initCharts = initAll;
}
