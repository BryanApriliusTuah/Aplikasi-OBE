<?= $this->extend('layouts/admin_layout') ?>
<?= $this->section('content') ?>

<!-- Include Modern Table Styles -->
<link rel="stylesheet" href="<?= base_url('css/modern-table.css') ?>">

<?php if (session()->getFlashdata('success')): ?>
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <?= session()->getFlashdata('success') ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
<?php endif; ?>
<?php if (session()->getFlashdata('error')): ?>
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <?= session()->getFlashdata('error') ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
<?php endif; ?>

<div class="container-fluid px-0">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="fw-bold mb-0">Daftar CPL</h2>
        <?php if (session('role') === 'admin'): ?>
        <div class="d-flex gap-2">
            <div class="btn-group">
                <button class="btn btn-outline-success dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false">
                    <i class="bi bi-download"></i> Download
                </button>
                <ul class="dropdown-menu dropdown-menu-end">
                    <li>
                        <a class="dropdown-item" href="<?= base_url('admin/cpl/exportPdf') ?>">
                            <i class="bi bi-file-earmark-pdf text-danger"></i> PDF
                        </a>
                    </li>
                    <li>
                        <a class="dropdown-item" href="<?= base_url('admin/cpl/exportExcel') ?>">
                            <i class="bi bi-file-earmark-excel text-success"></i> Excel
                        </a>
                    </li>
                </ul>
            </div>
            <a href="<?= base_url('admin/cpl/create') ?>" class="btn btn-primary">
                <i class="bi bi-plus-circle"></i> Tambah CPL
            </a>
        </div>
        <?php endif; ?>
    </div>

    <div class="shadow-sm border-0">
        <div class="p-0">
            <?php if (empty($cpl)): ?>
                <div class="text-center text-muted py-5">
                    <i class="bi bi-journal-x fs-1"></i>
                    <p class="mt-3 fw-semibold">Belum ada data CPL</p>
                    <p class="small">Silakan tambah data CPL</p>
                </div>
            <?php else: ?>
                <div class="modern-table-wrapper">
                    <div class="scroll-indicator"></div>
                    <table class="modern-table" id="cplTable">
                        <thead>
                            <tr>
                                <th class="text-center" style="width: 50px;">No</th>
                                <th class="text-center" style="width: 120px;">Kode CPL</th>
                                <th class="text-start">Deskripsi</th>
                                <th class="text-center" style="width: 150px;">Jenis CPL</th>
                                <?php if (session('role') === 'admin'): ?>
                                    <th class="text-center" style="width: 110px;">Aksi</th>
                                <?php endif; ?>
                            </tr>
                        </thead>
                        <tbody>
                            <?php $no = 1; foreach ($cpl as $row): ?>
                            <tr>
                                <td class="text-center fw-semibold text-muted"><?= $no++ ?></td>
                                <td class="text-center"><?= esc($row['kode_cpl']) ?></td>
                                <td class="text-start" style="white-space: normal;"><?= esc($row['deskripsi']) ?></td>
                                <td class="text-center"><?= esc($row['jenis_cpl']) ?></td>
                                <?php if (session('role') === 'admin'): ?>
                                    <td class="text-center">
                                        <div class="d-flex justify-content-center align-items-center gap-1">
                                            <a href="<?= base_url('admin/cpl/edit/' . $row['id']) ?>" class="btn btn-sm btn-outline-primary" title="Edit">
                                                <i class="bi bi-pencil-square"></i>
                                            </a>
                                            <button type="button"
                                                class="btn btn-sm btn-outline-danger btn-hapus"
                                                data-id="<?= $row['id'] ?>"
                                                data-kode="<?= esc($row['kode_cpl']) ?>"
                                                data-deskripsi="<?= esc($row['deskripsi']) ?>"
                                                data-bs-toggle="modal"
                                                data-bs-target="#modalHapus"
                                                title="Hapus">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                        </div>
                                    </td>
                                <?php endif; ?>
                            </tr>
                            <?php endforeach ?>
                        </tbody>
                    </table>
                </div>

                <!-- Pagination Container -->
                <div id="cplTable_pagination" class="d-flex justify-content-between align-items-center mt-3 px-3 pb-3"></div>

                <div class="card-footer bg-light border-0 py-3">
                    <div class="d-flex align-items-center gap-3 justify-content-center">
                        <small class="text-muted">
                            <i class="bi bi-info-circle me-1"></i>
                            Total: <?= count($cpl) ?> CPL
                        </small>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php if (session('role') === 'admin'): ?>
<div class="modal fade" id="modalHapus" tabindex="-1" aria-labelledby="modalHapusLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalHapusLabel">Konfirmasi Hapus</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                Yakin ingin menghapus data ini?<br>
                <strong id="modalKodeCPL"></strong>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                <a href="#" id="btnDeleteCPL" class="btn btn-danger">Hapus</a>
            </div>
        </div>
    </div>
</div>
<?php endif; ?>

<?= $this->section('js') ?>
<script>
document.addEventListener('DOMContentLoaded', function () {
    // Modal delete handler
    <?php if (session('role') === 'admin'): ?>
    var modalHapus = document.getElementById('modalHapus');
    modalHapus.addEventListener('show.bs.modal', function (event) {
        var button = event.relatedTarget;
        var id = button.getAttribute('data-id');
        var kode = button.getAttribute('data-kode');
        var deskripsi = button.getAttribute('data-deskripsi');
        document.getElementById('modalKodeCPL').textContent = kode + ' - ' + deskripsi;
        document.getElementById('btnDeleteCPL').href = "<?= base_url('admin/cpl/delete/') ?>" + id;
    });
    <?php endif; ?>

    // Scroll indicator
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

        checkScroll();
        window.addEventListener('resize', checkScroll);
        tableWrapper.addEventListener('scroll', checkScroll);
    }

    // Pagination
    function initPagination(tableId, rowsPerPage) {
        const table = document.getElementById(tableId);
        if (!table) return;

        const tbody = table.querySelector('tbody');
        const rows = Array.from(tbody.querySelectorAll('tr'));
        const totalRows = rows.length;
        const totalPages = Math.ceil(totalRows / rowsPerPage);
        let currentPage = 1;

        const paginationId = `${tableId}_pagination`;
        let paginationContainer = document.getElementById(paginationId);

        if (!paginationContainer) {
            paginationContainer = document.createElement('div');
            paginationContainer.id = paginationId;
            paginationContainer.className = 'd-flex justify-content-between align-items-center mt-3 px-3 pb-3';
            table.parentElement.parentElement.appendChild(paginationContainer);
        }

        function showPage(page) {
            currentPage = page;
            const start = (page - 1) * rowsPerPage;
            const end = start + rowsPerPage;

            rows.forEach((row, index) => {
                row.style.display = (index >= start && index < end) ? '' : 'none';
            });

            let visibleNum = start + 1;
            rows.forEach((row, index) => {
                if (index >= start && index < end) {
                    const firstCell = row.querySelector('td:first-child');
                    if (firstCell) firstCell.textContent = visibleNum++;
                }
            });

            renderPagination();

            const wrapper = table.closest('.modern-table-wrapper');
            if (wrapper) wrapper.scrollTop = 0;
        }

        function renderPagination() {
            if (totalRows <= rowsPerPage) {
                paginationContainer.innerHTML = '';
                return;
            }

            const startEntry = totalRows === 0 ? 0 : ((currentPage - 1) * rowsPerPage) + 1;
            const endEntry = Math.min(currentPage * rowsPerPage, totalRows);

            let html = `
                <div class="text-muted small">
                    Menampilkan ${startEntry} sampai ${endEntry} dari ${totalRows} data
                </div>
                <nav>
                    <ul class="pagination pagination-sm mb-0">
            `;

            html += `
                <li class="page-item ${currentPage === 1 ? 'disabled' : ''}">
                    <a class="page-link" href="#" data-page="${currentPage - 1}">Previous</a>
                </li>
            `;

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

        showPage(1);
    }

    initPagination('cplTable', 10);
});
</script>
<?= $this->endSection() ?>

<?= $this->endSection() ?>
