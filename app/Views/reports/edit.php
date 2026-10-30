<?php

declare(strict_types=1);

/*
|--------------------------------------------------------------------------
| LAPORAN - EDIT METADATA (PRELINE & ALPINE NATIVE)
|--------------------------------------------------------------------------
*/

$batch = is_array($batch ?? null) ? $batch : [];
$teams = is_array($teams ?? null) ? $teams : [];
$allTeams = $teams;
$currentTeam = trim((string) ($batch['team_name'] ?? $batch['client_name'] ?? ''));
if ($currentTeam !== '' && !in_array($currentTeam, $allTeams, true)) {
    $allTeams[] = $currentTeam;
    sort($allTeams);
}
$currentDate = (string) ($batch['report_date'] ?? '');
$currentName = (string) ($batch['name'] ?? '');
?>

<div class="max-w-7xl mx-auto space-y-6">

    <!-- =========================================================
         PAGE ACTIONS
         ========================================================= -->
    <div class="flex items-center justify-end">
        <a class="btn-secondary" href="<?= url('/reports/' . $batch['id']) ?>">
            <i class="bi bi-arrow-left"></i>
            <span>Kembali</span>
        </a>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- FORM CARD -->
        <section class="lg:col-span-2 bg-white border border-gray-200 rounded-2xl p-6 shadow-xs">
            <form
                method="post"
                action="<?= url('/reports/' . $batch['id']) ?>"
                x-data="reportEditForm({
                    initialDate: '<?= e(addslashes($currentDate)) ?>',
                    initialName: '<?= e(addslashes($currentName)) ?>',
                    initialTeam: '<?= e(addslashes($currentTeam)) ?>'
                })"
                @submit="if (!teamName) { $event.preventDefault(); window.remitToast ? window.remitToast.warning('Silakan pilih nama tim terlebih dahulu.') : alert('Silakan pilih nama tim.'); }"
            >
                <?= csrf_field() ?>
                <?= method_field('PUT') ?>
                <input type="hidden" name="month_key" :value="reportDate ? reportDate.substring(0, 7) : '<?= e($batch['month_key'] ?? '') ?>'">

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mb-6">
                    <div class="sm:col-span-2">
                        <label class="block text-xs font-semibold text-gray-700 mb-1.5">Nama report *</label>
                        <input
                            class="custom-form-input"
                            name="name"
                            required
                            placeholder="Contoh: LAPORAN HARIAN - 5 AGUSTUS 2026"
                            x-model="reportName"
                        >
                    </div>

                    <div>
                        <div class="flex items-center gap-1.5 mb-1.5">
                            <label class="block text-xs font-semibold text-gray-700">Tanggal report *</label>
                            <?= render_tooltip(
                                'Mengubah tanggal report otomatis memperbarui nama report.',
                                '<span class="info-toggle-btn cursor-help" aria-label="Info tanggal report"><i class="bi bi-info-circle text-[10px] leading-none"></i></span>',
                                ['placement' => 'top']
                            ) ?>
                        </div>
                        <div @change="if ($event.detail && $event.detail.value) onDateSelected($event.detail.value)">
                            <?= render_datepicker('report_date', (string) ($batch['report_date'] ?? ''), ['required' => true, 'placeholder' => 'Pilih tanggal']) ?>
                        </div>
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-gray-700 mb-1.5">Nama tim *</label>
                        <div class="custom-select-root relative" @click.outside="teamOpen = false">
                            <input type="hidden" name="team_name" :value="teamName">
                            <button
                                type="button"
                                class="dropdown-toggle"
                                @click="teamOpen = !teamOpen"
                                :aria-expanded="teamOpen.toString()"
                                aria-label="Pilih Nama Tim"
                            >
                                <div class="flex items-center gap-2 truncate">
                                    <i class="bi bi-people text-gray-400"></i>
                                    <span class="truncate" :class="teamName ? 'text-gray-900 font-medium' : 'text-gray-400'" x-text="teamLabel"></span>
                                </div>
                                <i class="bi bi-chevron-down text-xs text-gray-400 transition-transform duration-200 shrink-0 ms-1.5" :class="{ 'rotate-180': teamOpen }"></i>
                            </button>

                            <div
                                class="dropdown-menu absolute left-0 right-0 top-full mt-1.5 max-h-60 overflow-y-auto z-50 bg-white"
                                x-show="teamOpen"
                                x-cloak
                                x-transition:enter="transition ease-out duration-100"
                                x-transition:enter-start="opacity-0"
                                x-transition:enter-end="opacity-100"
                                x-transition:leave="transition ease-in duration-75"
                                x-transition:leave-start="opacity-100"
                                x-transition:leave-end="opacity-0"
                                style="display: none;"
                                role="listbox"
                            >
                                <button
                                    type="button"
                                    class="dropdown-item justify-between"
                                    :class="{ 'active': teamName === '' }"
                                    @click="selectTeam('')"
                                >
                                    <span class="text-gray-400 italic">-- Pilih Tim --</span>
                                    <i x-show="teamName === ''" class="bi bi-check2 text-blue-600 text-sm"></i>
                                </button>
                                <?php foreach ($allTeams as $team): ?>
                                    <?php $tVal = trim((string) $team); if ($tVal === '') continue; ?>
                                    <button
                                        type="button"
                                        class="dropdown-item justify-between"
                                        :class="{ 'active': teamName === '<?= e(addslashes($tVal)) ?>' }"
                                        @click="selectTeam('<?= e(addslashes($tVal)) ?>')"
                                    >
                                        <span class="font-medium"><?= e($tVal) ?></span>
                                        <i x-show="teamName === '<?= e(addslashes($tVal)) ?>'" class="bi bi-check2 text-blue-600 text-sm"></i>
                                    </button>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    </div>

                    <div class="sm:col-span-2">
                        <label class="block text-xs font-semibold text-gray-700 mb-1.5">Nama klien</label>
                        <input
                            class="custom-form-input"
                            name="client_name"
                            value="<?= e($batch['client_name'] ?? '') ?>"
                            x-model="clientName"
                            placeholder="Opsional (misal: PARK)"
                        >
                    </div>

                    <div class="sm:col-span-2">
                        <div class="flex items-center gap-1.5 mb-1.5">
                            <label class="block text-xs font-semibold text-gray-700">File sumber</label>
                            <?= render_tooltip(
                                'Untuk mengganti berkas Excel transaksi, hapus report lama dan upload file baru dengan tanggal yang sama.',
                                '<span class="info-toggle-btn cursor-help" aria-label="Info pergantian berkas"><i class="bi bi-info-circle text-[10px] leading-none"></i></span>',
                                ['placement' => 'top']
                            ) ?>
                        </div>
                        <input class="custom-form-input opacity-60 cursor-not-allowed" value="<?= e($batch['source_filename'] ?? '') ?>" disabled>
                    </div>
                </div>

                <div class="flex items-center justify-end gap-3 pt-4 border-t border-gray-100">
                    <a class="btn-secondary" href="<?= url('/reports/' . $batch['id']) ?>">
                        Batal
                    </a>
                    <button class="btn-primary" type="submit">
                        <i class="bi bi-floppy"></i>
                        <span>Simpan Perubahan</span>
                    </button>
                </div>
            </form>
        </section>

        <!-- ASIDE INFO CARD -->
        <aside class="bg-white border border-gray-200 rounded-2xl p-6 shadow-xs flex flex-col justify-between">
            <div>
                <h3 class="text-base font-semibold text-gray-900 mb-4 pb-2 border-b border-gray-100 flex items-center gap-2">
                    <i class="bi bi-info-circle text-gray-800"></i>
                    Informasi Report
                </h3>
                <dl class="divide-y divide-gray-100 text-xs">
                    <div class="py-2.5 flex justify-between items-center"><dt class="text-gray-500 font-medium">Batch ID</dt><dd class="font-bold text-gray-900 font-mono">#<?= (int) ($batch['id'] ?? 0) ?></dd></div>
                    <div class="py-2.5 flex justify-between items-center"><dt class="text-gray-500 font-medium">Total Baris</dt><dd class="font-bold text-gray-900 font-mono"><?= number_format((int) ($batch['total_rows'] ?? 0), 0, ',', '.') ?></dd></div>
                    <div class="py-2.5 flex justify-between items-center"><dt class="text-gray-500 font-medium">Terkirim</dt><dd class="font-bold text-emerald-600 font-mono"><?= number_format((int) ($batch['sent_rows'] ?? 0), 0, ',', '.') ?></dd></div>
                    <div class="py-2.5 flex justify-between items-center"><dt class="text-gray-500 font-medium">Pending</dt><dd class="font-bold text-amber-600 font-mono"><?= number_format((int) ($batch['pending_rows'] ?? 0), 0, ',', '.') ?></dd></div>
                    <div class="py-2.5 flex justify-between items-center"><dt class="text-gray-500 font-medium">Dibuat</dt><dd class="text-gray-700"><?= e(format_datetime($batch['created_at'] ?? null)) ?></dd></div>
                    <div class="py-2.5 flex justify-between items-center"><dt class="text-gray-500 font-medium">Diperbarui</dt><dd class="text-gray-700"><?= e(format_datetime($batch['updated_at'] ?? null)) ?></dd></div>
                </dl>
            </div>
        </aside>
    </div>

    <!-- FLOATING TOAST NOTIFICATION (GEMINI.MD SECTION 16 - ZERO LAYOUT SHIFT) -->
    <script>
    document.addEventListener('DOMContentLoaded', () => {
        if (window.remitToast && typeof window.remitToast.warning === 'function') {
            window.remitToast.warning('Untuk mengganti berkas Excel transaksi, hapus report lama dan upload file baru dengan tanggal yang sama.', {
                title: 'Pergantian Berkas',
                duration: 6000
            });
        }
    });

    function reportEditForm(config) {
        return {
            reportDate: config.initialDate || '',
            reportName: config.initialName || '',
            teamName: config.initialTeam || '',
            teamLabel: config.initialTeam || 'Pilih Tim',
            teamOpen: false,
            clientName: '<?= e(addslashes($batch['client_name'] ?? '')) ?>',

            formatTitle(dateStr) {
                if (!dateStr || typeof dateStr !== 'string') return '';
                const parts = dateStr.trim().split('-');
                if (parts.length !== 3) return '';
                const months = [
                    'JANUARI', 'FEBRUARI', 'MARET', 'APRIL', 'MEI', 'JUNI',
                    'JULI', 'AGUSTUS', 'SEPTEMBER', 'OKTOBER', 'NOVEMBER', 'DESEMBER'
                ];
                const y = parts[0];
                const m = parseInt(parts[1], 10) - 1;
                const d = parseInt(parts[2], 10);
                if (isNaN(d) || isNaN(m) || !months[m]) return '';
                return `LAPORAN HARIAN - ${d} ${months[m]} ${y}`;
            },

            onDateSelected(newDate) {
                this.reportDate = newDate;
                if (newDate) {
                    this.reportName = this.formatTitle(newDate);
                }
            },

            selectTeam(team) {
                this.teamName = team;
                this.teamLabel = team || 'Pilih Tim';
                this.teamOpen = false;
                if (!this.clientName) {
                    this.clientName = team;
                }
            }
        };
    }
    </script>
</div>
