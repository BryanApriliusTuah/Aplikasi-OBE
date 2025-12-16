/**
 * Modern Chart Component
 * A reusable chart component for creating beautiful, modern charts
 *
 * Dependencies:
 * - Chart.js
 * - ChartDataLabels plugin
 *
 * @author OBE System
 * @version 1.0.0
 */

class ModernChartComponent {
	/**
	 * Create a new ModernChartComponent instance
	 * @param {Object} options - Configuration options
	 * @param {string} options.containerId - ID of the container element
	 * @param {Object} options.chartData - Chart data {labels: [], data: [], details: []}
	 * @param {Object} options.config - Chart configuration
	 */
	constructor(options = {}) {
		this.containerId = options.containerId;
		this.chartData = options.chartData;
		this.config = {
			title: options.config?.title || "Chart Title",
			type: options.config?.type || "bar",
			passingThreshold: options.config?.passingThreshold || 65,
			showExportButton: options.config?.showExportButton !== false,
			showSubtitle: options.config?.showSubtitle !== false,
			subtitle:
				options.config?.subtitle ||
				"Visualisasi data capaian pembelajaran",
			exportFilename:
				options.config?.exportFilename || "chart-export.png",
			height: options.config?.height || 80,
			animationDuration: options.config?.animationDuration || 1000,
			colors: {
				success:
					options.config?.colors?.success ||
					"rgba(13, 110, 253, 0.9)",
				successLight:
					options.config?.colors?.successLight ||
					"rgba(13, 110, 253, 0.6)",
				danger:
					options.config?.colors?.danger || "rgba(220, 53, 69, 0.9)",
				dangerLight:
					options.config?.colors?.dangerLight ||
					"rgba(220, 53, 69, 0.6)",
			},
			labels: {
				yAxis:
					options.config?.labels?.yAxis || "Persentase Capaian (%)",
				xAxis: options.config?.labels?.xAxis || "Kode CPMK",
			},
		};
		this.chart = null;
		this.canvasId = `chart-${this.containerId}-${Date.now()}`;
	}

	/**
	 * Render the chart
	 */
	render() {
		const container = document.getElementById(this.containerId);
		if (!container) {
			console.error(`Container with ID '${this.containerId}' not found`);
			return;
		}

		// Create HTML structure
		container.innerHTML = this._createChartHTML();

		// Initialize chart
		setTimeout(() => {
			this._initializeChart();
		}, 100);

		// Bind export button
		if (this.config.showExportButton) {
			this._bindExportButton();
		}
	}

	/**
	 * Create the chart HTML structure
	 * @private
	 */
	_createChartHTML() {
		const exportButton = this.config.showExportButton
			? `
            <button class="btn btn-outline-primary btn-sm modern-chart-export-btn"
                    id="export-${this.canvasId}"
                    style="border-radius: 8px; padding: 0.5rem 1rem;">
                <i class="bi bi-download"></i> Export PNG
            </button>
        `
			: "";

		const subtitle = this.config.showSubtitle
			? `
            <p class="text-muted mb-0 small">${this.config.subtitle}</p>
        `
			: "";

		return `
            <div class="card shadow-sm border-0 modern-chart-card" style="border-radius: 12px; overflow: hidden;">
                <div class="card-header bg-white border-0 d-flex justify-content-between align-items-center" style="padding: 1.5rem;">
                    <div>
                        <h5 class="mb-1" style="color: #2c3e50; font-weight: 600;">
                            <i class="bi bi-bar-chart-fill" style="color: #0d6efd;"></i> ${this.config.title}
                        </h5>
                        ${subtitle}
                    </div>
                    ${exportButton}
                </div>
                <div class="card-body" style="padding: 2rem; background: linear-gradient(to bottom, #ffffff 0%, #f8f9fa 100%);">
                    <canvas id="${this.canvasId}" height="${this.config.height}"></canvas>
                </div>
            </div>
        `;
	}

	/**
	 * Initialize Chart.js chart
	 * @private
	 */
	_initializeChart() {
		const ctx = document.getElementById(this.canvasId);
		if (!ctx) {
			console.error(`Canvas with ID '${this.canvasId}' not found`);
			return;
		}

		const chartContext = ctx.getContext("2d");

		// Destroy existing chart if any
		if (this.chart) {
			this.chart.destroy();
		}

		// Create chart
		this.chart = new Chart(
			chartContext,
			this._getChartConfig(chartContext)
		);
	}

	/**
	 * Get Chart.js configuration
	 * @private
	 */
	_getChartConfig(ctx) {
		const backgroundColors = this.chartData.data.map((value) => {
			if (value < this.config.passingThreshold) {
				return this._createGradient(
					ctx,
					this.config.colors.danger,
					this.config.colors.dangerLight
				);
			} else {
				return this._createGradient(
					ctx,
					this.config.colors.success,
					this.config.colors.successLight
				);
			}
		});

		const borderColors = this.chartData.data.map((value) =>
			value < this.config.passingThreshold
				? this.config.colors.danger
				: this.config.colors.success
		);

		return {
			type: this.config.type,
			data: {
				labels: this.chartData.labels,
				datasets: [
					{
						label: "Capaian",
						data: this.chartData.data,
						backgroundColor: backgroundColors,
						borderColor: borderColors,
						borderWidth: 0,
						borderRadius: 8,
						barThickness: "flex",
						maxBarThickness: 60,
					},
				],
			},
			plugins: [ChartDataLabels],
			options: {
				responsive: true,
				maintainAspectRatio: true,
				interaction: {
					intersect: false,
					mode: "index",
				},
				animation: {
					duration: this.config.animationDuration,
					easing: "easeInOutQuart",
				},
				plugins: {
					legend: {
						display: true,
						position: "bottom",
						align: "end",
						labels: {
							usePointStyle: true,
							pointStyle: "circle",
							padding: 15,
							font: {
								size: 13,
								family: "'Inter', 'Segoe UI', sans-serif",
								weight: "500",
							},
							generateLabels: (chart) =>
								this._generateLegendLabels(chart),
						},
						padding: {
							bottom: 30,
						},
					},
					title: {
						display: false,
					},
					tooltip: {
						backgroundColor: "rgba(30, 39, 46, 0.95)",
						padding: 16,
						cornerRadius: 8,
						titleFont: {
							size: 14,
							weight: "600",
							family: "'Inter', 'Segoe UI', sans-serif",
						},
						bodyFont: {
							size: 13,
							family: "'Inter', 'Segoe UI', sans-serif",
						},
						borderColor: "rgba(255, 255, 255, 0.1)",
						borderWidth: 1,
						displayColors: true,
						callbacks: {
							title: (context) => context[0].label,
							label: (context) => {
								const value = context.parsed.y;
								const status =
									value >= this.config.passingThreshold
										? "Memenuhi"
										: "Belum Memenuhi";
								return [
									`Capaian: ${value.toFixed(2)}%`,
									`Status: ${status}`,
								];
							},
						},
					},
					datalabels: {
						anchor: "end",
						align: "top",
						offset: 4,
						formatter: (value) => value.toFixed(1) + "%",
						font: {
							weight: "600",
							size: 11,
							family: "'Inter', 'Segoe UI', sans-serif",
						},
						color: (context) => {
							return context.dataset.data[context.dataIndex] <
								this.config.passingThreshold
								? this.config.colors.danger
								: this.config.colors.success;
						},
						backgroundColor: (context) => {
							return context.dataset.data[context.dataIndex] <
								this.config.passingThreshold
								? "rgba(220, 53, 69, 0.1)"
								: "rgba(13, 110, 253, 0.1)";
						},
						borderRadius: 4,
						padding: {
							top: 4,
							bottom: 4,
							left: 8,
							right: 8,
						},
					},
				},
				scales: {
					y: {
						beginAtZero: true,
						max: 100,
						title: {
							display: true,
							text: this.config.labels.yAxis,
							font: {
								size: 13,
								weight: "600",
								family: "'Inter', 'Segoe UI', sans-serif",
							},
							color: "#2c3e50",
							padding: {
								bottom: 10,
							},
						},
						ticks: {
							callback: (value) => value + "%",
							font: {
								size: 11,
								family: "'Inter', 'Segoe UI', sans-serif",
							},
							color: "#5a6c7d",
							padding: 8,
						},
						grid: {
							display: true,
							color: "rgba(0, 0, 0, 0.04)",
							lineWidth: 1,
							drawBorder: false,
							drawTicks: false,
						},
						border: {
							display: false,
						},
					},
					x: {
						title: {
							display: true,
							text: this.config.labels.xAxis,
							font: {
								size: 13,
								weight: "600",
								family: "'Inter', 'Segoe UI', sans-serif",
							},
							color: "#2c3e50",
							padding: {
								top: 10,
							},
						},
						ticks: {
							font: {
								size: 11,
								weight: "500",
								family: "'Inter', 'Segoe UI', sans-serif",
							},
							color: "#2c3e50",
							padding: 8,
						},
						grid: {
							display: false,
							drawBorder: false,
						},
						border: {
							display: false,
						},
					},
				},
				layout: {
					padding: {
						top: 20,
						right: 20,
						bottom: 10,
						left: 10,
					},
				},
			},
		};
	}

	/**
	 * Create gradient for chart bars
	 * @private
	 */
	_createGradient(ctx, color1, color2) {
		const gradient = ctx.createLinearGradient(0, 0, 0, 400);
		gradient.addColorStop(0, color1);
		gradient.addColorStop(1, color2);
		return gradient;
	}

	/**
	 * Generate legend labels
	 * @private
	 */
	_generateLegendLabels(chart) {
		const data = chart.data.datasets[0].data;
		const labels = [];

		const hasAboveThreshold = data.some(
			(value) => value >= this.config.passingThreshold
		);
		if (hasAboveThreshold) {
			labels.push({
				text: `Capaian ≥ ${this.config.passingThreshold}%`,
				fillStyle: this.config.colors.success,
				strokeStyle: this.config.colors.success,
				lineWidth: 0,
				hidden: false,
				index: 0,
			});
		}

		const hasBelowThreshold = data.some(
			(value) => value < this.config.passingThreshold
		);
		if (hasBelowThreshold) {
			labels.push({
				text: `Capaian < ${this.config.passingThreshold}%`,
				fillStyle: this.config.colors.danger,
				strokeStyle: this.config.colors.danger,
				lineWidth: 0,
				hidden: false,
				index: 1,
			});
		}

		return labels;
	}

	/**
	 * Bind export button functionality
	 * @private
	 */
	_bindExportButton() {
		const exportBtn = document.getElementById(`export-${this.canvasId}`);
		if (exportBtn) {
			exportBtn.addEventListener("click", () => this.export());
		}
	}

	/**
	 * Export chart as PNG
	 */
	export() {
		if (this.chart) {
			const link = document.createElement("a");
			link.download = this.config.exportFilename;
			link.href = this.chart.toBase64Image();
			link.click();
		}
	}

	/**
	 * Update chart data
	 * @param {Object} newData - New chart data
	 */
	update(newData) {
		this.chartData = newData;
		if (this.chart) {
			this.chart.data.labels = newData.labels;
			this.chart.data.datasets[0].data = newData.data;
			this.chart.update();
		}
	}

	/**
	 * Destroy the chart
	 */
	destroy() {
		if (this.chart) {
			this.chart.destroy();
			this.chart = null;
		}
	}

	/**
	 * Show loading state
	 */
	showLoading() {
		const container = document.getElementById(this.containerId);
		if (container) {
			container.innerHTML = `
                <div class="text-center py-5">
                    <div class="spinner-border text-primary" role="status">
                        <span class="visually-hidden">Loading...</span>
                    </div>
                    <p class="text-muted mt-3">Memuat data...</p>
                </div>
            `;
		}
	}

	/**
	 * Show error state
	 * @param {string} message - Error message
	 */
	showError(message = "Terjadi kesalahan saat memuat data") {
		const container = document.getElementById(this.containerId);
		if (container) {
			container.innerHTML = `
                <div class="text-center py-5">
                    <i class="bi bi-exclamation-circle" style="font-size: 4rem; color: #dc3545;"></i>
                    <p class="text-danger mt-3">${message}</p>
                </div>
            `;
		}
	}

	/**
	 * Show empty state
	 * @param {string} message - Empty state message
	 */
	showEmpty(message = "Tidak ada data untuk ditampilkan") {
		const container = document.getElementById(this.containerId);
		if (container) {
			container.innerHTML = `
                <div class="text-center py-5">
                    <i class="bi bi-bar-chart" style="font-size: 4rem; color: #ccc;"></i>
                    <p class="text-muted mt-3">${message}</p>
                </div>
            `;
		}
	}
}

// Add CSS styles for the component
if (typeof document !== "undefined") {
	const style = document.createElement("style");
	style.textContent = `
        /* Modern Chart Component Styles */
        .modern-chart-card {
            animation: fadeInUp 0.5s ease-out;
        }

        .modern-chart-export-btn {
            transition: all 0.3s ease;
        }

        .modern-chart-export-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(13, 110, 253, 0.3);
        }

        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .modern-chart-card canvas {
            font-family: 'Inter', 'Segoe UI', -apple-system, BlinkMacSystemFont, sans-serif;
            border-radius: 8px;
        }
    `;
	document.head.appendChild(style);
}

// Export for use in modules
if (typeof module !== "undefined" && module.exports) {
	module.exports = ModernChartComponent;
}
