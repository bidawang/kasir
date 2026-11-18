-- phpMyAdmin SQL Dump
-- version 5.2.3
-- https://www.phpmyadmin.net/
--
-- Host: localhost
-- Generation Time: Nov 17, 2026 at 07:54 AM
-- Server version: 8.0.30
-- PHP Version: 8.5.11

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `brilink`
--

-- --------------------------------------------------------

--
-- Table structure for table `banks`
--

CREATE TABLE `banks` (
  `id` bigint UNSIGNED NOT NULL,
  `kode` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL,
  `nama` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `tipe` enum('bank','ewallet') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'bank',
  `icon` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `banks`
--

INSERT INTO `banks` (`id`, `kode`, `nama`, `tipe`, `icon`, `is_active`, `created_at`, `updated_at`) VALUES
(1, 'BRI', 'Bank Rakyat Indonesia', 'bank', NULL, 1, '2026-11-14 00:42:01', '2026-11-14 00:42:01'),
(2, 'BCA', 'Bank Central Asia', 'bank', NULL, 1, '2026-11-14 00:42:01', '2026-11-14 00:42:01'),
(3, 'MANDIRI', 'Bank Mandiri', 'bank', NULL, 1, '2026-11-14 00:42:01', '2026-11-14 00:42:01'),
(4, 'BNI', 'Bank Negara Indonesia', 'bank', NULL, 1, '2026-11-14 00:42:01', '2026-11-14 00:42:01'),
(5, 'BSI', 'Bank Syariah Indonesia', 'bank', NULL, 1, '2026-11-14 00:42:01', '2026-11-14 00:42:01'),
(6, 'CIMB', 'Bank CIMB Niaga', 'bank', NULL, 1, '2026-11-14 00:42:01', '2026-11-14 00:42:01'),
(7, 'DANAMON', 'Bank Danamon', 'bank', NULL, 1, '2026-11-14 00:42:01', '2026-11-14 00:42:01'),
(8, 'PERMATA', 'Bank Permata', 'bank', NULL, 1, '2026-11-14 00:42:01', '2026-11-14 00:42:01'),
(9, 'BTN', 'Bank Tabungan Negara', 'bank', NULL, 1, '2026-11-14 00:42:01', '2026-11-14 00:42:01'),
(10, 'BTPN', 'Bank BTPN / Jenius', 'bank', NULL, 1, '2026-11-14 00:42:01', '2026-11-14 00:42:01'),
(11, 'JAGO', 'Bank Jago', 'bank', NULL, 1, '2026-11-14 00:42:01', '2026-11-14 00:42:01'),
(12, 'SEABANK', 'SeaBank Indonesia', 'bank', NULL, 1, '2026-11-14 00:42:01', '2026-11-14 00:42:01'),
(13, 'NEO', 'Bank Neo Commerce', 'bank', NULL, 1, '2026-11-14 00:42:01', '2026-11-14 00:42:01'),
(14, 'DANA', 'DANA Indonesia', 'ewallet', NULL, 1, '2026-11-14 00:42:01', '2026-11-14 00:42:01'),
(15, 'GOPAY', 'GoPay', 'ewallet', NULL, 1, '2026-11-14 00:42:01', '2026-11-14 00:42:01'),
(16, 'OVO', 'OVO', 'ewallet', NULL, 1, '2026-11-14 00:42:01', '2026-11-14 00:42:01'),
(17, 'SHOPEEPAY', 'ShopeePay', 'ewallet', NULL, 1, '2026-11-14 00:42:01', '2026-11-14 00:42:01'),
(18, 'LINKAJA', 'LinkAja', 'ewallet', NULL, 1, '2026-11-14 00:42:01', '2026-11-14 00:42:01');

-- --------------------------------------------------------

--
-- Table structure for table `biaya_admin`
--

CREATE TABLE `biaya_admin` (
  `id` bigint UNSIGNED NOT NULL,
  `id_warung` bigint UNSIGNED NOT NULL,
  `id_kategori` bigint UNSIGNED DEFAULT NULL,
  `id_kas` bigint UNSIGNED DEFAULT NULL,
  `jenis` varchar(30) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `range_awal` bigint UNSIGNED NOT NULL,
  `range_akhir` bigint UNSIGNED NOT NULL,
  `biaya_admin` bigint UNSIGNED NOT NULL,
  `biaya_admin_bank` bigint UNSIGNED NOT NULL DEFAULT '0',
  `keterangan` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `biaya_admin`
--

INSERT INTO `biaya_admin` (`id`, `id_warung`, `id_kategori`, `id_kas`, `jenis`, `range_awal`, `range_akhir`, `biaya_admin`, `biaya_admin_bank`, `keterangan`, `created_at`, `updated_at`) VALUES
(4, 1, 1, NULL, NULL, 0, 1000000, 5000, 2500, 'Test Multi Tier Auto Chain', '2026-11-16 20:01:08', '2026-11-16 20:01:08'),
(5, 1, 1, NULL, NULL, 1000001, 2000000, 7000, 2500, 'Test Multi Tier Auto Chain', '2026-11-16 20:01:08', '2026-11-16 20:01:08'),
(6, 1, 1, NULL, NULL, 2000001, 3000000, 10000, 2500, 'Test Multi Tier Auto Chain', '2026-11-16 20:01:08', '2026-11-16 20:01:08');

-- --------------------------------------------------------

--
-- Table structure for table `detail_kas`
--

CREATE TABLE `detail_kas` (
  `id` bigint UNSIGNED NOT NULL,
  `id_kas_warung` bigint UNSIGNED NOT NULL,
  `pecahan` int UNSIGNED NOT NULL,
  `jumlah` int NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `detail_kas`
--

INSERT INTO `detail_kas` (`id`, `id_kas_warung`, `pecahan`, `jumlah`, `created_at`, `updated_at`) VALUES
(1, 1, 100, 0, '2026-11-14 00:42:39', '2026-11-14 00:42:39'),
(2, 1, 200, 0, '2026-11-14 00:42:39', '2026-11-14 00:42:39'),
(3, 1, 500, 0, '2026-11-14 00:42:39', '2026-11-14 00:42:39'),
(4, 1, 1000, 0, '2026-11-14 00:42:39', '2026-11-14 00:42:39'),
(5, 1, 2000, 0, '2026-11-14 00:42:39', '2026-11-14 00:42:39'),
(6, 1, 5000, 0, '2026-11-14 00:42:39', '2026-11-14 00:42:39'),
(7, 1, 10000, 1, '2026-11-14 00:42:39', '2026-11-14 00:46:14'),
(8, 1, 20000, 0, '2026-11-14 00:42:39', '2026-11-14 00:42:39'),
(9, 1, 50000, 0, '2026-11-14 00:42:39', '2026-11-14 00:42:39'),
(10, 1, 100000, 10, '2026-11-14 00:42:39', '2026-11-16 22:21:10'),
(21, 17, 100, 0, '2026-11-16 22:55:15', '2026-11-16 22:55:15'),
(22, 17, 200, 0, '2026-11-16 22:55:15', '2026-11-16 22:55:15'),
(23, 17, 500, 0, '2026-11-16 22:55:15', '2026-11-16 22:55:15'),
(24, 17, 1000, 0, '2026-11-16 22:55:15', '2026-11-16 22:55:15'),
(25, 17, 2000, 0, '2026-11-16 22:55:15', '2026-11-16 22:55:15'),
(26, 17, 5000, 0, '2026-11-16 22:55:15', '2026-11-16 22:55:15'),
(27, 17, 10000, 0, '2026-11-16 22:55:15', '2026-11-16 22:55:15'),
(28, 17, 20000, 0, '2026-11-16 22:55:15', '2026-11-16 22:55:15'),
(29, 17, 50000, 0, '2026-11-16 22:55:15', '2026-11-16 22:55:15'),
(30, 17, 100000, 0, '2026-11-16 22:55:15', '2026-11-16 22:55:15');

-- --------------------------------------------------------

--
-- Table structure for table `detail_transaksi`
--

CREATE TABLE `detail_transaksi` (
  `id` bigint UNSIGNED NOT NULL,
  `id_transaksi_kas` bigint UNSIGNED NOT NULL,
  `pecahan` int UNSIGNED NOT NULL,
  `jumlah` int NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `item_jualan`
--

CREATE TABLE `item_jualan` (
  `id` bigint UNSIGNED NOT NULL,
  `id_warung` bigint UNSIGNED NOT NULL,
  `nama_item` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `jenis` varchar(30) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'fisik',
  `harga_jual` bigint UNSIGNED NOT NULL DEFAULT '0',
  `harga_beli` bigint UNSIGNED NOT NULL DEFAULT '0',
  `jumlah` int NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `item_jualan`
--

INSERT INTO `item_jualan` (`id`, `id_warung`, `nama_item`, `jenis`, `harga_jual`, `harga_beli`, `jumlah`, `created_at`, `updated_at`) VALUES
(1, 1, 'Parfum', 'fisik', 10000, 0, 9, '2026-11-14 00:45:12', '2026-11-14 00:46:14'),
(3, 1, 'Barang Uji Coba Laba', 'fisik', 20000, 15000, 10, '2026-11-16 20:01:08', '2026-11-16 20:01:08');

-- --------------------------------------------------------

--
-- Table structure for table `kas`
--

CREATE TABLE `kas` (
  `id` bigint UNSIGNED NOT NULL,
  `id_warung` bigint UNSIGNED NOT NULL,
  `jenis_kas` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'Kas Utama',
  `status` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'cash',
  `nomor_rekening` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `atas_nama` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `saldo` bigint NOT NULL DEFAULT '0',
  `is_primary` tinyint(1) NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `kas`
--

INSERT INTO `kas` (`id`, `id_warung`, `jenis_kas`, `status`, `nomor_rekening`, `atas_nama`, `saldo`, `is_primary`, `created_at`, `updated_at`) VALUES
(1, 1, 'Kas Tunai Transaksi', 'cash', NULL, NULL, 1010000, 1, '2026-11-14 00:42:39', '2026-11-16 22:21:10'),
(2, 1, 'Rekening BRI Utama', 'bank', '012345678901234', 'Stipen', 4769500, 1, '2026-11-14 00:42:39', '2026-11-16 22:07:37'),
(3, 1, 'EDC', 'bank', NULL, NULL, 60941000, 1, '2026-11-16 18:00:48', '2026-11-16 18:00:48'),
(4, 1, 'ATM UMI', 'bank', NULL, NULL, 827000, 0, '2026-11-16 18:00:48', '2026-11-16 18:00:48'),
(5, 1, 'BNI', 'bank', NULL, NULL, 541000, 0, '2026-11-16 18:00:48', '2026-11-16 18:00:48'),
(6, 1, 'Mandiri', 'bank', NULL, NULL, 0, 0, '2026-11-16 18:00:48', '2026-11-16 18:00:48'),
(7, 1, 'Sea Bank', 'bank', NULL, NULL, 253000, 0, '2026-11-16 18:00:48', '2026-11-16 18:00:48'),
(8, 1, 'Dana', 'ewallet', NULL, NULL, 2107000, 0, '2026-11-16 18:00:48', '2026-11-16 18:00:48'),
(9, 1, 'Gopay', 'ewallet', NULL, NULL, 74000, 0, '2026-11-16 18:00:48', '2026-11-16 18:00:48'),
(10, 1, 'Super Bank', 'bank', NULL, NULL, 2478000, 0, '2026-11-16 18:00:48', '2026-11-16 18:00:48'),
(11, 1, 'Dana Payz', 'ewallet', NULL, NULL, 208000, 0, '2026-11-16 18:00:48', '2026-11-16 18:00:48'),
(12, 1, 'Voucher', 'ewallet', NULL, NULL, 27000, 0, '2026-11-16 18:00:48', '2026-11-16 18:00:48'),
(13, 2, 'Kas Tunai', 'cash', NULL, NULL, 5000000, 1, '2026-11-16 20:53:17', '2026-11-16 20:53:17'),
(14, 2, 'Rekening MANDIRI Utama', 'bank', NULL, NULL, 20250000, 1, '2026-11-16 20:53:17', '2026-11-16 22:07:37'),
(17, 4, 'Kas Tunai', 'cash', NULL, NULL, 0, 1, '2026-11-16 22:55:15', '2026-11-16 22:55:15'),
(18, 4, 'Rekening BANK SYARIAH INDONESIA Utama', 'bank', '1234567865431', 'Rke', 0, 1, '2026-11-16 22:55:15', '2026-11-16 22:55:15');

-- --------------------------------------------------------

--
-- Table structure for table `kasir`
--

CREATE TABLE `kasir` (
  `id` bigint UNSIGNED NOT NULL,
  `id_user` bigint UNSIGNED NOT NULL,
  `nama_warung` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `alamat` text COLLATE utf8mb4_unicode_ci,
  `no_telepon` varchar(30) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `modal` bigint UNSIGNED NOT NULL DEFAULT '0',
  `bank_utama` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'BRI',
  `nomor_rekening_utama` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `atas_nama_utama` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `kasir`
--

INSERT INTO `kasir` (`id`, `id_user`, `nama_warung`, `alamat`, `no_telepon`, `modal`, `bank_utama`, `nomor_rekening_utama`, `atas_nama_utama`, `created_at`, `updated_at`) VALUES
(1, 1, 'Uji Coba', '-', '-', 0, 'BRI', '012345678901234', 'Stipen', '2026-11-14 00:42:39', '2026-11-14 00:42:39'),
(2, 1, 'Cabang Berkah Jaya', NULL, NULL, 0, 'MANDIRI', NULL, NULL, '2026-11-16 20:53:17', '2026-11-16 20:53:17'),
(4, 6, 'Nangs', '-', '-', 0, 'BANK SYARIAH INDONESIA', '1234567865431', 'Rke', '2026-11-16 22:55:15', '2026-11-16 22:55:15');

-- --------------------------------------------------------

--
-- Table structure for table `kasir_user`
--

CREATE TABLE `kasir_user` (
  `id_kasir` bigint UNSIGNED NOT NULL,
  `id_user` bigint UNSIGNED NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `kasir_user`
--

INSERT INTO `kasir_user` (`id_kasir`, `id_user`, `created_at`, `updated_at`) VALUES
(1, 2, '2026-11-16 18:00:48', '2026-11-16 18:00:48'),
(4, 7, '2026-11-16 22:56:06', '2026-11-16 22:56:06');

-- --------------------------------------------------------

--
-- Table structure for table `kategori_transaksi`
--

CREATE TABLE `kategori_transaksi` (
  `id` bigint UNSIGNED NOT NULL,
  `kode` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `nama_kategori` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `jenis_transaksi` varchar(30) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'transfer',
  `metode_transaksi` varchar(30) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'umum',
  `jenis_aliran` enum('masuk','keluar') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'masuk',
  `jenis_mutasi_akun` enum('masuk','keluar') COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `deskripsi` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `icon` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `badge_color` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'blue',
  `urutan` int NOT NULL DEFAULT '0',
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `kategori_transaksi`
--

INSERT INTO `kategori_transaksi` (`id`, `kode`, `nama_kategori`, `jenis_transaksi`, `metode_transaksi`, `jenis_aliran`, `jenis_mutasi_akun`, `deskripsi`, `icon`, `badge_color`, `urutan`, `is_active`, `created_at`, `updated_at`) VALUES
(1, 'tf_sesama_kartu', 'Transfer Sesama Bank (Pakai Kartu / EDC)', 'transfer', 'kartu', 'masuk', 'keluar', 'Nasabah setor uang tunai untuk transfer sesama rekening via mesin EDC', 'credit-card', 'blue', 1, 1, '2026-11-14 00:42:01', '2026-11-14 00:42:01'),
(2, 'tf_sesama_app', 'Transfer Sesama Bank (Tanpa Kartu / Aplikasi)', 'transfer', 'tanpa_kartu', 'masuk', 'keluar', 'Nasabah setor tunai untuk transfer sesama rekening via mobile/internet banking', 'device-phone-mobile', 'indigo', 2, 1, '2026-11-14 00:42:01', '2026-11-14 00:42:01'),
(3, 'tf_bank_lain', 'Transfer Antar Bank Lain', 'transfer', 'umum', 'masuk', 'keluar', 'Nasabah setor uang tunai untuk kirim dana ke rekening bank lain', 'arrow-right-circle', 'amber', 3, 1, '2026-11-14 00:42:01', '2026-11-14 00:42:01'),
(4, 'tarik_utama_kartu', 'Tarik Tunai Bank Utama (Pakai Kartu / EDC)', 'tarik_tunai', 'kartu', 'keluar', 'masuk', 'Nasabah gesek kartu di EDC dan kasir menyerahkan uang fisik', 'banknotes', 'rose', 4, 1, '2026-11-14 00:42:01', '2026-11-14 00:42:01'),
(5, 'tarik_utama_app', 'Tarik Tunai Bank Utama (Tanpa Kartu)', 'tarik_tunai', 'tanpa_kartu', 'keluar', 'masuk', 'Tarik tunai tanpa kartu via kode reservasi tarik tunai / transfer ke rekening agen', 'qr-code', 'rose', 5, 1, '2026-11-14 00:42:01', '2026-11-14 00:42:01'),
(6, 'tarik_bank_lain', 'Tarik Tunai Bank Lain (Kartu EDC)', 'tarik_tunai', 'kartu', 'keluar', 'masuk', 'Penarikan tunai dari kartu bank lain melalui jaringan ATM Bersama / Prima di EDC', 'building-library', 'red', 6, 1, '2026-11-14 00:42:01', '2026-11-14 00:42:01'),
(7, 'ewallet_topup', 'Top Up E-Wallet (DANA, Gopay, OVO, ShopeePay)', 'ewallet', 'aplikasi', 'masuk', 'keluar', 'Nasabah bayar uang tunai untuk isi saldo dompet digital', 'wallet', 'emerald', 7, 1, '2026-11-14 00:42:01', '2026-11-14 00:42:01'),
(8, 'ewallet_tarik', 'Tarik Tunai Saldo E-Wallet (QRIS / Transfer)', 'ewallet', 'qris', 'keluar', 'masuk', 'Nasabah scan QRIS agen / transfer saldo dan kasir memberikan uang tunai', 'arrow-down-tray', 'orange', 8, 1, '2026-11-14 00:42:01', '2026-11-14 00:42:01'),
(9, 'briva_va', 'Pembayaran Virtual Account (VA / BRIVA)', 'ppob', 'aplikasi', 'masuk', 'keluar', 'Nasabah bayar tunai untuk tagihan Virtual Account (BRIVA / Mandiri VA / BCA VA)', 'receipt-percent', 'purple', 9, 1, '2026-11-14 00:42:01', '2026-11-14 00:42:01'),
(10, 'ppob_tagihan', 'Listrik PLN, Pulsa & Data, Wifi, Cicilan', 'ppob', 'aplikasi', 'masuk', 'keluar', 'Pembayaran tagihan bulanan dan pembelian pulsa / token listrik', 'bolt', 'cyan', 10, 1, '2026-11-14 00:42:01', '2026-11-14 00:42:01');

-- --------------------------------------------------------

--
-- Table structure for table `laporan_harian`
--

CREATE TABLE `laporan_harian` (
  `id` bigint UNSIGNED NOT NULL,
  `id_warung` bigint UNSIGNED NOT NULL,
  `id_user` bigint UNSIGNED NOT NULL,
  `id_pelapor` bigint UNSIGNED NOT NULL,
  `tanggal` date NOT NULL,
  `rincian_fisik` json DEFAULT NULL,
  `saldo_fisik` bigint UNSIGNED NOT NULL,
  `saldo_sistem` bigint UNSIGNED NOT NULL,
  `selisih` bigint NOT NULL,
  `catatan` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `laporan_harian`
--

INSERT INTO `laporan_harian` (`id`, `id_warung`, `id_user`, `id_pelapor`, `tanggal`, `rincian_fisik`, `saldo_fisik`, `saldo_sistem`, `selisih`, `catatan`, `created_at`, `updated_at`) VALUES
(1, 1, 2, 1, '2026-11-17', '{\"100\": 20, \"200\": 25, \"500\": 0, \"1000\": 5, \"2000\": 32, \"5000\": 74, \"10000\": 30, \"20000\": 10, \"50000\": 180, \"100000\": 93}', 19250000, 19250000, 0, 'Rekap fisik kasir & angsulan harian lancar', '2026-11-16 18:00:48', '2026-11-16 18:00:48');

-- --------------------------------------------------------

--
-- Table structure for table `migrations`
--

CREATE TABLE `migrations` (
  `id` int UNSIGNED NOT NULL,
  `migration` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `batch` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `migrations`
--

INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES
(1, '0001_01_01_000000_create_users_table', 1),
(2, '0001_01_01_000001_create_kasir_table', 1),
(3, '0001_01_01_000002_create_banks_table', 1),
(4, '0001_01_01_000003_create_kas_table', 1),
(5, '0001_01_01_000004_create_kategori_transaksi_table', 1),
(6, '0001_01_01_000005_create_biaya_admin_table', 1),
(7, '0001_01_01_000006_create_item_jualan_table', 1),
(8, '0001_01_01_000007_create_transaksi_table', 1),
(9, '0001_01_01_000008_create_transfer_kas_table', 1),
(10, '0001_01_01_000009_create_laporan_harian_table', 1),
(11, '0001_01_01_000010_create_riwayat_laporan_pdf_table', 1);

-- --------------------------------------------------------

--
-- Table structure for table `riwayat_laporan_pdf`
--

CREATE TABLE `riwayat_laporan_pdf` (
  `id` bigint UNSIGNED NOT NULL,
  `id_warung` bigint UNSIGNED NOT NULL,
  `id_user` bigint UNSIGNED NOT NULL,
  `nomor_laporan` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `judul` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `start_date` date DEFAULT NULL,
  `end_date` date DEFAULT NULL,
  `id_kategori` bigint UNSIGNED DEFAULT NULL,
  `kategori_label` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `total_masuk` decimal(15,2) NOT NULL DEFAULT '0.00',
  `total_keluar` decimal(15,2) NOT NULL DEFAULT '0.00',
  `total_keuntungan` decimal(15,2) NOT NULL DEFAULT '0.00',
  `total_admin_toko` decimal(15,2) NOT NULL DEFAULT '0.00',
  `total_admin_bank` decimal(15,2) NOT NULL DEFAULT '0.00',
  `total_transaksi` int NOT NULL DEFAULT '0',
  `catatan` text COLLATE utf8mb4_unicode_ci,
  `filter_snapshot` json DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `sessions`
--

CREATE TABLE `sessions` (
  `id` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `user_id` bigint UNSIGNED DEFAULT NULL,
  `ip_address` varchar(45) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `user_agent` text COLLATE utf8mb4_unicode_ci,
  `payload` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `last_activity` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `sessions`
--

INSERT INTO `sessions` (`id`, `user_id`, `ip_address`, `user_agent`, `payload`, `last_activity`) VALUES
('4EAoQsaww7DGdc48blWiwzznuuE3y9n5ZM7tLv5h', 3, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:160.0) Gecko/20100101 Firefox/160.0', 'YTo0OntzOjY6Il90b2tlbiI7czo0MDoiMzZWQVljZVJpTjVyNVBNM3J1R3RRa1RBejZsSzM4SVNsRVozTzVTRCI7czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6NDQ6Imh0dHA6Ly9sb2NhbGhvc3Q6ODAwMC9kZXZlbG9wZXIva2FzaXIvY3JlYXRlIjt9czo1MDoibG9naW5fd2ViXzU5YmEzNmFkZGMyYjJmOTQwMTU4MGYwMTRjN2Y1OGVhNGUzMDk4OWQiO2k6Mzt9', 1794898677),
('Kp0GlNfzOlpnPfn2xgDAxAsyvVpTGALI0ORJFUjS', NULL, '127.0.0.1', 'Symfony', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoiNlUycHRoRGFFdFJZNXlsY0pnaVVoMFJKTnVzQ09QS2dDZVF4V2hDaCI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6MzI6Imh0dHA6Ly9sb2NhbGhvc3QvZGV2ZWxvcGVyL2xvZ2luIjt9czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319fQ==', 1794897800),
('mMBSYFcAuL56vk16GqSgtmWztrG7ON6hHRBruDjW', 3, '127.0.0.1', 'Symfony', 'YTo0OntzOjY6Il90b2tlbiI7czo0MDoiNlUycHRoRGFFdFJZNXlsY0pnaVVoMFJKTnVzQ09QS2dDZVF4V2hDaCI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6MzY6Imh0dHA6Ly9sb2NhbGhvc3QvZGV2ZWxvcGVyL2Rhc2hib2FyZCI7fXM6NjoiX2ZsYXNoIjthOjI6e3M6Mzoib2xkIjthOjA6e31zOjM6Im5ldyI7YTowOnt9fXM6NTA6ImxvZ2luX3dlYl81OWJhMzZhZGRjMmIyZjk0MDE1ODBmMDE0YzdmNThlYTRlMzA5ODlkIjtpOjM7fQ==', 1794897800);

-- --------------------------------------------------------

--
-- Table structure for table `transaksi`
--

CREATE TABLE `transaksi` (
  `id` bigint UNSIGNED NOT NULL,
  `kode_transaksi` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `id_user` bigint UNSIGNED DEFAULT NULL,
  `id_kas_warung` bigint UNSIGNED NOT NULL,
  `id_kas_sumber` bigint UNSIGNED DEFAULT NULL,
  `id_kategori` bigint UNSIGNED DEFAULT NULL,
  `id_item_jualan` bigint UNSIGNED DEFAULT NULL,
  `kategori` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'transaksi_customer',
  `sub_kategori` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `metode_transaksi` varchar(30) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `metode_pembayaran` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'cash',
  `bank_tujuan` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `nomor_tujuan` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `nama_tujuan` varchar(150) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `nominal` bigint UNSIGNED NOT NULL DEFAULT '0',
  `biaya_admin` bigint UNSIGNED NOT NULL DEFAULT '0',
  `biaya_admin_bank` bigint UNSIGNED NOT NULL DEFAULT '0',
  `keuntungan` bigint NOT NULL DEFAULT '0',
  `kas_masuk` bigint UNSIGNED NOT NULL DEFAULT '0',
  `kas_keluar` bigint UNSIGNED NOT NULL DEFAULT '0',
  `total` bigint UNSIGNED NOT NULL,
  `is_profit` tinyint(1) NOT NULL DEFAULT '0',
  `jenis` enum('masuk','keluar') COLLATE utf8mb4_unicode_ci NOT NULL,
  `status` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'sukses',
  `nama_item` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `harga_item` bigint UNSIGNED DEFAULT NULL,
  `keterangan` text COLLATE utf8mb4_unicode_ci,
  `tanggal` date NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `transaksi`
--

INSERT INTO `transaksi` (`id`, `kode_transaksi`, `id_user`, `id_kas_warung`, `id_kas_sumber`, `id_kategori`, `id_item_jualan`, `kategori`, `sub_kategori`, `metode_transaksi`, `metode_pembayaran`, `bank_tujuan`, `nomor_tujuan`, `nama_tujuan`, `nominal`, `biaya_admin`, `biaya_admin_bank`, `keuntungan`, `kas_masuk`, `kas_keluar`, `total`, `is_profit`, `jenis`, `status`, `nama_item`, `harga_item`, `keterangan`, `tanggal`, `created_at`, `updated_at`) VALUES
(2, 'TRX-20261116-001', 2, 1, 3, 1, NULL, 'transaksi_customer', NULL, 'kartu', 'cash', NULL, NULL, NULL, 150000000, 3500000, 338000, 3162000, 153500000, 0, 153500000, 0, 'masuk', 'sukses', NULL, NULL, 'Akumulasi Transaksi BRILink Kemarin', '2026-11-16', '2026-11-16 07:30:00', '2026-11-16 07:30:00'),
(3, 'TRX-20261117-002', 2, 1, 3, 1, NULL, 'transaksi_customer', NULL, 'kartu', 'cash', NULL, NULL, NULL, 5000000, 75000, 10000, 65000, 5075000, 0, 5075000, 0, 'masuk', 'sukses', NULL, NULL, 'Setor Tunai Nasabah - Shift Siang', '2026-11-17', '2026-11-17 03:15:00', '2026-11-17 03:15:00'),
(4, 'TRX-20261117-003', 2, 1, 3, 4, NULL, 'transaksi_customer', NULL, 'kartu', 'cash', NULL, NULL, NULL, 8000000, 140000, 13000, 127000, 0, 8000000, 8000000, 0, 'keluar', 'sukses', NULL, NULL, 'Tarik Tunai Nasabah - Shift Malam', '2026-11-17', '2026-11-17 11:45:00', '2026-11-17 11:45:00'),
(5, 'TRX-20261117-004', 2, 1, NULL, NULL, 1, 'kas_toko', NULL, 'cash', 'cash', NULL, NULL, NULL, 68000, 0, 0, 18000, 68000, 0, 68000, 0, 'masuk', 'sukses', 'Parfum', 68000, 'Penjualan Parfum Toko (1 pcs)', '2026-11-17', '2026-11-17 06:20:00', '2026-11-17 06:20:00'),
(6, NULL, 1, 2, NULL, NULL, NULL, 'transaksi_brilink', NULL, NULL, 'ewallet', NULL, NULL, NULL, 10000000, 0, 0, 0, 10000000, 0, 10000000, 0, 'masuk', 'sukses', NULL, NULL, '1', '2026-11-17', '2026-11-16 19:46:29', '2026-11-16 19:46:29'),
(7, NULL, 1, 2, NULL, NULL, NULL, 'penjualan_item', NULL, NULL, 'transfer', NULL, NULL, NULL, 20000, 2000, 0, 7000, 22000, 0, 22000, 0, 'masuk', 'sukses', 'Barang Uji Coba Laba', 20000, 'Pembeli transfer via QRIS', '2026-11-17', '2026-11-16 20:01:08', '2026-11-16 20:01:08'),
(8, 'TRF-20261117-000008', 1, 2, 14, NULL, NULL, 'pemindahan_dana', 'antar_cabang', NULL, 'bank', NULL, NULL, NULL, 5000000, 0, 0, 0, 0, 5000000, 5000000, 0, 'keluar', 'sukses', NULL, NULL, 'Transfer Dana ke Cabang Berkah Jaya (Rekening MANDIRI Utama)', '2026-11-17', '2026-11-16 21:21:54', '2026-11-16 21:27:14'),
(9, 'TRF-20261117-000009', 1, 14, 2, NULL, NULL, 'pemindahan_dana', 'antar_cabang', NULL, 'bank', NULL, NULL, NULL, 5000000, 0, 0, 0, 5000000, 0, 5000000, 0, 'masuk', 'sukses', NULL, NULL, 'Transfer Dana Masuk dari Cabang Uji Coba (Rekening BRI Utama)', '2026-11-17', '2026-11-16 21:21:54', '2026-11-16 21:27:14'),
(10, 'TRF-20261117-VVBWLS', 1, 2, 14, NULL, NULL, 'pemindahan_dana', 'antar_cabang', 'transfer', 'bank', 'Rekening MANDIRI Utama (Cabang Berkah Jaya)', NULL, NULL, 250000, 0, 2500, -2500, 0, 252500, 252500, 0, 'keluar', 'sukses', NULL, NULL, 'Transfer Dana ke Cabang Cabang Berkah Jaya (Rekening MANDIRI Utama) - Topup modal kas cabang Mandiri', '2026-11-17', '2026-11-16 22:07:37', '2026-11-16 22:07:37'),
(11, 'TRF-20261117-NSNBT8', 1, 14, 2, NULL, NULL, 'pemindahan_dana', 'antar_cabang', 'transfer', 'bank', 'Rekening BRI Utama (Uji Coba)', NULL, NULL, 250000, 0, 0, 0, 250000, 0, 250000, 0, 'masuk', 'sukses', NULL, NULL, 'Transfer Dana Masuk dari Cabang Uji Coba (Rekening BRI Utama) - Topup modal kas cabang Mandiri', '2026-11-17', '2026-11-16 22:07:37', '2026-11-16 22:07:37');

-- --------------------------------------------------------

--
-- Table structure for table `transfer_kas`
--

CREATE TABLE `transfer_kas` (
  `id` bigint UNSIGNED NOT NULL,
  `id_kas_asal` bigint UNSIGNED NOT NULL,
  `id_kas_tujuan` bigint UNSIGNED NOT NULL,
  `jumlah` bigint UNSIGNED NOT NULL,
  `biaya_transfer` bigint UNSIGNED NOT NULL DEFAULT '0',
  `keterangan` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `transfer_kas`
--

INSERT INTO `transfer_kas` (`id`, `id_kas_asal`, `id_kas_tujuan`, `jumlah`, `biaya_transfer`, `keterangan`, `created_at`, `updated_at`) VALUES
(1, 2, 14, 5000000, 0, NULL, '2026-11-16 21:21:54', '2026-11-16 21:21:54'),
(2, 2, 14, 250000, 2500, 'Topup modal kas cabang Mandiri', '2026-11-16 22:07:37', '2026-11-16 22:07:37');

-- --------------------------------------------------------

--
-- Table structure for table `user`
--

CREATE TABLE `user` (
  `id` bigint UNSIGNED NOT NULL,
  `nama` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `nomor_hp` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `username` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `role` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'kasir',
  `status` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'aktif',
  `password` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `remember_token` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `user`
--

INSERT INTO `user` (`id`, `nama`, `nomor_hp`, `username`, `email`, `email_verified_at`, `role`, `status`, `password`, `remember_token`, `created_at`, `updated_at`) VALUES
(1, 'Owner Agen', '08123456789', 'admin', 'admin@gmail.com', NULL, 'admin', 'aktif', '$2y$12$T37CSwmxw.lxnqlboYvA7OnY5FDz2TNbJOVIcv9STpzObqsp0yEk.', NULL, '2026-11-14 00:42:01', '2026-11-14 00:42:01'),
(2, 'Kasir Agen', '08123456789', 'kasir', 'kasir@gmail.com', NULL, 'kasir', 'aktif', '$2y$12$cPYalpQAUZXSirjyPEj70.p/sIaKLv5HgtHGJPevGPDCz6Ti.VAqy', NULL, '2026-11-14 00:42:01', '2026-11-14 00:42:01'),
(3, 'Master Developer', '081122334455', 'developer', 'developer@gmail.com', NULL, 'developer', 'aktif', '$2y$12$G35sU3iX2i3WeC9flDwh5.Pwl23aBVdWxZOPeK1YSxfosswm5Kj4K', NULL, '2026-11-16 22:34:40', '2026-11-16 22:34:40'),
(6, 'User01', '-', 'user01', 'user01@gmail.com', NULL, 'owner', 'aktif', '$2y$12$Qa64IEKnaK.1pUcVDVpm1.kPxviaB.4aaCPSIyeIbPda8WqCAcj0i', NULL, '2026-11-16 22:54:09', '2026-11-16 22:54:09'),
(7, 'Novita', '-', NULL, 'kasir2@gmail.com', NULL, 'kasir', 'aktif', '$2y$12$UxarQrUNJXo.7ZO/r61wG.Tl7nUHzylv7.SjdOLPYe8FWZ4Pt2qAi', NULL, '2026-11-16 22:56:06', '2026-11-16 22:56:06');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `banks`
--
ALTER TABLE `banks`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `banks_kode_unique` (`kode`);

--
-- Indexes for table `biaya_admin`
--
ALTER TABLE `biaya_admin`
  ADD PRIMARY KEY (`id`),
  ADD KEY `biaya_admin_id_kategori_foreign` (`id_kategori`),
  ADD KEY `biaya_admin_id_kas_foreign` (`id_kas`),
  ADD KEY `biaya_admin_kategori_range_index` (`id_warung`,`id_kategori`,`range_awal`,`range_akhir`),
  ADD KEY `biaya_admin_jenis_range_index` (`id_warung`,`jenis`,`range_awal`,`range_akhir`);

--
-- Indexes for table `detail_kas`
--
ALTER TABLE `detail_kas`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `detail_kas_id_kas_warung_pecahan_unique` (`id_kas_warung`,`pecahan`);

--
-- Indexes for table `detail_transaksi`
--
ALTER TABLE `detail_transaksi`
  ADD PRIMARY KEY (`id`),
  ADD KEY `detail_transaksi_id_transaksi_kas_foreign` (`id_transaksi_kas`);

--
-- Indexes for table `item_jualan`
--
ALTER TABLE `item_jualan`
  ADD PRIMARY KEY (`id`),
  ADD KEY `item_jualan_id_warung_foreign` (`id_warung`);

--
-- Indexes for table `kas`
--
ALTER TABLE `kas`
  ADD PRIMARY KEY (`id`),
  ADD KEY `kas_id_warung_foreign` (`id_warung`);

--
-- Indexes for table `kasir`
--
ALTER TABLE `kasir`
  ADD PRIMARY KEY (`id`),
  ADD KEY `kasir_id_user_foreign` (`id_user`);

--
-- Indexes for table `kasir_user`
--
ALTER TABLE `kasir_user`
  ADD PRIMARY KEY (`id_kasir`,`id_user`),
  ADD KEY `kasir_user_id_user_foreign` (`id_user`);

--
-- Indexes for table `kategori_transaksi`
--
ALTER TABLE `kategori_transaksi`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `kategori_transaksi_kode_unique` (`kode`);

--
-- Indexes for table `laporan_harian`
--
ALTER TABLE `laporan_harian`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `laporan_harian_id_warung_id_user_tanggal_unique` (`id_warung`,`id_user`,`tanggal`),
  ADD KEY `laporan_harian_id_user_foreign` (`id_user`),
  ADD KEY `laporan_harian_id_pelapor_foreign` (`id_pelapor`),
  ADD KEY `laporan_harian_id_warung_tanggal_index` (`id_warung`,`tanggal`);

--
-- Indexes for table `migrations`
--
ALTER TABLE `migrations`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `riwayat_laporan_pdf`
--
ALTER TABLE `riwayat_laporan_pdf`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `riwayat_laporan_pdf_nomor_laporan_unique` (`nomor_laporan`),
  ADD KEY `riwayat_laporan_pdf_id_user_foreign` (`id_user`),
  ADD KEY `riwayat_laporan_pdf_id_kategori_foreign` (`id_kategori`),
  ADD KEY `riwayat_laporan_pdf_id_warung_created_at_index` (`id_warung`,`created_at`);

--
-- Indexes for table `sessions`
--
ALTER TABLE `sessions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `sessions_user_id_index` (`user_id`),
  ADD KEY `sessions_last_activity_index` (`last_activity`);

--
-- Indexes for table `transaksi`
--
ALTER TABLE `transaksi`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `transaksi_kode_transaksi_unique` (`kode_transaksi`),
  ADD KEY `transaksi_id_user_foreign` (`id_user`),
  ADD KEY `transaksi_id_kas_sumber_foreign` (`id_kas_sumber`),
  ADD KEY `transaksi_id_item_jualan_foreign` (`id_item_jualan`),
  ADD KEY `transaksi_id_kas_warung_tanggal_index` (`id_kas_warung`,`tanggal`),
  ADD KEY `transaksi_id_kategori_tanggal_index` (`id_kategori`,`tanggal`),
  ADD KEY `transaksi_jenis_tanggal_index` (`jenis`,`tanggal`);

--
-- Indexes for table `transfer_kas`
--
ALTER TABLE `transfer_kas`
  ADD PRIMARY KEY (`id`),
  ADD KEY `transfer_kas_id_kas_asal_foreign` (`id_kas_asal`),
  ADD KEY `transfer_kas_id_kas_tujuan_foreign` (`id_kas_tujuan`);

--
-- Indexes for table `user`
--
ALTER TABLE `user`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `user_email_unique` (`email`),
  ADD UNIQUE KEY `user_username_unique` (`username`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `banks`
--
ALTER TABLE `banks`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=19;

--
-- AUTO_INCREMENT for table `biaya_admin`
--
ALTER TABLE `biaya_admin`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `detail_kas`
--
ALTER TABLE `detail_kas`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=51;

--
-- AUTO_INCREMENT for table `detail_transaksi`
--
ALTER TABLE `detail_transaksi`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `item_jualan`
--
ALTER TABLE `item_jualan`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `kas`
--
ALTER TABLE `kas`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=23;

--
-- AUTO_INCREMENT for table `kasir`
--
ALTER TABLE `kasir`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `kategori_transaksi`
--
ALTER TABLE `kategori_transaksi`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `laporan_harian`
--
ALTER TABLE `laporan_harian`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `migrations`
--
ALTER TABLE `migrations`
  MODIFY `id` int UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT for table `riwayat_laporan_pdf`
--
ALTER TABLE `riwayat_laporan_pdf`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `transaksi`
--
ALTER TABLE `transaksi`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT for table `transfer_kas`
--
ALTER TABLE `transfer_kas`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `user`
--
ALTER TABLE `user`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `biaya_admin`
--
ALTER TABLE `biaya_admin`
  ADD CONSTRAINT `biaya_admin_id_kas_foreign` FOREIGN KEY (`id_kas`) REFERENCES `kas` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `biaya_admin_id_kategori_foreign` FOREIGN KEY (`id_kategori`) REFERENCES `kategori_transaksi` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `biaya_admin_id_warung_foreign` FOREIGN KEY (`id_warung`) REFERENCES `kasir` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `detail_kas`
--
ALTER TABLE `detail_kas`
  ADD CONSTRAINT `detail_kas_id_kas_warung_foreign` FOREIGN KEY (`id_kas_warung`) REFERENCES `kas` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `detail_transaksi`
--
ALTER TABLE `detail_transaksi`
  ADD CONSTRAINT `detail_transaksi_id_transaksi_kas_foreign` FOREIGN KEY (`id_transaksi_kas`) REFERENCES `transaksi` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `item_jualan`
--
ALTER TABLE `item_jualan`
  ADD CONSTRAINT `item_jualan_id_warung_foreign` FOREIGN KEY (`id_warung`) REFERENCES `kasir` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `kas`
--
ALTER TABLE `kas`
  ADD CONSTRAINT `kas_id_warung_foreign` FOREIGN KEY (`id_warung`) REFERENCES `kasir` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `kasir`
--
ALTER TABLE `kasir`
  ADD CONSTRAINT `kasir_id_user_foreign` FOREIGN KEY (`id_user`) REFERENCES `user` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `kasir_user`
--
ALTER TABLE `kasir_user`
  ADD CONSTRAINT `kasir_user_id_kasir_foreign` FOREIGN KEY (`id_kasir`) REFERENCES `kasir` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `kasir_user_id_user_foreign` FOREIGN KEY (`id_user`) REFERENCES `user` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `laporan_harian`
--
ALTER TABLE `laporan_harian`
  ADD CONSTRAINT `laporan_harian_id_pelapor_foreign` FOREIGN KEY (`id_pelapor`) REFERENCES `user` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `laporan_harian_id_user_foreign` FOREIGN KEY (`id_user`) REFERENCES `user` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `laporan_harian_id_warung_foreign` FOREIGN KEY (`id_warung`) REFERENCES `kasir` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `riwayat_laporan_pdf`
--
ALTER TABLE `riwayat_laporan_pdf`
  ADD CONSTRAINT `riwayat_laporan_pdf_id_kategori_foreign` FOREIGN KEY (`id_kategori`) REFERENCES `kategori_transaksi` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `riwayat_laporan_pdf_id_user_foreign` FOREIGN KEY (`id_user`) REFERENCES `user` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `riwayat_laporan_pdf_id_warung_foreign` FOREIGN KEY (`id_warung`) REFERENCES `kasir` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `transaksi`
--
ALTER TABLE `transaksi`
  ADD CONSTRAINT `transaksi_id_item_jualan_foreign` FOREIGN KEY (`id_item_jualan`) REFERENCES `item_jualan` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `transaksi_id_kas_sumber_foreign` FOREIGN KEY (`id_kas_sumber`) REFERENCES `kas` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `transaksi_id_kas_warung_foreign` FOREIGN KEY (`id_kas_warung`) REFERENCES `kas` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `transaksi_id_kategori_foreign` FOREIGN KEY (`id_kategori`) REFERENCES `kategori_transaksi` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `transaksi_id_user_foreign` FOREIGN KEY (`id_user`) REFERENCES `user` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `transfer_kas`
--
ALTER TABLE `transfer_kas`
  ADD CONSTRAINT `transfer_kas_id_kas_asal_foreign` FOREIGN KEY (`id_kas_asal`) REFERENCES `kas` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `transfer_kas_id_kas_tujuan_foreign` FOREIGN KEY (`id_kas_tujuan`) REFERENCES `kas` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
