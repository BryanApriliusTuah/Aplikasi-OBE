<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddCplCpmkToMbkmNilaiAkhir extends Migration
{
    public function up()
    {
        // Add new columns to mbkm_nilai_akhir table
        $this->forge->addColumn('mbkm_nilai_akhir', [
            'nilai_type' => [
                'type'       => 'ENUM',
                'constraint' => ['cpmk', 'cpl'],
                'null'       => true,
                'after'      => 'status_kelulusan',
                'comment'    => 'Type of achievement this score applies to',
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

        // Add foreign key constraints
        $this->db->query('ALTER TABLE `mbkm_nilai_akhir` ADD KEY `idx_cpmk` (`cpmk_id`)');
        $this->db->query('ALTER TABLE `mbkm_nilai_akhir` ADD KEY `idx_cpl` (`cpl_id`)');
        $this->db->query('ALTER TABLE `mbkm_nilai_akhir` ADD CONSTRAINT `fk_nilai_akhir_cpmk` FOREIGN KEY (`cpmk_id`) REFERENCES `cpmk` (`id`) ON DELETE SET NULL');
        $this->db->query('ALTER TABLE `mbkm_nilai_akhir` ADD CONSTRAINT `fk_nilai_akhir_cpl` FOREIGN KEY (`cpl_id`) REFERENCES `cpl` (`id`) ON DELETE SET NULL');

        // Update the view to include CPL/CPMK info
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
                (SELECT mahasiswa.program_studi_kode FROM mahasiswa WHERE mahasiswa.id = (SELECT mbkm_kegiatan_mahasiswa.mahasiswa_id FROM mbkm_kegiatan_mahasiswa WHERE mbkm_kegiatan_mahasiswa.kegiatan_id = k.id LIMIT 1)) AS program_studi,
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

    public function down()
    {
        // Remove foreign key constraints first
        $this->db->query('ALTER TABLE `mbkm_nilai_akhir` DROP FOREIGN KEY `fk_nilai_akhir_cpmk`');
        $this->db->query('ALTER TABLE `mbkm_nilai_akhir` DROP FOREIGN KEY `fk_nilai_akhir_cpl`');
        $this->db->query('ALTER TABLE `mbkm_nilai_akhir` DROP KEY `idx_cpmk`');
        $this->db->query('ALTER TABLE `mbkm_nilai_akhir` DROP KEY `idx_cpl`');

        // Remove the columns
        $this->forge->dropColumn('mbkm_nilai_akhir', ['nilai_type', 'cpmk_id', 'cpl_id']);

        // Restore the original view without CPL/CPMK info
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
                na.status_kelulusan AS status_kelulusan
            FROM mbkm_kegiatan k
            JOIN mbkm_jenis_kegiatan jk ON k.jenis_kegiatan_id = jk.id
            LEFT JOIN mbkm_kegiatan_mahasiswa km ON k.id = km.kegiatan_id
            LEFT JOIN mahasiswa m ON km.mahasiswa_id = m.id
            LEFT JOIN dosen d ON k.dosen_pembimbing_id = d.id
            LEFT JOIN mbkm_nilai_akhir na ON k.id = na.kegiatan_id
            GROUP BY k.id, k.judul_kegiatan, jk.nama_kegiatan, k.tempat_kegiatan, k.tanggal_mulai, k.tanggal_selesai, k.durasi_minggu, k.sks_dikonversi, d.nama_lengkap, k.status_kegiatan, k.tahun_akademik, na.nilai_angka, na.nilai_huruf, na.status_kelulusan
        ");
    }
}
