<?= $this->extend('layouts/admin_layout') ?>

<?= $this->section('content') ?>

<div class="card">
    <div class="card-body">
        <h2 class="mb-4">Capaian CPL Mahasiswa</h2>

        <!-- Tab Navigation -->
        <ul class="nav nav-tabs mb-4" id="cplTabs" role="tablist">
            <li class="nav-item" role="presentation">
                <button class="nav-link active" id="individual-tab" data-bs-toggle="tab" data-bs-target="#individual" type="button" role="tab">
                    <i class="bi bi-person"></i> Individual
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="comparative-tab" data-bs-toggle="tab" data-bs-target="#comparative" type="button" role="tab">
                    <i class="bi bi-people"></i> Komparatif (Angkatan)
                </button>
            </li>
        </ul>

        <!-- Tab Content -->
        <div class="tab-content" id="cplTabContent">
            <!-- Individual Tab -->
            <div class="tab-pane fade show active" id="individual" role="tabpanel">
                <!-- Filter Section Individual -->
                <div class="card mb-4">
                    <div class="card-header bg-light">
                        <h5 class="mb-0"><i class="bi bi-funnel"></i> Filter Mahasiswa</h5>
                    </div>
                    <div class="card-body">
                        <form id="filterIndividualForm">
                            <div class="row g-3">
                                <div class="col-md-4">
                                    <label for="programStudiSelect" class="form-label">Program Studi</label>
                                    <select class="form-select" id="programStudiSelect" name="program_studi">
                                        <option value="">-- Semua Program Studi --</option>
                                        <?php foreach ($programStudi as $prodi): ?>
                                            <option value="<?= esc($prodi) ?>"><?= esc($prodi) ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                                <div class="col-md-3">
                                    <label for="tahunAngkatanSelect" class="form-label">Tahun Angkatan</label>
                                    <select class="form-select" id="tahunAngkatanSelect" name="tahun_angkatan">
                                        <option value="">-- Semua Tahun --</option>
                                        <?php foreach ($tahunAngkatan as $tahun): ?>
                                            <option value="<?= esc($tahun) ?>"><?= esc($tahun) ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                                <div class="col-md-4">
                                    <label for="mahasiswaSelect" class="form-label">Mahasiswa <span class="text-danger">*</span></label>
                                    <select class="form-select" id="mahasiswaSelect" name="mahasiswa_id" required disabled>
                                        <option value="">-- Pilih Mahasiswa --</option>
                                    </select>
                                </div>
                                <div class="col-md-1 d-flex align-items-end">
                                    <button type="submit" class="btn btn-primary w-100">
                                        <i class="bi bi-search"></i>
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- Info Section Individual -->
                <div id="infoSectionIndividual" class="alert alert-info d-none">
                    <h6 class="mb-2"><strong>Informasi Mahasiswa:</strong></h6>
                    <div id="infoContentIndividual"></div>
                </div>

                <!-- Chart Section Individual -->
                <div id="chartSectionIndividual" class="d-none">
                    <div class="card">
                        <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                            <h5 class="mb-0"><i class="bi bi-bar-chart-fill"></i> Grafik Capaian CPL</h5>
                            <button class="btn btn-light btn-sm" id="exportChartIndividualBtn">
                                <i class="bi bi-download"></i> Export PNG
                            </button>
                        </div>
                        <div class="card-body">
                            <canvas id="cplChartIndividual" height="80"></canvas>
                        </div>
                    </div>

                    <!-- Detail Table Individual -->
                    <div class="card mt-4">
                        <div class="card-header bg-secondary text-white">
                            <h5 class="mb-0"><i class="bi bi-table"></i> Detail Capaian CPL</h5>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-bordered table-hover" id="detailTableIndividual">
                                    <thead class="table-light">
                                        <tr>
                                            <th width="5%">No</th>
                                            <th width="10%">Kode CPL</th>
                                            <th width="35%">Deskripsi CPL</th>
                                            <th width="15%">Jenis CPL</th>
                                            <th width="10%" class="text-center">Jumlah CPMK</th>
                                            <th width="10%" class="text-center">Rata-rata</th>
                                            <th width="10%" class="text-center">Status</th>
                                            <th width="5%" class="text-center">Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody id="detailTableBodyIndividual">
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Empty State Individual -->
                <div id="emptyStateIndividual" class="text-center py-5">
                    <i class="bi bi-bar-chart" style="font-size: 4rem; color: #ccc;"></i>
                    <p class="text-muted mt-3">Pilih mahasiswa dan klik tombol search untuk melihat grafik capaian CPL</p>
                </div>
            </div>

            <!-- Comparative Tab -->
            <div class="tab-pane fade" id="comparative" role="tabpanel">
                <!-- Filter Section Comparative -->
                <div class="card mb-4">
                    <div class="card-header bg-light">
                        <h5 class="mb-0"><i class="bi bi-funnel"></i> Filter Angkatan</h5>
                    </div>
                    <div class="card-body">
                        <form id="filterComparativeForm">
                            <div class="row g-3">
                                <div class="col-md-5">
                                    <label for="programStudiComparativeSelect" class="form-label">Program Studi <span class="text-danger">*</span></label>
                                    <select class="form-select" id="programStudiComparativeSelect" name="program_studi" required>
                                        <option value="">-- Pilih Program Studi --</option>
                                        <?php foreach ($programStudi as $prodi): ?>
                                            <option value="<?= esc($prodi) ?>"><?= esc($prodi) ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                                <div class="col-md-5">
                                    <label for="tahunAngkatanComparativeSelect" class="form-label">Tahun Angkatan <span class="text-danger">*</span></label>
                                    <select class="form-select" id="tahunAngkatanComparativeSelect" name="tahun_angkatan" required>
                                        <option value="">-- Pilih Tahun Angkatan --</option>
                                        <?php foreach ($tahunAngkatan as $tahun): ?>
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

                <!-- Info Section Comparative -->
                <div id="infoSectionComparative" class="alert alert-info d-none">
                    <h6 class="mb-2"><strong>Informasi:</strong></h6>
                    <div id="infoContentComparative"></div>
                </div>

                <!-- Chart Section Comparative -->
                <div id="chartSectionComparative" class="d-none">
                    <div class="card">
                        <div class="card-header bg-success text-white d-flex justify-content-between align-items-center">
                            <h5 class="mb-0"><i class="bi bi-bar-chart-fill"></i> Grafik Rata-rata Capaian CPL (Angkatan)</h5>
                            <button class="btn btn-light btn-sm" id="exportChartComparativeBtn">
                                <i class="bi bi-download"></i> Export PNG
                            </button>
                        </div>
                        <div class="card-body">
                            <canvas id="cplChartComparative" height="80"></canvas>
                        </div>
                    </div>

                    <!-- Detail Table Comparative -->
                    <div class="card mt-4">
                        <div class="card-header bg-secondary text-white">
                            <h5 class="mb-0"><i class="bi bi-table"></i> Detail Rata-rata CPL</h5>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-bordered table-hover">
                                    <thead class="table-light">
                                        <tr>
                                            <th width="5%">No</th>
                                            <th width="12%">Kode CPL</th>
                                            <th width="40%">Deskripsi CPL</th>
                                            <th width="15%">Jenis CPL</th>
                                            <th width="13%" class="text-center">Jumlah Mahasiswa</th>
                                            <th width="10%" class="text-center">Rata-rata</th>
                                            <th width="5%" class="text-center">Status</th>
                                        </tr>
                                    </thead>
                                    <tbody id="detailTableBodyComparative">
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Empty State Comparative -->
                <div id="emptyStateComparative" class="text-center py-5">
                    <i class="bi bi-people" style="font-size: 4rem; color: #ccc;"></i>
                    <p class="text-muted mt-3">Pilih program studi dan tahun angkatan untuk melihat grafik rata-rata capaian CPL</p>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal Detail CPL -->
<div class="modal fade" id="detailCplModal" tabindex="-1">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title" id="detailCplModalTitle">Detail Capaian CPL</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div id="detailCplModalContent">
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
let cplChartIndividual = null;
let cplChartComparative = null;
let currentIndividualData = null;
let currentComparativeData = null;

$(document).ready(function() {
    // Load mahasiswa when filter changes
    $('#programStudiSelect, #tahunAngkatanSelect').on('change', function() {
        loadMahasiswa();
    });

    // Load initial mahasiswa list
    loadMahasiswa();

    // Individual form submit
    $('#filterIndividualForm').on('submit', function(e) {
        e.preventDefault();
        loadIndividualChartData();
    });

    // Comparative form submit
    $('#filterComparativeForm').on('submit', function(e) {
        e.preventDefault();
        loadComparativeChartData();
    });

    // Export buttons
    $('#exportChartIndividualBtn').on('click', function() {
        if (cplChartIndividual) {
            const link = document.createElement('a');
            link.download = 'capaian-cpl-individual.png';
            link.href = cplChartIndividual.toBase64Image();
            link.click();
        }
    });

    $('#exportChartComparativeBtn').on('click', function() {
        if (cplChartComparative) {
            const link = document.createElement('a');
            link.download = 'capaian-cpl-comparative.png';
            link.href = cplChartComparative.toBase64Image();
            link.click();
        }
    });
});

function loadMahasiswa() {
    const programStudi = $('#programStudiSelect').val();
    const tahunAngkatan = $('#tahunAngkatanSelect').val();

    $.ajax({
        url: '<?= base_url("admin/capaian-cpl/mahasiswa") ?>',
        method: 'GET',
        data: {
            program_studi: programStudi,
            tahun_angkatan: tahunAngkatan
        },
        success: function(response) {
            const mahasiswaSelect = $('#mahasiswaSelect');
            mahasiswaSelect.html('<option value="">-- Pilih Mahasiswa --</option>');
            
            if (response.length > 0) {
                response.forEach(function(mhs) {
                    mahasiswaSelect.append(`<option value="${mhs.id}">${mhs.nim} - ${mhs.nama_lengkap}</option>`);
                });
                mahasiswaSelect.prop('disabled', false);
            } else {
                mahasiswaSelect.prop('disabled', true);
            }
        }
    });
}

function loadIndividualChartData() {
    const mahasiswaId = $('#mahasiswaSelect').val();

    if (!mahasiswaId) {
        alert('Silakan pilih mahasiswa terlebih dahulu');
        return;
    }

    // Show loading
    $('#emptyStateIndividual').addClass('d-none');
    $('#chartSectionIndividual').removeClass('d-none').html(`
        <div class="text-center py-5">
            <div class="spinner-border text-primary" role="status">
                <span class="visually-hidden">Loading...</span>
            </div>
            <p class="text-muted mt-3">Memuat data...</p>
        </div>
    `);

    $.ajax({
        url: '<?= base_url("admin/capaian-cpl/chart-data") ?>',
        method: 'GET',
        data: {
            mahasiswa_id: mahasiswaId,
            program_studi: $('#programStudiSelect').val(),
            tahun_angkatan: $('#tahunAngkatanSelect').val()
        },
        success: function(response) {
            if (response.success) {
                currentIndividualData = response;
                displayIndividualChart(response);
                displayIndividualInfo(response);
                displayIndividualDetailTable(response.chartData.details);
            } else {
                $('#chartSectionIndividual').addClass('d-none');
                $('#emptyStateIndividual').removeClass('d-none').html(`
                    <i class="bi bi-exclamation-circle" style="font-size: 4rem; color: #dc3545;"></i>
                    <p class="text-danger mt-3">${response.message}</p>
                `);
            }
        },
        error: function() {
            $('#chartSectionIndividual').addClass('d-none');
            $('#emptyStateIndividual').removeClass('d-none').html(`
                <i class="bi bi-exclamation-triangle" style="font-size: 4rem; color: #ffc107;"></i>
                <p class="text-warning mt-3">Terjadi kesalahan saat memuat data</p>
            `);
        }
    });
}

function displayIndividualInfo(response) {
    const info = `
        <div class="row">
            <div class="col-md-3">
                <strong>NIM:</strong> ${response.mahasiswa.nim}
            </div>
            <div class="col-md-4">
                <strong>Nama:</strong> ${response.mahasiswa.nama_lengkap}
            </div>
            <div class="col-md-3">
                <strong>Program Studi:</strong> ${response.mahasiswa.program_studi}
            </div>
            <div class="col-md-2">
                <strong>Angkatan:</strong> ${response.mahasiswa.tahun_angkatan}
            </div>
        </div>
    `;
    $('#infoContentIndividual').html(info);
    $('#infoSectionIndividual').removeClass('d-none');
}

function displayIndividualChart(response) {
    $('#chartSectionIndividual').html(`
        <div class="card">
            <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                <h5 class="mb-0"><i class="bi bi-bar-chart-fill"></i> Grafik Capaian CPL</h5>
                <button class="btn btn-light btn-sm" id="exportChartIndividualBtn">
                    <i class="bi bi-download"></i> Export PNG
                </button>
            </div>
            <div class="card-body">
                <canvas id="cplChartIndividual" height="80"></canvas>
            </div>
        </div>
        <div class="card mt-4">
            <div class="card-header bg-secondary text-white">
                <h5 class="mb-0"><i class="bi bi-table"></i> Detail Capaian CPL</h5>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered table-hover">
                        <thead class="table-light">
                            <tr>
                                <th width="5%">No</th>
                                <th width="10%">Kode CPL</th>
                                <th width="35%">Deskripsi CPL</th>
                                <th width="15%">Jenis CPL</th>
                                <th width="10%" class="text-center">Jumlah CPMK</th>
                                <th width="10%" class="text-center">Rata-rata</th>
                                <th width="10%" class="text-center">Status</th>
                                <th width="5%" class="text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody id="detailTableBodyIndividual">
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    `);

    // Re-bind export button
    $('#exportChartIndividualBtn').on('click', function() {
        if (cplChartIndividual) {
            const link = document.createElement('a');
            link.download = 'capaian-cpl-individual.png';
            link.href = cplChartIndividual.toBase64Image();
            link.click();
        }
    });

    const ctx = document.getElementById('cplChartIndividual').getContext('2d');
    
    if (cplChartIndividual) {
        cplChartIndividual.destroy();
    }

    const gradient = ctx.createLinearGradient(0, 0, 0, 400);
    gradient.addColorStop(0, 'rgba(13, 110, 253, 0.8)');
    gradient.addColorStop(1, 'rgba(13, 110, 253, 0.2)');

    cplChartIndividual = new Chart(ctx, {
        type: 'bar',
        data: {
            labels: response.chartData.labels,
            datasets: [{
                label: 'Capaian CPL',
                data: response.chartData.data,
                backgroundColor: gradient,
                borderColor: 'rgba(13, 110, 253, 1)',
                borderWidth: 2,
                borderRadius: 5,
                barThickness: 40
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
                    text: 'Capaian CPL Mahasiswa',
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
                            return 'Nilai: ' + context.parsed.y.toFixed(2);
                        }
                    }
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    max: 100,
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

function displayIndividualDetailTable(details) {
    let html = '';
    details.forEach((item, index) => {
        const statusClass = item.rata_rata >= 75 ? 'success' : (item.rata_rata >= 60 ? 'warning' : 'danger');
        const statusText = item.rata_rata >= 75 ? 'Baik' : (item.rata_rata >= 60 ? 'Cukup' : 'Kurang');
        
        html += `
            <tr>
                <td>${index + 1}</td>
                <td><strong>${item.kode_cpl}</strong></td>
                <td>${item.deskripsi}</td>
                <td><span class="badge bg-info">${item.jenis_cpl}</span></td>
                <td class="text-center">${item.jumlah_cpmk}</td>
                <td class="text-center"><strong>${item.rata_rata.toFixed(2)}</strong></td>
                <td class="text-center"><span class="badge bg-${statusClass}">${statusText}</span></td>
                <td class="text-center">
                    <button class="btn btn-sm btn-info" onclick="showCplDetail(${item.cpl_id}, '${item.kode_cpl}')">
                        <i class="bi bi-eye"></i>
                    </button>
                </td>
            </tr>
        `;
    });
    $('#detailTableBodyIndividual').html(html);
}

function loadComparativeChartData() {
    const programStudi = $('#programStudiComparativeSelect').val();
    const tahunAngkatan = $('#tahunAngkatanComparativeSelect').val();

    if (!programStudi || !tahunAngkatan) {
        alert('Silakan pilih program studi dan tahun angkatan');
        return;
    }

    // Show loading
    $('#emptyStateComparative').addClass('d-none');
    $('#chartSectionComparative').removeClass('d-none').html(`
        <div class="text-center py-5">
            <div class="spinner-border text-success" role="status">
                <span class="visually-hidden">Loading...</span>
            </div>
            <p class="text-muted mt-3">Memuat data...</p>
        </div>
    `);

    $.ajax({
        url: '<?= base_url("admin/capaian-cpl/comparative-data") ?>',
        method: 'GET',
        data: {
            program_studi: programStudi,
            tahun_angkatan: tahunAngkatan
        },
        success: function(response) {
            if (response.success) {
                currentComparativeData = response;
                displayComparativeChart(response);
                displayComparativeInfo(response);
                displayComparativeDetailTable(response.chartData.details);
            } else {
                $('#chartSectionComparative').addClass('d-none');
                $('#emptyStateComparative').removeClass('d-none').html(`
                    <i class="bi bi-exclamation-circle" style="font-size: 4rem; color: #dc3545;"></i>
                    <p class="text-danger mt-3">${response.message}</p>
                `);
            }
        },
        error: function() {
            $('#chartSectionComparative').addClass('d-none');
            $('#emptyStateComparative').removeClass('d-none').html(`
                <i class="bi bi-exclamation-triangle" style="font-size: 4rem; color: #ffc107;"></i>
                <p class="text-warning mt-3">Terjadi kesalahan saat memuat data</p>
            `);
        }
    });
}

function displayComparativeInfo(response) {
    const info = `
        <div class="row">
            <div class="col-md-4">
                <strong>Program Studi:</strong> ${response.programStudi}
            </div>
            <div class="col-md-4">
                <strong>Tahun Angkatan:</strong> ${response.tahunAngkatan}
            </div>
            <div class="col-md-4">
                <strong>Total Mahasiswa:</strong> ${response.totalMahasiswa} orang
            </div>
        </div>
    `;
    $('#infoContentComparative').html(info);
    $('#infoSectionComparative').removeClass('d-none');
}

function displayComparativeChart(response) {
    $('#chartSectionComparative').html(`
        <div class="card">
            <div class="card-header bg-success text-white d-flex justify-content-between align-items-center">
                <h5 class="mb-0"><i class="bi bi-bar-chart-fill"></i> Grafik Rata-rata Capaian CPL (Angkatan)</h5>
                <button class="btn btn-light btn-sm" id="exportChartComparativeBtn">
                    <i class="bi bi-download"></i> Export PNG
                </button>
            </div>
            <div class="card-body">
                <canvas id="cplChartComparative" height="80"></canvas>
            </div>
        </div>
        <div class="card mt-4">
            <div class="card-header bg-secondary text-white">
                <h5 class="mb-0"><i class="bi bi-table"></i> Detail Rata-rata CPL</h5>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered table-hover">
                        <thead class="table-light">
                            <tr>
                                <th width="5%">No</th>
                                <th width="12%">Kode CPL</th>
                                <th width="40%">Deskripsi CPL</th>
                                <th width="15%">Jenis CPL</th>
                                <th width="13%" class="text-center">Jumlah Mahasiswa</th>
                                <th width="10%" class="text-center">Rata-rata</th>
                                <th width="5%" class="text-center">Status</th>
                            </tr>
                        </thead>
                        <tbody id="detailTableBodyComparative">
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    `);

    // Re-bind export button
    $('#exportChartComparativeBtn').on('click', function() {
        if (cplChartComparative) {
            const link = document.createElement('a');
            link.download = 'capaian-cpl-comparative.png';
            link.href = cplChartComparative.toBase64Image();
            link.click();
        }
    });

    const ctx = document.getElementById('cplChartComparative').getContext('2d');
    
    if (cplChartComparative) {
        cplChartComparative.destroy();
    }

    const gradient = ctx.createLinearGradient(0, 0, 0, 400);
    gradient.addColorStop(0, 'rgba(25, 135, 84, 0.8)');
    gradient.addColorStop(1, 'rgba(25, 135, 84, 0.2)');

    cplChartComparative = new Chart(ctx, {
        type: 'bar',
        data: {
            labels: response.chartData.labels,
            datasets: [{
                label: 'Rata-rata CPL',
                data: response.chartData.data,
                backgroundColor: gradient,
                borderColor: 'rgba(25, 135, 84, 1)',
                borderWidth: 2,
                borderRadius: 5,
                barThickness: 40
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
                    text: 'Rata-rata Capaian CPL (Angkatan)',
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

function displayComparativeDetailTable(details) {
    let html = '';
    details.forEach((item, index) => {
        const statusClass = item.rata_rata >= 75 ? 'success' : (item.rata_rata >= 60 ? 'warning' : 'danger');
        const statusText = item.rata_rata >= 75 ? 'Baik' : (item.rata_rata >= 60 ? 'Cukup' : 'Kurang');
        
        html += `
            <tr>
                <td>${index + 1}</td>
                <td><strong>${item.kode_cpl}</strong></td>
                <td>${item.deskripsi}</td>
                <td><span class="badge bg-info">${item.jenis_cpl}</span></td>
                <td class="text-center">${item.jumlah_mahasiswa}</td>
                <td class="text-center"><strong>${item.rata_rata.toFixed(2)}</strong></td>
                <td class="text-center"><span class="badge bg-${statusClass}">${statusText}</span></td>
            </tr>
        `;
    });
    $('#detailTableBodyComparative').html(html);
}

function showCplDetail(cplId, kodeCpl) {
    const mahasiswaId = $('#mahasiswaSelect').val();
    
    $('#detailCplModalTitle').text(`Detail Capaian ${kodeCpl}`);
    $('#detailCplModal').modal('show');
    
    // Show loading
    $('#detailCplModalContent').html(`
        <div class="text-center py-4">
            <div class="spinner-border text-primary" role="status">
                <span class="visually-hidden">Loading...</span>
            </div>
        </div>
    `);
    
    $.ajax({
        url: '<?= base_url("admin/capaian-cpl/detail-data") ?>',
        method: 'GET',
        data: {
            mahasiswa_id: mahasiswaId,
            cpl_id: cplId
        },
        success: function(response) {
            if (response.success) {
                displayCplDetailModal(response.data, response.cpl);
            } else {
                $('#detailCplModalContent').html(`
                    <div class="alert alert-warning">${response.message || 'Tidak ada data'}</div>
                `);
            }
        },
        error: function() {
            $('#detailCplModalContent').html(`
                <div class="alert alert-danger">Terjadi kesalahan saat memuat data</div>
            `);
        }
    });
}

function displayCplDetailModal(data, cpl) {
    let html = `
        <div class="mb-4">
            <h6><strong>CPL:</strong> ${cpl.kode_cpl}</h6>
            <p class="text-muted">${cpl.deskripsi}</p>
            <span class="badge bg-info">Jenis: ${getJenisCplLabel(cpl.jenis_cpl)}</span>
        </div>
    `;
    
    if (data.length === 0) {
        html += `
            <div class="alert alert-info">
                <i class="bi bi-info-circle"></i> Belum ada data CPMK yang terkait dengan CPL ini atau mahasiswa belum memiliki nilai.
            </div>
        `;
    } else {
        html += `
            <div class="table-responsive">
                <table class="table table-bordered table-striped">
                    <thead class="table-primary">
                        <tr>
                            <th width="5%">No</th>
                            <th width="12%">Kode CPMK</th>
                            <th width="38%">Deskripsi CPMK</th>
                            <th width="10%" class="text-center">Jumlah MK</th>
                            <th width="10%" class="text-center">Rata-rata</th>
                            <th width="25%">Detail Mata Kuliah</th>
                        </tr>
                    </thead>
                    <tbody>
        `;
        
        data.forEach((item, index) => {
            const badgeClass = item.rata_rata >= 75 ? 'success' : (item.rata_rata >= 60 ? 'warning' : 'danger');
            
            let detailMk = '<ul class="mb-0" style="font-size: 0.85rem;">';
            if (item.detail_mk.length === 0) {
                detailMk += '<li class="text-muted">Belum ada nilai</li>';
            } else {
                item.detail_mk.forEach(mk => {
                    detailMk += `<li>${mk.kode_mk} (${mk.tahun_akademik} - ${mk.kelas}): <strong>${parseFloat(mk.nilai_cpmk).toFixed(2)}</strong></li>`;
                });
            }
            detailMk += '</ul>';
            
            html += `
                <tr>
                    <td>${index + 1}</td>
                    <td><strong>${item.kode_cpmk}</strong></td>
                    <td><small>${item.deskripsi_cpmk}</small></td>
                    <td class="text-center">${item.jumlah_nilai}</td>
                    <td class="text-center">
                        <span class="badge bg-${badgeClass}">${item.rata_rata.toFixed(2)}</span>
                    </td>
                    <td>${detailMk}</td>
                </tr>
            `;
        });
        
        html += `
                    </tbody>
                </table>
            </div>
        `;
    }
    
    $('#detailCplModalContent').html(html);
}

function getJenisCplLabel(jenis) {
    const labels = {
        'P': 'Pengetahuan',
        'KK': 'Keterampilan Khusus',
        'S': 'Sikap',
        'KU': 'Keterampilan Umum'
    };
    return labels[jenis] || jenis;
}
</script>

<?= $this->endSection() ?>