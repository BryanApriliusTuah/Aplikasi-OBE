<?= $this->extend('layouts/mahasiswa_layout') ?>

<?= $this->section('content') ?>

<!-- Include Modern Table Styles -->
<link rel="stylesheet" href="<?= base_url('css/modern-table.css') ?>">

<style>
	.filter-card {
		border: 2px solid #e0e0e0;
		background: #ffffff;
	}

	.filter-card:hover {
		transform: translateY(-5px);
		border-color: #0d6efd !important;
	}

	.filter-card.border-primary {
		background: linear-gradient(135deg, #f8f9ff 0%, #ffffff 100%);
	}

	.filter-card i {
		transition: all 0.3s ease;
	}

	.filter-card:hover i {
		transform: scale(1.1);
	}

	.filter-card .card-title {
		transition: all 0.3s ease;
		font-size: 1.1rem;
	}

	.filter-card .card-text {
		line-height: 1.4;
		min-height: 40px;
	}
</style>

<div class="mb-4">
	<h2 class="mb-1">Laporan CPMK</h2>
	<p class="text-muted">Laporan Capaian Pembelajaran Mata Kuliah</p>
</div>

<!-- Sub-tabs for different filter types -->
<div class="row g-3 mb-4">
	<div class="col-md-6">
		<button type="button" class="btn p-0 w-100 text-start border-0" data-bs-toggle="tab" data-bs-target="#semester-filter" id="semester-card-btn">
			<div class="card filter-card h-100 border-primary shadow-sm" id="semester-card" style="cursor: pointer; transition: all 0.3s;">
				<div class="card-body text-center p-3">
					<div class="mb-2">
						<i class="bi bi-calendar-week text-primary" style="font-size: 1.8rem;"></i>
					</div>
					<h6 class="card-title mb-2 text-primary fw-bold">CPMK Per Semester</h6>
					<p class="card-text text-muted small mb-0">Pilih semester tertentu untuk melihat laporan CPMK mahasiswa pada semester tersebut.</p>
				</div>
			</div>
		</button>
	</div>
	<div class="col-md-6">
		<button type="button" class="btn p-0 w-100 text-start border-0" data-bs-toggle="tab" data-bs-target="#tahun-filter" id="tahun-card-btn">
			<div class="card filter-card h-100 shadow-sm" id="tahun-card" style="cursor: pointer; transition: all 0.3s;">
				<div class="card-body text-center p-3">
					<div class="mb-2">
						<i class="bi bi-calendar-range text-secondary" style="font-size: 1.8rem;"></i>
					</div>
					<h6 class="card-title mb-2">CPMK Per Tahun Akademik</h6>
					<p class="card-text text-muted small mb-0">Pilih tahun akademik untuk melihat laporan CPMK mahasiswa dalam 1 tahun akademik.</p>
				</div>
			</div>
		</button>
	</div>
</div>

<!-- Tab Content -->
<div class="tab-content" id="filterTabsContent">
	<!-- Semester Filter Tab -->
	<div class="tab-pane fade show active" id="semester-filter" role="tabpanel">
		<div class="card mb-4">
			<div class="card-body">
				<form id="filterFormSemester">
					<div class="row g-3">
						<div class="col-md-10">
							<label for="semester" class="form-label">Semester <span class="text-danger">*</span></label>
							<select class="form-select" id="semester" name="semester" required>
								<option value="">-- Pilih Semester --</option>
								<?php foreach ($semesterList as $sem): ?>
									<option value="<?= esc($sem) ?>"><?= esc($sem) ?></option>
								<?php endforeach; ?>
							</select>
						</div>
						<div class="col-md-2 d-flex align-items-end">
							<button type="submit" class="btn btn-primary w-100">
								<i class="bi bi-search"></i> Tampilkan
							</button>
						</div>
					</div>
				</form>
			</div>
		</div>
	</div>

	<!-- Tahun Akademik Filter Tab -->
	<div class="tab-pane fade" id="tahun-filter" role="tabpanel">
		<div class="card mb-4">
			<div class="card-body">
				<form id="filterFormTahun">
					<div class="row g-3">
						<div class="col-md-10">
							<label for="tahun_akademik" class="form-label">Tahun Akademik <span class="text-danger">*</span></label>
							<select class="form-select" id="tahun_akademik" name="tahun_akademik" required>
								<option value="">-- Pilih Tahun Akademik --</option>
								<?php foreach ($tahunAkademikList as $tahun): ?>
									<option value="<?= esc($tahun) ?>"><?= esc($tahun) ?></option>
								<?php endforeach; ?>
							</select>
						</div>
						<div class="col-md-2 d-flex align-items-end">
							<button type="submit" class="btn btn-primary w-100">
								<i class="bi bi-search"></i> Tampilkan
							</button>
						</div>
					</div>
				</form>
			</div>
		</div>
	</div>
</div>

<!-- Loading Spinner -->
<div id="loadingSpinner" class="text-center py-5" style="display: none;">
	<div class="spinner-border text-primary" role="status">
		<span class="visually-hidden">Loading...</span>
	</div>
	<p class="mt-3 text-muted">Memuat data...</p>
</div>

<!-- Empty State -->
<div id="emptyState" class="card" style="display: none;">
	<div class="card-body text-center py-5 text-muted">
		<i class="bi bi-inbox" style="font-size: 3rem;"></i>
		<p class="mb-1 mt-3">Belum ada data yang ditampilkan</p>
		<small>Pilih filter berdasarkan semester atau tahun akademik untuk melihat laporan CPMK</small>
	</div>
</div>

<!-- Data Table -->
<div id="dataTable" style="display: none;">
	<div class="shadow-sm mb-4">
		<div class="modern-table-wrapper">
			<div class="scroll-indicator"></div>
			<table id="cpmkTable" class="modern-table">
				<thead>
					<tr>
						<th class="text-center" style="width: 50px;">No</th>
						<th class="text-center" style="min-width: 120px;">Kode CPMK</th>
						<th class="text-center" style="min-width: 250px;">Deskripsi</th>
						<th class="text-center" style="min-width: 100px;">Jumlah MK</th>
						<th class="text-center" style="width: 120px;">Nilai CPMK</th>
						<th class="text-center" style="width: 120px;">Capaian CPMK</th>
						<th class="text-center" style="width: 100px;">Detail</th>
					</tr>
				</thead>
				<tbody id="tableBody">
				</tbody>
			</table>
		</div>
	</div>
</div>

<!-- Modal Detail CPMK -->
<div class="modal fade" id="detailModal" tabindex="-1">
	<div class="modal-dialog modal-xl">
		<div class="modal-content">
			<div class="modal-header bg-primary text-white">
				<h5 class="modal-title" id="detailModalTitle">Detail Perhitungan CPMK</h5>
				<button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
			</div>
			<div class="modal-body">
				<div id="detailModalContent">
					<div class="text-center py-4">
						<div class="spinner-border text-primary" role="status">
							<span class="visually-hidden">Loading...</span>
						</div>
					</div>
				</div>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
			</div>
		</div>
	</div>
</div>

<?= $this->endSection() ?>

<?= $this->section('js') ?>
<script>
	$(document).ready(function() {
		// Show empty state initially
		$('#emptyState').show();

		// Handle filter card clicks - update styling and switch tabs
		$('#semester-card-btn, #semester-card').on('click', function(e) {
			e.preventDefault();

			// Remove active styling from all cards
			$('.filter-card').removeClass('border-primary');
			$('.filter-card .card-title').removeClass('text-primary fw-bold');
			$('.filter-card i').removeClass('text-primary').addClass('text-secondary');

			// Add active styling to semester card
			$('#semester-card').addClass('border-primary');
			$('#semester-card').find('.card-title').addClass('text-primary fw-bold');
			$('#semester-card').find('i').removeClass('text-secondary').addClass('text-primary');

			// Switch to semester tab
			$('.tab-pane').removeClass('show active');
			$('#semester-filter').addClass('show active');
		});

		$('#tahun-card-btn, #tahun-card').on('click', function(e) {
			e.preventDefault();

			// Remove active styling from all cards
			$('.filter-card').removeClass('border-primary');
			$('.filter-card .card-title').removeClass('text-primary fw-bold');
			$('.filter-card i').removeClass('text-primary').addClass('text-secondary');

			// Add active styling to tahun card
			$('#tahun-card').addClass('border-primary');
			$('#tahun-card').find('.card-title').addClass('text-primary fw-bold');
			$('#tahun-card').find('i').removeClass('text-secondary').addClass('text-primary');

			// Switch to tahun akademik tab
			$('.tab-pane').removeClass('show active');
			$('#tahun-filter').addClass('show active');
		});

		// Handle semester filter form submission
		$('#filterFormSemester').on('submit', function(e) {
			e.preventDefault();

			const semester = $('#semester').val();

			if (!semester) {
				alert('Pilih semester terlebih dahulu');
				return;
			}

			loadData(semester, '');
		});

		// Handle tahun akademik filter form submission
		$('#filterFormTahun').on('submit', function(e) {
			e.preventDefault();

			const tahunAkademik = $('#tahun_akademik').val();

			if (!tahunAkademik) {
				alert('Pilih tahun akademik terlebih dahulu');
				return;
			}

			loadData('', tahunAkademik);
		});

		function loadData(semester, tahunAkademik) {
			// Show loading, hide others
			$('#loadingSpinner').show();
			$('#emptyState').hide();
			$('#dataTable').hide();

			$.ajax({
				url: '<?= base_url('mahasiswa/get-laporan-cpmk-data') ?>',
				type: 'GET',
				data: {
					semester: semester,
					tahun_akademik: tahunAkademik
				},
				dataType: 'json',
				success: function(response) {
					$('#loadingSpinner').hide();

					if (response.success && response.data.length > 0) {
						renderTable(response.data);
						$('#dataTable').show();
					} else {
						alert(response.message || 'Tidak ada data untuk filter yang dipilih');
						$('#emptyState').show();
					}
				},
				error: function(xhr) {
					$('#loadingSpinner').hide();
					$('#emptyState').show();
					alert('Terjadi kesalahan saat memuat data');
					console.error(xhr);
				}
			});
		}

		function renderTable(data) {
			const tbody = $('#tableBody');
			tbody.empty();

			data.forEach((item, index) => {
				const row = `
					<tr>
						<td class="text-center">${index + 1}</td>
						<td class="text-center">
							<strong class="text-primary">${escapeHtml(item.kode_cpmk)}</strong>
						</td>
						<td>
							<small class="text-dark">${escapeHtml(item.deskripsi)}</small>
						</td>
						<td class="text-center">
							${item.mata_kuliah_count}
						</td>
						<td class="text-center">
							<strong>${parseFloat(item.nilai_cpmk).toFixed(2)}</strong>
						</td>
						<td class="text-center" data-order="${item.capaian}">
								${parseFloat(item.capaian).toFixed(2)}%
						</td>
						<td class="text-center">
							<button class="btn btn-sm btn-outline-primary" onclick="showDetail('${escapeHtml(item.kode_cpmk)}')" data-bs-toggle="tooltip" title="Lihat detail nilai CPMK">
								<i class="bi bi-eye"></i>
							</button>
						</td>
					</tr>
				`;
				tbody.append(row);
			});

			// Initialize tooltips
			$('[data-bs-toggle="tooltip"]').tooltip();

			// Initialize custom pagination
			initPagination('cpmkTable', 10);

			// Handle scroll indicator for modern table
			const tableWrapper = document.querySelector('.modern-table-wrapper');
			if (tableWrapper) {
				function checkScroll() {
					const hasHorizontalScroll = tableWrapper.scrollWidth > tableWrapper.clientWidth;
					const isScrolledToEnd = tableWrapper.scrollLeft >= (tableWrapper.scrollWidth - tableWrapper.clientWidth - 10);

					if (hasHorizontalScroll && !isScrolledToEnd) {
						tableWrapper.classList.add('has-scroll');
					} else {
						tableWrapper.classList.remove('has-scroll');
					}
				}

				// Check on load and resize
				checkScroll();
				window.addEventListener('resize', checkScroll);
				tableWrapper.addEventListener('scroll', checkScroll);
			}
		}

		// Custom Pagination Function
		function initPagination(tableId, rowsPerPage = 10) {
			const table = document.getElementById(tableId);
			if (!table) return;

			const tbody = table.querySelector('tbody');
			const rows = Array.from(tbody.querySelectorAll('tr'));
			const totalRows = rows.length;
			const totalPages = Math.ceil(totalRows / rowsPerPage);
			let currentPage = 1;

			// Create pagination controls
			const paginationId = `${tableId}_pagination`;
			let paginationContainer = document.getElementById(paginationId);

			if (!paginationContainer) {
				paginationContainer = document.createElement('div');
				paginationContainer.id = paginationId;
				paginationContainer.className = 'd-flex justify-content-between align-items-center mt-3';
				table.parentElement.parentElement.appendChild(paginationContainer);
			}

			function showPage(page) {
				currentPage = page;
				const start = (page - 1) * rowsPerPage;
				const end = start + rowsPerPage;

				rows.forEach((row, index) => {
					row.style.display = (index >= start && index < end) ? '' : 'none';
				});

				renderPagination();
			}

			function renderPagination() {
				const startEntry = totalRows === 0 ? 0 : ((currentPage - 1) * rowsPerPage) + 1;
				const endEntry = Math.min(currentPage * rowsPerPage, totalRows);

				let html = `
					<div class="text-muted small">
						Menampilkan ${startEntry} sampai ${endEntry} dari ${totalRows} data
					</div>
					<nav>
						<ul class="pagination pagination-sm mb-0">
				`;

				// Previous button
				html += `
					<li class="page-item ${currentPage === 1 ? 'disabled' : ''}">
						<a class="page-link" href="#" data-page="${currentPage - 1}">Previous</a>
					</li>
				`;

				// Page numbers
				const maxVisiblePages = 5;
				let startPage = Math.max(1, currentPage - Math.floor(maxVisiblePages / 2));
				let endPage = Math.min(totalPages, startPage + maxVisiblePages - 1);

				if (endPage - startPage + 1 < maxVisiblePages) {
					startPage = Math.max(1, endPage - maxVisiblePages + 1);
				}

				if (startPage > 1) {
					html += `<li class="page-item"><a class="page-link" href="#" data-page="1">1</a></li>`;
					if (startPage > 2) {
						html += `<li class="page-item disabled"><span class="page-link">...</span></li>`;
					}
				}

				for (let i = startPage; i <= endPage; i++) {
					html += `
						<li class="page-item ${i === currentPage ? 'active' : ''}">
							<a class="page-link" href="#" data-page="${i}">${i}</a>
						</li>
					`;
				}

				if (endPage < totalPages) {
					if (endPage < totalPages - 1) {
						html += `<li class="page-item disabled"><span class="page-link">...</span></li>`;
					}
					html += `<li class="page-item"><a class="page-link" href="#" data-page="${totalPages}">${totalPages}</a></li>`;
				}

				// Next button
				html += `
					<li class="page-item ${currentPage === totalPages ? 'disabled' : ''}">
						<a class="page-link" href="#" data-page="${currentPage + 1}">Next</a>
					</li>
				`;

				html += `
						</ul>
					</nav>
				`;

				paginationContainer.innerHTML = html;

				// Add event listeners
				paginationContainer.querySelectorAll('a.page-link').forEach(link => {
					link.addEventListener('click', function(e) {
						e.preventDefault();
						const page = parseInt(this.getAttribute('data-page'));
						if (page >= 1 && page <= totalPages) {
							showPage(page);
						}
					});
				});
			}

			// Initialize first page
			showPage(1);
		}

		window.showDetail = function(kodeCpmk) {
			// Get filter values based on active tab
			let semester = '';
			let tahunAkademik = '';

			if ($('#semester-filter').hasClass('show active')) {
				semester = $('#semester').val();
			} else if ($('#tahun-filter').hasClass('show active')) {
				tahunAkademik = $('#tahun_akademik').val();
			}

			$('#detailModalTitle').text(`Detail Perhitungan ${kodeCpmk}`);
			$('#detailModal').modal('show');
			$('#detailModalContent').html(`
				<div class="text-center py-4">
					<div class="spinner-border text-primary" role="status">
						<span class="visually-hidden">Loading...</span>
					</div>
					<p class="mt-3 text-muted">Memuat detail perhitungan...</p>
				</div>
			`);

			$.ajax({
				url: '<?= base_url('mahasiswa/get-cpmk-detail-calculation') ?>',
				type: 'GET',
				data: {
					kode_cpmk: kodeCpmk,
					semester: semester,
					tahun_akademik: tahunAkademik
				},
				dataType: 'json',
				success: function(response) {
					if (response.success) {
						renderDetailModal(response.data, response.summary);
					} else {
						$('#detailModalContent').html(`
							<div class="alert alert-warning">
								<i class="bi bi-exclamation-triangle"></i> ${response.message || 'Tidak ada data'}
							</div>
						`);
					}
				},
				error: function(xhr) {
					$('#detailModalContent').html(`
						<div class="alert alert-danger">
							<i class="bi bi-exclamation-triangle"></i> Terjadi kesalahan saat memuat detail
						</div>
					`);
					console.error(xhr);
				}
			});
		};

		function renderDetailModal(data, summary) {
			let html = '';

			// Display per-course CPMK score breakdown
			if (data && data.length > 0) {
				html += `
					<div class="modern-table-wrapper mb-4">
						<table class="modern-table">
							<thead>
								<tr>
									<th>Mata Kuliah</th>
									<th class="text-center">Semester</th>
									<th class="text-center">Kelas</th>
									<th class="text-center">Nilai CPMK</th>
									<th class="text-center">Bobot</th>
									<th class="text-center">Capaian (%)</th>
								</tr>
							</thead>
							<tbody>
				`;

				data.forEach(course => {
					const kelasLabel = course.kelas === 'KM'
						? '<span class="badge bg-info">MBKM</span>'
						: escapeHtml(course.kelas);

					html += `
						<tr>
							<td><strong>${escapeHtml(course.kode_mk)}</strong> - ${escapeHtml(course.nama_mk)}</td>
							<td class="text-center">${escapeHtml(course.tahun_akademik)}</td>
							<td class="text-center">${kelasLabel}</td>
							<td class="text-center">${parseFloat(course.nilai_cpmk).toFixed(2)}</td>
							<td class="text-center">${parseFloat(course.bobot).toFixed(2)}</td>
							<td class="text-center">${parseFloat(course.capaian).toFixed(2)}%</td>
						</tr>
					`;
				});

				html += `
							</tbody>
							<tfoot>
								<tr>
									<td colspan="3" class="text-end"><strong>Total</strong></td>
									<td class="text-center"><strong>${parseFloat(summary.grand_total_nilai_cpmk).toFixed(2)}</strong></td>
									<td class="text-center"><strong>${parseFloat(summary.grand_total_bobot).toFixed(2)}</strong></td>
									<td class="text-center"><strong>${parseFloat(summary.capaian).toFixed(2)}%</strong></td>
								</tr>
							</tfoot>
						</table>
					</div>
				`;
			} else {
				html += `<p class="text-muted">Belum ada nilai</p>`;
			}

			// Display formula and final capaian
			html += `
				<div class="alert alert-primary mb-0">
					<h6 class="mb-2"><i class="bi bi-calculator"></i> Capaian ${escapeHtml(summary.kode_cpmk)}:</h6>
					<p class="mb-1"><strong>Capaian CPMK</strong> = ${summary.grand_total_nilai_cpmk} / ${summary.grand_total_bobot} &times; 100 = ${parseFloat(summary.capaian).toFixed(2)}%</p>
				</div>
			`;

			$('#detailModalContent').html(html);
		}

		function escapeHtml(text) {
			const map = {
				'&': '&amp;',
				'<': '&lt;',
				'>': '&gt;',
				'"': '&quot;',
				"'": '&#039;'
			};
			return text.replace(/[&<>"']/g, m => map[m]);
		}
	});
</script>
<?= $this->endSection() ?>