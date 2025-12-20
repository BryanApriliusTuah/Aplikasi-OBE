<?php
/**
 * Modern Filter Component
 *
 * A reusable filter component that matches the modern table design aesthetic.
 *
 * Usage Example:
 * <?= view('components/modern_filter', [
 *     'title' => 'Filter Jadwal',
 *     'action' => current_url(),
 *     'filters' => [
 *         [
 *             'type' => 'select',
 *             'name' => 'program_studi',
 *             'label' => 'Program Studi',
 *             'icon' => 'bi-mortarboard-fill',
 *             'col' => 'col-md-5',
 *             'options' => [
 *                 '' => 'Semua Program Studi',
 *                 'Teknik Informatika' => 'Teknik Informatika',
 *                 'Sistem Informasi' => 'Sistem Informasi',
 *             ],
 *             'selected' => $filters['program_studi'] ?? ''
 *         ],
 *         [
 *             'type' => 'text',
 *             'name' => 'tahun_akademik',
 *             'label' => 'Tahun Akademik',
 *             'icon' => 'bi-calendar-event',
 *             'col' => 'col-md-5',
 *             'placeholder' => 'e.g. 2025/2026 Ganjil',
 *             'value' => $filters['tahun_akademik'] ?? ''
 *         ]
 *     ],
 *     'buttonCol' => 'col-md-2',
 *     'buttonText' => 'Terapkan',
 *     'showReset' => true
 * ]) ?>
 */

// Default values
$title = $title ?? 'Filter';
$action = $action ?? current_url();
$filters = $filters ?? [];
$buttonCol = $buttonCol ?? 'col-md-2';
$buttonText = $buttonText ?? 'Terapkan';
$showReset = $showReset ?? true;
$method = $method ?? 'GET';
?>

<div class="modern-filter-wrapper mb-4">
	<div class="modern-filter-header">
		<div class="d-flex align-items-center gap-2">
			<i class="bi bi-funnel-fill text-primary"></i>
			<span class="modern-filter-title"><?= esc($title) ?></span>
		</div>
	</div>
	<div class="modern-filter-body">
		<form method="<?= esc($method) ?>" action="<?= esc($action) ?>">
			<?php if ($method === 'POST'): ?>
				<?= csrf_field() ?>
			<?php endif; ?>

			<div class="row g-3 align-items-end">
				<?php foreach ($filters as $filter): ?>
					<div class="<?= esc($filter['col'] ?? 'col-md-4') ?>">
						<label for="filter_<?= esc($filter['name']) ?>" class="modern-filter-label">
							<?php if (!empty($filter['icon'])): ?>
								<i class="bi <?= esc($filter['icon']) ?> me-1"></i>
							<?php endif; ?>
							<?= esc($filter['label']) ?>
						</label>

						<?php if ($filter['type'] === 'select'): ?>
							<select
								class="form-select modern-filter-input"
								id="filter_<?= esc($filter['name']) ?>"
								name="<?= esc($filter['name']) ?>"
								<?= !empty($filter['required']) ? 'required' : '' ?>
								<?= !empty($filter['disabled']) ? 'disabled' : '' ?>>
								<?php foreach ($filter['options'] as $value => $label): ?>
									<option
										value="<?= esc($value) ?>"
										<?= (isset($filter['selected']) && $filter['selected'] == $value) ? 'selected' : '' ?>>
										<?= esc($label) ?>
									</option>
								<?php endforeach; ?>
							</select>

						<?php elseif ($filter['type'] === 'text'): ?>
							<input
								type="text"
								class="form-control modern-filter-input"
								id="filter_<?= esc($filter['name']) ?>"
								name="<?= esc($filter['name']) ?>"
								value="<?= esc($filter['value'] ?? '') ?>"
								placeholder="<?= esc($filter['placeholder'] ?? '') ?>"
								<?= !empty($filter['required']) ? 'required' : '' ?>
								<?= !empty($filter['disabled']) ? 'disabled' : '' ?>>

						<?php elseif ($filter['type'] === 'number'): ?>
							<input
								type="number"
								class="form-control modern-filter-input"
								id="filter_<?= esc($filter['name']) ?>"
								name="<?= esc($filter['name']) ?>"
								value="<?= esc($filter['value'] ?? '') ?>"
								placeholder="<?= esc($filter['placeholder'] ?? '') ?>"
								min="<?= esc($filter['min'] ?? '') ?>"
								max="<?= esc($filter['max'] ?? '') ?>"
								step="<?= esc($filter['step'] ?? '1') ?>"
								<?= !empty($filter['required']) ? 'required' : '' ?>
								<?= !empty($filter['disabled']) ? 'disabled' : '' ?>>

						<?php elseif ($filter['type'] === 'date'): ?>
							<input
								type="date"
								class="form-control modern-filter-input"
								id="filter_<?= esc($filter['name']) ?>"
								name="<?= esc($filter['name']) ?>"
								value="<?= esc($filter['value'] ?? '') ?>"
								<?= !empty($filter['required']) ? 'required' : '' ?>
								<?= !empty($filter['disabled']) ? 'disabled' : '' ?>>

						<?php elseif ($filter['type'] === 'daterange'): ?>
							<div class="input-group">
								<input
									type="date"
									class="form-control modern-filter-input"
									id="filter_<?= esc($filter['name']) ?>_start"
									name="<?= esc($filter['name']) ?>_start"
									value="<?= esc($filter['value_start'] ?? '') ?>"
									placeholder="Tanggal Mulai"
									<?= !empty($filter['required']) ? 'required' : '' ?>
									<?= !empty($filter['disabled']) ? 'disabled' : '' ?>>
								<span class="input-group-text">-</span>
								<input
									type="date"
									class="form-control modern-filter-input"
									id="filter_<?= esc($filter['name']) ?>_end"
									name="<?= esc($filter['name']) ?>_end"
									value="<?= esc($filter['value_end'] ?? '') ?>"
									placeholder="Tanggal Akhir"
									<?= !empty($filter['required']) ? 'required' : '' ?>
									<?= !empty($filter['disabled']) ? 'disabled' : '' ?>>
							</div>
						<?php endif; ?>
					</div>
				<?php endforeach; ?>

				<div class="<?= esc($buttonCol) ?> d-flex gap-2">
					<button type="submit" class="btn btn-primary modern-filter-btn flex-fill">
						<i class="bi bi-search"></i> <?= esc($buttonText) ?>
					</button>
					<?php if ($showReset): ?>
						<a href="<?= esc($action) ?>"
							class="btn btn-outline-secondary modern-filter-btn-reset"
							data-bs-toggle="tooltip"
							title="Reset Filter">
							<i class="bi bi-arrow-clockwise"></i>
						</a>
					<?php endif; ?>
				</div>
			</div>
		</form>
	</div>
</div>
