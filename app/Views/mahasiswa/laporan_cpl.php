<?= $this->extend('layouts/mahasiswa_layout') ?>

<?= $this->section('content') ?>

<!-- Include Modern Table Styles -->
<link rel="stylesheet" href="<?= base_url('css/modern-table.css') ?>">

<style>
	/* Filter Card Styling */
	.filter-card {
		border: 2px solid #e0e0e0;
		background: #ffffff;
		transition: all 0.3s ease;
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
	<h2 class="mb-1">Laporan CPL</h2>
	<p class="text-muted">Laporan Capaian Pembelajaran Lulusan</p>
</div>

<!-- Filter Type Cards -->
<div class="row g-3 mb-4">
	<div class="col-md-6">
		<button type="button" class="btn p-0 w-100 text-start border-0" data-bs-toggle="tab" data-bs-target="#cpl-semester" id="semester-card-btn">
			<div class="card filter-card h-100 border-primary shadow-sm" id="semester-card" style="cursor: pointer;">
				<div class="card-body text-center p-3">
					<div class="mb-2">
						<i class="bi bi-calendar-range text-primary" style="font-size: 1.8rem;"></i>
					</div>
					<h6 class="card-title mb-2 text-primary fw-bold">CPL Per Semester</h6>
					<p class="card-text text-muted small mb-0">Perhitungan CPL per semester dilakukan dengan menjumlahkan CPMK dari berbagai mata kuliah dalam satu semester.</p>
				</div>
			</div>
		</button>
	</div>
	<div class="col-md-6">
		<button type="button" class="btn p-0 w-100 text-start border-0" data-bs-toggle="tab" data-bs-target="#cpl-tahun" id="tahun-card-btn">
			<div class="card filter-card h-100 shadow-sm" id="tahun-card" style="cursor: pointer;">
				<div class="card-body text-center p-3">
					<div class="mb-2">
						<i class="bi bi-calendar2-event text-secondary" style="font-size: 1.8rem;"></i>
					</div>
					<h6 class="card-title mb-2">CPL Per Tahun Akademik</h6>
					<p class="card-text text-muted small mb-0">Perhitungan CPL per tahun akademik dilakukan dengan menjumlahkan CPMK dari berbagai mata kuliah dalam 1 tahun akademik.</p>
				</div>
			</div>
		</button>
	</div>
</div>

<!-- Tab Content -->
<div class="tab-content" id="filterTabsContent">
	<!-- Semester Filter Tab -->
	<div class="tab-pane fade show active" id="cpl-semester" role="tabpanel">
		<div class="card mb-4">
			<div class="card-body">
				<div class="d-flex align-items-center gap-2 mb-3">
					<i class="bi bi-funnel-fill text-primary"></i>
					<span class="fw-semibold">Filter CPL Per Semester</span>
				</div>
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
	<div class="tab-pane fade" id="cpl-tahun" role="tabpanel">
		<div class="card mb-4">
			<div class="card-body">
				<div class="d-flex align-items-center gap-2 mb-3">
					<i class="bi bi-funnel-fill text-primary"></i>
					<span class="fw-semibold">Filter CPL Per Tahun Akademik</span>
				</div>
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
		<small>Pilih filter untuk melihat laporan CPL Anda</small>
	</div>
</div>

<!-- Data Table -->
<div id="dataTable" style="display: none;">
	<div class="shadow-sm mb-4">
		<div class="modern-table-wrapper">
			<div class="scroll-indicator"></div>
			<table id="cplTable" class="modern-table">
				<thead>
					<tr>
						<th class="text-center" style="width: 50px;">No</th>
						<th class="text-center" style="min-width: 120px;">Kode CPL</th>
						<th class="text-center" style="min-width: 250px;">Deskripsi</th>
						<th class="text-center" style="min-width: 100px;">Jumlah MK</th>
						<th class="text-center" style="width: 120px;">Nilai CPL</th>
						<th class="text-center" style="width: 120px;">Capaian CPL</th>
						<th class="text-center" style="width: 100px;">Detail</th>
					</tr>
				</thead>
				<tbody id="tableBody">
				</tbody>
			</table>
		</div>
	</div>
</div>

<!-- Modal Detail CPL -->
<div class="modal fade" id="detailModal" tabindex="-1">
	<div class="modal-dialog modal-xl">
		<div class="modal-content">
			<div class="modal-header bg-primary text-white">
				<h5 class="modal-title" id="detailModalTitle">Detail Perhitungan CPL</h5>
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
			updateActiveCard('semester');
		});

		$('#tahun-card-btn, #tahun-card').on('click', function(e) {
			e.preventDefault();
			updateActiveCard('tahun');
		});

		function updateActiveCard(type) {
			// Remove active styling from all cards
			$('.filter-card').removeClass('border-primary');
			$('.filter-card .card-title').removeClass('text-primary fw-bold');
			$('.filter-card i').removeClass('text-primary').addClass('text-secondary');

			// Add active styling to selected card
			const cardId = type + '-card';
			$('#' + cardId).addClass('border-primary');
			$('#' + cardId).find('.card-title').addClass('text-primary fw-bold');
			$('#' + cardId).find('i').removeClass('text-secondary').addClass('text-primary');

			// Switch to corresponding tab
			$('.tab-pane').removeClass('show active');
			$('#cpl-' + type).addClass('show active');

			// Hide data table and show empty state
			$('#dataTable').hide();
			$('#emptyState').show();
		}

		// Handle semester filter form submission
		$('#filterFormSemester').on('submit', function(e) {
			e.preventDefault();

			const semester = $('#semester').val();

			if (!semester) {
				alert('Pilih semester terlebih dahulu');
				return;
			}

			loadData(semester, '', 'semester');
		});

		// Handle tahun akademik filter form submission
		$('#filterFormTahun').on('submit', function(e) {
			e.preventDefault();

			const tahunAkademik = $('#tahun_akademik').val();

			if (!tahunAkademik) {
				alert('Pilih tahun akademik terlebih dahulu');
				return;
			}

			loadData('', tahunAkademik, 'tahun');
		});

		function loadData(semester, tahunAkademik, filterType) {
			// Show loading, hide others
			$('#loadingSpinner').show();
			$('#emptyState').hide();
			$('#dataTable').hide();

			$.ajax({
				url: '<?= base_url('mahasiswa/get-laporan-cpl-data') ?>',
				type: 'GET',
				data: {
					semester: semester,
					tahun_akademik: tahunAkademik,
					filter_type: filterType
				},
				dataType: 'json',
				success: function(response) {
					$('#loadingSpinner').hide();

					if (response.success && response.data.length > 0) {
						renderTable(response.data, filterType, semester, tahunAkademik);
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

		function renderTable(data, filterType, semester, tahunAkademik) {
			const tbody = $('#tableBody');
			tbody.empty();

			data.forEach((item, index) => {
				const row = `
					<tr>
						<td class="text-center">${index + 1}</td>
						<td class="text-center">
							<strong class="text-primary">${escapeHtml(item.kode_cpl)}</strong>
						</td>
						<td>
							<small class="text-dark">${escapeHtml(item.deskripsi)}</small>
						</td>
						<td class="text-center">
							${item.mata_kuliah_count}
						</td>
						<td class="text-center">
							<strong>${parseFloat(item.nilai_cpl).toFixed(2)}</strong>
						</td>
						<td class="text-center" data-order="${item.capaian}">
							${parseFloat(item.capaian).toFixed(2)}%
						</td>
						<td class="text-center">
							<button class="btn btn-sm btn-outline-primary" onclick="showDetail('${escapeHtml(item.kode_cpl)}', '${filterType}', '${semester}', '${tahunAkademik}')" data-bs-toggle="tooltip" title="Lihat detail nilai CPL">
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
			initPagination('cplTable', 10);

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

		window.showDetail = function(kodeCpl, filterType, semester, tahunAkademik) {
			$('#detailModal').modal('show');
			$('#detailModalTitle').text('Detail Perhitungan CPL: ' + kodeCpl);
			$('#detailModalContent').html(`
				<div class="text-center py-4">
					<div class="spinner-border text-primary" role="status">
						<span class="visually-hidden">Loading...</span>
					</div>
				</div>
			`);

			$.ajax({
				url: '<?= base_url('mahasiswa/get-cpl-detail-calculation') ?>',
				type: 'GET',
				data: {
					kode_cpl: kodeCpl,
					semester: semester,
					tahun_akademik: tahunAkademik,
					filter_type: filterType
				},
				dataType: 'json',
				success: function(response) {
					if (response.success) {
						renderDetailModal(response.data, response.summary);
					} else {
						$('#detailModalContent').html(`
							<div class="alert alert-warning">
								${response.message || 'Tidak ada data'}
							</div>
						`);
					}
				},
				error: function(xhr) {
					$('#detailModalContent').html(`
						<div class="alert alert-danger">
							Terjadi kesalahan saat memuat detail
						</div>
					`);
					console.error(xhr);
				}
			});
		};

		function renderDetailModal(data, summary) {
			let html = `
				<div class="modern-table-wrapper">
					<table class="modern-table mb-0">
						<thead>
							<tr>
								<th width="5%" class="text-center">No</th>
								<th width="15%">Kode CPMK</th>
								<th width="25%">Mata Kuliah</th>
								<th width="12%" class="text-center">Tahun Akademik</th>
								<th width="10%" class="text-center">Kelas</th>
								<th width="10%" class="text-center">Nilai CPMK</th>
								<th width="10%" class="text-center">Bobot (%)</th>
							</tr>
						</thead>
						<tbody>
			`;

			if (data.length === 0) {
				html += `
					<tr>
						<td colspan="7" class="text-center text-muted">Belum ada data nilai untuk CPL ini</td>
					</tr>
				`;
			} else {
				data.forEach((item, index) => {
					html += `
						<tr>
							<td class="text-center">${index + 1}</td>
							<td><strong>${escapeHtml(item.kode_cpmk)}</strong></td>
							<td><small>${escapeHtml(item.kode_mk)} - ${escapeHtml(item.nama_mk)}</small></td>
							<td class="text-center">${escapeHtml(item.tahun_akademik)}</td>
							<td class="text-center">${escapeHtml(item.kelas)}</td>
							<td class="text-center">${parseFloat(item.nilai_cpmk).toFixed(2)}</td>
							<td class="text-center">${parseFloat(item.bobot).toFixed(0)}%</td>
						</tr>
					`;
				});
			}

			// Summary row
			html += `
						</tbody>
						<tfoot>
							<tr>
								<td colspan="5" class="text-end"><strong>TOTAL:</strong></td>
								<td class="text-center"><strong>${summary.nilai_cpl.toFixed(2)}</strong></td>
								<td class="text-center"><strong>${summary.total_bobot.toFixed(0)}%</strong></td>
							</tr>
							<tr style="background-color: #d1e7dd;">
								<td colspan="6" class="text-end"><strong>Capaian CPL (%) = (${summary.nilai_cpl.toFixed(2)} / ${summary.total_bobot.toFixed(0)}) × 100</strong></td>
								<td class="text-center"><h6 class="mb-0"><strong>${summary.capaian_cpl.toFixed(2)}%</strong></h6></td>
							</tr>
						</tfoot>
					</table>
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
			return String(text).replace(/[&<>"']/g, m => map[m]);
		}
	});
</script>
<?= $this->endSection() ?>