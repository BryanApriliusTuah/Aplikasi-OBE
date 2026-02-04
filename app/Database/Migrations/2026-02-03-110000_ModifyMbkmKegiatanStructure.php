<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class ModifyMbkmKegiatanStructure extends Migration
{
    public function up()
    {
        // Step 1: Drop the foreign key constraint
        $this->db->query('ALTER TABLE `mbkm_kegiatan` DROP FOREIGN KEY `fk_mbkm_jenis`');
        $this->db->query('ALTER TABLE `mbkm_kegiatan` DROP KEY `idx_jenis_kegiatan`');

        // Step 2: Drop the jenis_kegiatan_id column
        $this->forge->dropColumn('mbkm_kegiatan', 'jenis_kegiatan_id');

        // Step 3: Add new columns
        $this->forge->addColumn('mbkm_kegiatan', [
            'jenis_kegiatan' => [
                'type'       => 'VARCHAR',
                'constraint' => 100,
                'null'       => false,
                'default'    => 'Magang',
                'after'      => 'id',
                'comment'    => 'Type of MBKM activity (text input)',
            ],
            'nilai_type' => [
                'type'       => 'ENUM',
                'constraint' => ['cpmk', 'cpl'],
                'null'       => true,
                'after'      => 'tahun_akademik',
                'comment'    => 'Type of achievement: CPMK or CPL',
            ],
            'cpmk_id' => [
                'type'       => 'INT',
                'null'       => true,
                'after'      => 'nilai_type',
                'comment'    => 'FK to cpmk table if nilai_type is cpmk',
            ],
            'cpl_id' => [
                'type'       => 'INT',
                'null'       => true,
                'after'      => 'cpmk_id',
                'comment'    => 'FK to cpl table if nilai_type is cpl',
            ],
        ]);

        // Step 4: Add foreign key constraints for cpmk_id and cpl_id
        $this->db->query('ALTER TABLE `mbkm_kegiatan` ADD KEY `idx_kegiatan_cpmk` (`cpmk_id`)');
        $this->db->query('ALTER TABLE `mbkm_kegiatan` ADD KEY `idx_kegiatan_cpl` (`cpl_id`)');
        $this->db->query('ALTER TABLE `mbkm_kegiatan` ADD CONSTRAINT `fk_kegiatan_cpmk` FOREIGN KEY (`cpmk_id`) REFERENCES `cpmk` (`id`) ON DELETE SET NULL');
        $this->db->query('ALTER TABLE `mbkm_kegiatan` ADD CONSTRAINT `fk_kegiatan_cpl` FOREIGN KEY (`cpl_id`) REFERENCES `cpl` (`id`) ON DELETE SET NULL');

        // Step 5: Update the view to use new structure
        $this->db->query('DROP VIEW IF EXISTS `view_mbkm_lengkap`');
        $this->db->query("
            CREATE VIEW `view_mbkm_lengkap` AS
            SELECT
                k.id AS id,
                k.judul_kegiatan AS judul_kegiatan,
                GROUP_CONCAT(DISTINCT m.nim ORDER BY km.id ASC SEPARATOR ', ') AS nim_list,
                GROUP_CONCAT(DISTINCT m.nama_lengkap ORDER BY km.id ASC SEPARATOR ', ') AS nama_mahasiswa_list,
                GROUP_CONCAT(DISTINCT CONCAT(m.nama_lengkap, ' (', m.nim, ')') ORDER BY km.id ASC SEPARATOR ', ') AS mahasiswa_detail,
                COUNT(DISTINCT km.mahasiswa_id) AS jumlah_mahasiswa,
                (SELECT mahasiswa.program_studi_kode FROM mahasiswa WHERE mahasiswa.id = (SELECT mbkm_kegiatan_mahasiswa.mahasiswa_id FROM mbkm_kegiatan_mahasiswa WHERE mbkm_kegiatan_mahasiswa.kegiatan_id = k.id LIMIT 1)) AS program_studi_kode,
                k.jenis_kegiatan AS jenis_kegiatan,
                k.tempat_kegiatan AS tempat_kegiatan,
                k.tanggal_mulai AS tanggal_mulai,
                k.tanggal_selesai AS tanggal_selesai,
                k.durasi_minggu AS durasi_minggu,
                k.sks_dikonversi AS sks_dikonversi,
                d.nama_lengkap AS dosen_pembimbing,
                k.status_kegiatan AS status_kegiatan,
                k.tahun_akademik AS tahun_akademik,
                k.nilai_type AS nilai_type,
                k.cpmk_id AS cpmk_id,
                k.cpl_id AS cpl_id,
                cpmk.kode_cpmk AS kode_cpmk,
                cpmk.deskripsi AS cpmk_deskripsi,
                cpl.kode_cpl AS kode_cpl,
                cpl.deskripsi AS cpl_deskripsi,
                na.nilai_angka AS nilai_angka,
                na.nilai_huruf AS nilai_huruf,
                na.status_kelulusan AS status_kelulusan
            FROM mbkm_kegiatan k
            LEFT JOIN mbkm_kegiatan_mahasiswa km ON k.id = km.kegiatan_id
            LEFT JOIN mahasiswa m ON km.mahasiswa_id = m.id
            LEFT JOIN dosen d ON k.dosen_pembimbing_id = d.id
            LEFT JOIN mbkm_nilai_akhir na ON k.id = na.kegiatan_id
            LEFT JOIN cpmk ON k.cpmk_id = cpmk.id
            LEFT JOIN cpl ON k.cpl_id = cpl.id
            GROUP BY k.id, k.judul_kegiatan, k.jenis_kegiatan, k.tempat_kegiatan, k.tanggal_mulai, k.tanggal_selesai, k.durasi_minggu, k.sks_dikonversi, d.nama_lengkap, k.status_kegiatan, k.tahun_akademik, k.nilai_type, k.cpmk_id, k.cpl_id, cpmk.kode_cpmk, cpmk.deskripsi, cpl.kode_cpl, cpl.deskripsi, na.nilai_angka, na.nilai_huruf, na.status_kelulusan
        ");
    }

    public function down()
    {
        // Step 1: Remove foreign key constraints for cpmk_id and cpl_id
        $this->db->query('ALTER TABLE `mbkm_kegiatan` DROP FOREIGN KEY `fk_kegiatan_cpmk`');
        $this->db->query('ALTER TABLE `mbkm_kegiatan` DROP FOREIGN KEY `fk_kegiatan_cpl`');
        $this->db->query('ALTER TABLE `mbkm_kegiatan` DROP KEY `idx_kegiatan_cpmk`');
        $this->db->query('ALTER TABLE `mbkm_kegiatan` DROP KEY `idx_kegiatan_cpl`');

        // Step 2: Remove the new columns
        $this->forge->dropColumn('mbkm_kegiatan', ['jenis_kegiatan', 'nilai_type', 'cpmk_id', 'cpl_id']);

        // Step 3: Add back jenis_kegiatan_id column
        $this->forge->addColumn('mbkm_kegiatan', [
            'jenis_kegiatan_id' => [
                'type'       => 'INT',
                'null'       => false,
                'after'      => 'id',
            ],
        ]);

        // Step 4: Add back foreign key constraint
        $this->db->query('ALTER TABLE `mbkm_kegiatan` ADD KEY `idx_jenis_kegiatan` (`jenis_kegiatan_id`)');
        $this->db->query('ALTER TABLE `mbkm_kegiatan` ADD CONSTRAINT `fk_mbkm_jenis` FOREIGN KEY (`jenis_kegiatan_id`) REFERENCES `mbkm_jenis_kegiatan` (`id`) ON DELETE CASCADE');

        // Step 5: Restore old view structure
        $this->db->query('DROP VIEW IF EXISTS `view_mbkm_lengkap`');
        $this->db->query("
            CREATE VIEW `view_mbkm_lengkap` AS
            SELECT
                k.id AS id,
                k.judul_kegiatan AS judul_kegiatan,
                GROUP_CONCAT(DISTINCT m.nim ORDER BY km.id ASC SEPARATOR ', ') AS nim_list,
                GROUP_CONCAT(DISTINCT m.nama_lengkap ORDER BY km.id ASC SEPARATOR ', ') AS nama_mahasiswa_list,
                GROUP_CONCAT(DISTINCT CONCAT(m.nama_lengkap, ' (', m.nim, ')') ORDER BY km.id ASC SEPARATOR ', ') AS mahasiswa_detail,
                COUNT(DISTINCT km.mahasiswa_id) AS jumlah_mahasiswa,
                (SELECT mahasiswa.program_studi_kode FROM mahasiswa WHERE mahasiswa.id = (SELECT mbkm_kegiatan_mahasiswa.mahasiswa_id FROM mbkm_kegiatan_mahasiswa WHERE mbkm_kegiatan_mahasiswa.kegiatan_id = k.id LIMIT 1)) AS program_studi_kode,
                jk.nama_kegiatan AS jenis_kegiatan,
                k.tempat_kegiatan AS tempat_kegiatan,
                k.tanggal_mulai AS tanggal_mulai,
                k.tanggal_selesai AS tanggal_selesai,
                k.durasi_minggu AS durasi_minggu,
                k.sks_dikonversi AS sks_dikonversi,
                d.nama_lengkap AS dosen_pembimbing,
                k.status_kegiatan AS status_kegiatan,
                k.tahun_akademik AS tahun_akademik,
                na.nilai_angka AS nilai_angka,
                na.nilai_huruf AS nilai_huruf,
                na.status_kelulusan AS status_kelulusan,
                na.nilai_type AS nilai_type,
                cpmk.kode_cpmk AS kode_cpmk,
                cpl.kode_cpl AS kode_cpl
            FROM mbkm_kegiatan k
            JOIN mbkm_jenis_kegiatan jk ON k.jenis_kegiatan_id = jk.id
            LEFT JOIN mbkm_kegiatan_mahasiswa km ON k.id = km.kegiatan_id
            LEFT JOIN mahasiswa m ON km.mahasiswa_id = m.id
            LEFT JOIN dosen d ON k.dosen_pembimbing_id = d.id
            LEFT JOIN mbkm_nilai_akhir na ON k.id = na.kegiatan_id
            LEFT JOIN cpmk ON na.cpmk_id = cpmk.id
            LEFT JOIN cpl ON na.cpl_id = cpl.id
            GROUP BY k.id, k.judul_kegiatan, jk.nama_kegiatan, k.tempat_kegiatan, k.tanggal_mulai, k.tanggal_selesai, k.durasi_minggu, k.sks_dikonversi, d.nama_lengkap, k.status_kegiatan, k.tahun_akademik, na.nilai_angka, na.nilai_huruf, na.status_kelulusan, na.nilai_type, cpmk.kode_cpmk, cpl.kode_cpl
        ");
    }
}
