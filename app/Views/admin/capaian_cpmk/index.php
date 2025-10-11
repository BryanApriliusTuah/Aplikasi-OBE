<?= $this->extend('layouts/admin_layout') ?>

<?= $this->section('content') ?>

<div class="card">
    <div class="card-body">
        <h2 class="mb-4">Capaian CPMK Mahasiswa</h2>

        <!-- Filter Section -->
        <div class="card mb-4">
            <div class="card-header bg-light">
                <h5 class="mb-0"><i class="bi bi-funnel"></i> Filter Data</h5>
            </div>
            <div class="card-body">
                <form id="filterForm">
                    <div class="row g-3">
                        <div class="col-md-4">
                            <label for="mataKuliahSelect" class="form-label">Mata Kuliah <span class="text-danger">*</span></label>
                            <select class="form-select" id="mataKuliahSelect" name="mata_kuliah_id" required>
                                <option value="">-- Pilih Mata Kuliah --</option>
                                <?php foreach ($mataKuliah as $mk): ?>
                                    <option value="<?= $mk['id'] ?>"><?= esc($mk['kode_mk']) ?> - <?= esc($mk['nama_mk']) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label for="tahunAkademikSelect" class="form-label">Tahun Akademik</label>
                            <select class="form-select" id="tahunAkademikSelect" name="tahun_akademik">
                                <option value="">-- Semua Tahun --</option>
                                <?php foreach ($tahunAkademik as $ta): ?>
                                    <option value="<?= esc($ta) ?>"><?= esc($ta) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label for="kelasSelect" class="form-label">Kelas</label>
                            <select class="form-select" id="kelasSelect" name="kelas" disabled>
                                <option value="">-- Semua Kelas --</option>
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

        <!-- Info Section -->
        <div id="infoSection" class="alert alert-info d-none">
            <h6 class="mb-2"><strong>Informasi:</strong></h6>
            <div id="infoContent"></div>
        </div>

        <!-- Chart Section -->
        <div id="chartSection" class="d-none">
            <div class="card">
                <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                    <h5 class="mb-0"><i class="bi bi-bar-chart-fill"></i> Grafik Rata-rata Capaian CPMK</h5>
                    <button class="btn btn-light btn-sm" id="exportChartBtn">
                        <i class="bi bi-download"></i> Export PNG
                    </button>
                </div>
                <div class="card-body">
                    <canvas id="cpmkChart" height="80"></canvas>
                </div>
            </div>

            <!-- Detail Table -->
            <div class="card mt-4">
                <div class="card-header bg-secondary text-white">
                    <h5 class="mb-0"><i class="bi bi-table"></i> Detail Capaian CPMK</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered table-hover" id="detailTable">
                            <thead class="table-light">
                                <tr>
                                    <th>No</th>
                                    <th>Kode CPMK</th>
                                    <th>Deskripsi CPMK</th>
                                    <th class="text-center">Jumlah Mahasiswa</th>
                                    <th class="text-center">Rata-rata Nilai</th>
                                    <th class="text-center">Status</th>
                                    <th class="text-center">Aksi</th>
                                </tr>
                            </thead>
                            <tbody id="detailTableBody">
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Empty State -->
        <div id="emptyState" class="text-center py-5">
            <i class="bi bi-bar-chart" style="font-size: 4rem; color: #ccc;"></i>
            <p class="text-muted mt-3">Pilih mata kuliah dan klik "Tampilkan" untuk melihat grafik capaian CPMK</p>
        </div>
    </div>
</div>

<!-- Modal Detail Mahasiswa -->
<div class="modal fade" id="detailModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title" id="detailModalTitle">Detail Nilai Mahasiswa</h5>
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
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
let cpmkChart = null;
let currentChartData = null;

$(document).ready(function() {
    // Load kelas when mata kuliah or tahun akademik changes
    $('#mataKuliahSelect, #tahunAkademikSelect').on('change', function() {
        const mataKuliahId = $('#mataKuliahSelect').val();
        const tahunAkademik = $('#tahunAkademikSelect').val();
        
        if (mataKuliahId) {
            loadKelas(mataKuliahId, tahunAkademik);
        } else {
            $('#kelasSelect').prop('disabled', true).html('<option value="">-- Semua Kelas --</option>');
        }
    });

    // Form submit handler
    $('#filterForm').on('submit', function(e) {
        e.preventDefault();
        loadChartData();
    });

    // Export chart handler
    $('#exportChartBtn').on('click', function() {
        if (cpmkChart) {
            const link = document.createElement('a');
            link.download = 'capaian-cpmk.png';
            link.href = cpmkChart.toBase64Image();
            link.click();
        }
    });
});

function loadKelas(mataKuliahId, tahunAkademik) {
    $.ajax({
        url: '<?= base_url("admin/capaian-cpmk/get-kelas") ?>',
        method: 'GET',
        data: {
            mata_kuliah_id: mataKuliahId,
            tahun_akademik: tahunAkademik
        },
        success: function(response) {
            const kelasSelect = $('#kelasSelect');
            kelasSelect.html('<option value="">-- Semua Kelas --</option>');
            
            if (response.length > 0) {
                response.forEach(function(kelas) {
                    kelasSelect.append(`<option value="${kelas}">${kelas}</option>`);
                });
                kelasSelect.prop('disabled', false);
            } else {
                kelasSelect.prop('disabled', true);
            }
        }
    });
}

function loadChartData() {
    const formData = {
        mata_kuliah_id: $('#mataKuliahSelect').val(),
        tahun_akademik: $('#tahunAkademikSelect').val(),
        kelas: $('#kelasSelect').val()
    };

    if (!formData.mata_kuliah_id) {
        alert('Silakan pilih mata kuliah terlebih dahulu');
        return;
    }

    // Show loading
    $('#emptyState').addClass('d-none');
    $('#chartSection').removeClass('d-none').html(`
        <div class="text-center py-5">
            <div class="spinner-border text-primary" role="status">
                <span class="visually-hidden">Loading...</span>
            </div>
            <p class="text-muted mt-3">Memuat data...</p>
        </div>
    `);

    $.ajax({
        url: '<?= base_url("admin/capaian-cpmk/chart-data") ?>',
        method: 'GET',
        data: formData,
        success: function(response) {
            if (response.success) {
                currentChartData = response;
                displayChart(response);
                displayInfo(response);
                displayDetailTable(response.chartData.details);
            } else {
                $('#chartSection').addClass('d-none');
                $('#emptyState').removeClass('d-none').html(`
                    <i class="bi bi-exclamation-circle" style="font-size: 4rem; color: #dc3545;"></i>
                    <p class="text-danger mt-3">${response.message}</p>
                `);
            }
        },
        error: function() {
            $('#chartSection').addClass('d-none');
            $('#emptyState').removeClass('d-none').html(`
                <i class="bi bi-exclamation-triangle" style="font-size: 4rem; color: #ffc107;"></i>
                <p class="text-warning mt-3">Terjadi kesalahan saat memuat data</p>
            `);
        }
    });
}

function displayInfo(response) {
    const info = `
        <div class="row">
            <div class="col-md-6">
                <strong>Mata Kuliah:</strong> ${response.mataKuliah.kode_mk} - ${response.mataKuliah.nama_mk}
            </div>
            <div class="col-md-3">
                <strong>Tahun Akademik:</strong> ${response.jadwal.tahun_akademik}
            </div>
            <div class="col-md-3">
                <strong>Kelas:</strong> ${response.jadwal.kelas}
            </div>
        </div>
    `;
    $('#infoContent').html(info);
    $('#infoSection').removeClass('d-none');
}

function displayChart(response) {
    $('#chartSection').html(`
        <div class="card">
            <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                <h5 class="mb-0"><i class="bi bi-bar-chart-fill"></i> Grafik Rata-rata Capaian CPMK</h5>
                <button class="btn btn-light btn-sm" id="exportChartBtn">
                    <i class="bi bi-download"></i> Export PNG
                </button>
            </div>
            <div class="card-body">
                <canvas id="cpmkChart" height="80"></canvas>
            </div>
        </div>
        <div class="card mt-4">
            <div class="card-header bg-secondary text-white">
                <h5 class="mb-0"><i class="bi bi-table"></i> Detail Capaian CPMK</h5>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered table-hover" id="detailTable">
                        <thead class="table-light">
                            <tr>
                                <th>No</th>
                                <th>Kode CPMK</th>
                                <th>Deskripsi CPMK</th>
                                <th class="text-center">Jumlah Mahasiswa</th>
                                <th class="text-center">Rata-rata Nilai</th>
                                <th class="text-center">Status</th>
                                <th class="text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody id="detailTableBody">
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    `);

    // Re-bind export button
    $('#exportChartBtn').on('click', function() {
        if (cpmkChart) {
            const link = document.createElement('a');
            link.download = 'capaian-cpmk.png';
            link.href = cpmkChart.toBase64Image();
            link.click();
        }
    });

    const ctx = document.getElementById('cpmkChart').getContext('2d');
    
    // Destroy existing chart if exists
    if (cpmkChart) {
        cpmkChart.destroy();
    }

    // Create gradient
    const gradient = ctx.createLinearGradient(0, 0, 0, 400);
    gradient.addColorStop(0, 'rgba(54, 162, 235, 0.8)');
    gradient.addColorStop(1, 'rgba(54, 162, 235, 0.2)');

    cpmkChart = new Chart(ctx, {
        type: 'bar',
        data: {
            labels: response.chartData.labels,
            datasets: [{
                label: 'Rata-rata Nilai CPMK',
                data: response.chartData.data,
                backgroundColor: gradient,
                borderColor: 'rgba(54, 162, 235, 1)',
                borderWidth: 2,
                borderRadius: 5,
                barThickness: 50
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: true,
            plugins: {
                legend: {
                    display: true,
                    position: 'top'
                },
                title: {
                    display: true,
                    text: 'Rata-rata Capaian CPMK',
                    font: {
                        size: 16,
                        weight: 'bold'
                    }
                },
                tooltip: {
                    backgroundColor: 'rgba(0, 0, 0, 0.8)',
                    padding: 12,
                    callbacks: {
                        label: function(context) {
                            return 'Rata-rata: ' + context.parsed.y.toFixed(2);
                        }
                    }
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    max: 100,
                    ticks: {
                        callback: function(value) {
                            return value;
                        }
                    },
                    grid: {
                        display: true,
                        color: 'rgba(0, 0, 0, 0.05)'
                    }
                },
                x: {
                    grid: {
                        display: false
                    }
                }
            }
        }
    });
}

function displayDetailTable(details) {
    let html = '';
    details.forEach((item, index) => {
        const statusClass = item.rata_rata >= 75 ? 'success' : (item.rata_rata >= 60 ? 'warning' : 'danger');
        const statusText = item.rata_rata >= 75 ? 'Baik' : (item.rata_rata >= 60 ? 'Cukup' : 'Kurang');
        
        html += `
            <tr>
                <td>${index + 1}</td>
                <td><strong>${item.kode_cpmk}</strong></td>
                <td>${item.deskripsi}</td>
                <td class="text-center">${item.jumlah_mahasiswa}</td>
                <td class="text-center"><strong>${item.rata_rata.toFixed(2)}</strong></td>
                <td class="text-center"><span class="badge bg-${statusClass}">${statusText}</span></td>
                <td class="text-center">
                    <button class="btn btn-sm btn-info" onclick="showDetail(${item.cpmk_id}, '${item.kode_cpmk}')">
                        <i class="bi bi-eye"></i> Lihat Detail
                    </button>
                </td>
            </tr>
        `;
    });
    $('#detailTableBody').html(html);
}

function showDetail(cpmkId, kodeCpmk) {
    $('#detailModalTitle').text(`Detail Nilai ${kodeCpmk}`);
    $('#detailModal').modal('show');
    
    // Show loading
    $('#detailModalContent').html(`
        <div class="text-center py-4">
            <div class="spinner-border text-primary" role="status">
                <span class="visually-hidden">Loading...</span>
            </div>
        </div>
    `);
    
    $.ajax({
        url: '<?= base_url("admin/capaian-cpmk/detail-data") ?>',
        method: 'GET',
        data: {
            mata_kuliah_id: $('#mataKuliahSelect').val(),
            tahun_akademik: $('#tahunAkademikSelect').val(),
            kelas: $('#kelasSelect').val(),
            cpmk_id: cpmkId
        },
        success: function(response) {
            if (response.success) {
                displayDetailModal(response.data, response.cpmk);
            } else {
                $('#detailModalContent').html(`
                    <div class="alert alert-danger">${response.message}</div>
                `);
            }
        },
        error: function() {
            $('#detailModalContent').html(`
                <div class="alert alert-danger">Terjadi kesalahan saat memuat data</div>
            `);
        }
    });
}

function getCpmkIdFromKode(kodeCpmk) {
    // We need to get CPMK ID from the server
    // For now, we'll pass the code and let the server handle it
    // But ideally, we should include the ID in the chart data
    
    // Find from current data structure - we need to enhance the controller to include cpmk_id
    const allCpmk = <?= json_encode(array_map(function($mk) {
        $db = \Config\Database::connect();
        $builder = $db->table('cpmk_mk');
        $result = $builder
            ->select('cpmk.id, cpmk.kode_cpmk')
            ->join('cpmk', 'cpmk.id = cpmk_mk.cpmk_id')
            ->where('cpmk_mk.mata_kuliah_id', $mk['id'])
            ->get()
            ->getResultArray();
        return $result;
    }, $mataKuliah)) ?>;
    
    // Find in the nested array structure
    const mataKuliahId = $('#mataKuliahSelect').val();
    const cpmkList = allCpmk[mataKuliahId] || [];
    const cpmk = cpmkList.find(c => c.kode_cpmk === kodeCpmk);
    
    return cpmk ? cpmk.id : null;
}

function displayDetailModal(data, cpmk) {
    let html = `
        <div class="mb-3">
            <h6><strong>CPMK:</strong> ${cpmk.kode_cpmk}</h6>
            <p class="text-muted">${cpmk.deskripsi}</p>
        </div>
        <div class="table-responsive">
            <table class="table table-bordered table-striped table-sm">
                <thead class="table-primary">
                    <tr>
                        <th width="10%">No</th>
                        <th width="20%">NIM</th>
                        <th width="50%">Nama Mahasiswa</th>
                        <th width="20%" class="text-center">Nilai</th>
                    </tr>
                </thead>
                <tbody>
    `;
    
    if (data.length === 0) {
        html += `
            <tr>
                <td colspan="4" class="text-center text-muted">Belum ada data nilai mahasiswa</td>
            </tr>
        `;
    } else {
        data.forEach((item, index) => {
            const badgeClass = item.nilai_cpmk >= 75 ? 'success' : (item.nilai_cpmk >= 60 ? 'warning' : 'danger');
            html += `
                <tr>
                    <td>${index + 1}</td>
                    <td>${item.nim}</td>
                    <td>${item.nama_lengkap}</td>
                    <td class="text-center">
                        <span class="badge bg-${badgeClass}">${parseFloat(item.nilai_cpmk).toFixed(2)}</span>
                    </td>
                </tr>
            `;
        });
    }
    
    html += `
                </tbody>
            </table>
        </div>
    `;
    
    $('#detailModalContent').html(html);
}
</script>

<?= $this->endSection() ?>