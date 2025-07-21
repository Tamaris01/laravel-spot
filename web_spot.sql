-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Dec 16, 2024 at 11:06 AM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.1.25

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `web_spot`
--

-- --------------------------------------------------------

--
-- Table structure for table `kendaraan`
--

CREATE TABLE `kendaraan` (
  `plat_nomor` varchar(255) NOT NULL,
  `id_pengguna` varchar(255) NOT NULL,
  `jenis` enum('Mobil','Motor') NOT NULL,
  `qr_code` varchar(255) NOT NULL,
  `foto` varchar(255) NOT NULL,
  `warna` enum('Merah','Biru','Hijau','Kuning','Hitam','Putih','Abu-abu','Silver','Oranye','Cokelat','Ungu','Emas','Pink') NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `kendaraan`
--

INSERT INTO `kendaraan` (`plat_nomor`, `id_pengguna`, `jenis`, `qr_code`, `foto`, `warna`) VALUES
('BP 0001 GA', '222331', 'Mobil', '/images/qrcodes/BP 0001 GA.png', 'kendaraan/mobil3.jpg', 'Kuning'),
('BP 0002 IQ', '222332', 'Motor', '/images/qrcodes/BP 0002 IQ.png', 'kendaraan/motor4.jpg', 'Merah'),
('BP 0003 BW', '4342211082', 'Motor', '/images/qrcodes/BP 0003 BW.png', 'kendaraan/motor5.jpg', 'Biru'),
('BP 0004 AW', '4342211083', 'Mobil', '/images/qrcodes/BP 0004 AW.png', 'kendaraan/mobil4.jpg', 'Merah'),
('BP 1121 AZ', '4342211045', 'Mobil', '/images/qrcodes/BP 1121 AZ.png', 'kendaraan/mobil2.jpg', 'Putih'),
('BP 1234 TA', '4342211050', 'Motor', '/images/qrcodes/BP 1234 TA.png', 'kendaraan/motor1.jpg', 'Putih'),
('BP 3141 MA', '4342211046', 'Motor', '/images/qrcodes/BP 3141 MA.png', 'kendaraan/motor3.jpg', 'Putih'),
('BP 5678 EL', '4342211036', 'Mobil', '/images/qrcodes/BP 5678 EL.png', 'kendaraan/mobil1.jpg', 'Putih'),
('BP 9101 EC', '4342211041', 'Motor', '/images/qrcodes/BP 9101 EC.png', 'kendaraan/motor2.jpg', 'Putih');

-- --------------------------------------------------------

--
-- Table structure for table `migrations`
--

CREATE TABLE `migrations` (
  `id` int(10) UNSIGNED NOT NULL,
  `migration` varchar(255) NOT NULL,
  `batch` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `migrations`
--

INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES
(1, '2019_12_14_000001_create_personal_access_tokens_table', 1),
(2, '2024_10_07_055825_create_pengguna_parkir_table', 1),
(3, '2024_10_07_055931_create_kendaraan_table', 1),
(4, '2024_10_07_060000_create_pengelola_parkir_table', 1),
(5, '2024_10_23_073916_create_riwayat_parkir_table', 1);

-- --------------------------------------------------------

--
-- Table structure for table `pengelola_parkir`
--

CREATE TABLE `pengelola_parkir` (
  `id_pengelola` varchar(255) NOT NULL,
  `nama` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `foto` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `pengelola_parkir`
--

INSERT INTO `pengelola_parkir` (`id_pengelola`, `nama`, `email`, `password`, `foto`) VALUES
('1234567890', 'Admin Parkir', 'adminparkir@gmail.com', '$2y$10$WvQZMG1g7MBqcN3Er3F3k.sjWwovzpWuzoHQGQOzxr1RqFJt6egk.', 'profil/default.png');

-- --------------------------------------------------------

--
-- Table structure for table `pengguna_parkir`
--

CREATE TABLE `pengguna_parkir` (
  `id_pengguna` varchar(255) NOT NULL,
  `kategori` enum('Mahasiswa','Dosen/Karyawan','Tamu') NOT NULL,
  `nama` varchar(255) NOT NULL,
  `foto` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `status` enum('aktif','nonaktif') NOT NULL DEFAULT 'nonaktif'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `pengguna_parkir`
--

INSERT INTO `pengguna_parkir` (`id_pengguna`, `kategori`, `nama`, `foto`, `email`, `password`, `status`) VALUES
('222331', 'Dosen/Karyawan', 'Gilang Bagus Ramadhan, A.Md. Kom', 'profil/Gilang.jpg', 'gilang@polibatam.ac.id', '$2y$10$1VzUbOtsm24ANClfg68ze.RlNJCMDspT3N9m7VTFK6sURHRzaLQeW', 'aktif'),
('222332', 'Dosen/Karyawan', 'Iqbal Afif, A.Md.Kom', 'profil/Iqbal.jpg', 'iqbal@polibatam.ac.id', '$2y$10$9kcLP6/5m9isWwCV0.rsLupkNxDYDFJAmnHnk3CXNb8//LTslaZSW', 'aktif'),
('4342211036', 'Mahasiswa', 'Elsa Marina S', 'profil/Elsa.jpg', 'elsa@gmail.com', '$2y$10$0HuzkHApjHQ2b83aQmHoBOqy4I.0qBo.ZTnBGorpjb0mVfoG3ULzm', 'aktif'),
('4342211041', 'Mahasiswa', 'Elicia Sandova', 'profil/Elicia.jpg', 'elicia@gmail.com', '$2y$10$PPUoXipTYJvgrgRZTxZjYenljdhqQLEVTb/lBUqLBfilG.LSNzNmm', 'aktif'),
('4342211045', 'Mahasiswa', 'Alifzidan Rizky', 'profil/Alifzidan.jpg', 'alif@gmail.com', '$2y$10$7OtIR/h8YflLN0nFssAznOZ2PAw6tFBASpEzNy9jBPLMoK6hFL.o.', 'aktif'),
('4342211046', 'Mahasiswa', 'Maulana Arianto', 'profil/Maulana.jpg', 'maulana@gmail.com', '$2y$10$1yOY1iG5W3TUBi04eZxlA.WgWnQYuMzjG.pt7pCrb/N7BdQFgbhhm', 'aktif'),
('4342211050', 'Mahasiswa', 'Tamaris Roulina S', 'profil/Tamaris.png', 'tama@gmail.com', '$2y$10$8tRdPSpUibVoDWTg.wToYOYje7h2X8by3xlGzCdmQ9I7.7z7Nr2CW', 'aktif'),
('4342211082', 'Tamu', 'Budi Santoso', 'profil/Budi.jpg', 'budi@gmail.com', '$2y$10$UNPiqOuwEsj1pqJR2EYUjOfRjUqnN8wdx1LB5/b8DMY.zHFjI.4Ri', 'aktif'),
('4342211083', 'Tamu', 'Anna Wati', 'profil/Anna.jpg', 'anna@gmail.com', '$2y$10$Py0KTjDMxLipUN7EU7Sw6.nBFguAnDAimu1W2bidr6CgG0M8fOanm', 'aktif');

-- --------------------------------------------------------

--
-- Table structure for table `personal_access_tokens`
--

CREATE TABLE `personal_access_tokens` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `tokenable_type` varchar(255) NOT NULL,
  `tokenable_id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `token` varchar(64) NOT NULL,
  `abilities` text DEFAULT NULL,
  `last_used_at` timestamp NULL DEFAULT NULL,
  `expires_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `riwayat_parkir`
--

CREATE TABLE `riwayat_parkir` (
  `id_riwayat_parkir` varchar(255) NOT NULL,
  `id_pengguna` varchar(255) NOT NULL,
  `waktu_masuk` datetime NOT NULL,
  `waktu_keluar` datetime DEFAULT NULL,
  `status_parkir` enum('masuk','keluar') NOT NULL,
  `plat_nomor` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `riwayat_parkir`
--

INSERT INTO `riwayat_parkir` (`id_riwayat_parkir`, `id_pengguna`, `waktu_masuk`, `waktu_keluar`, `status_parkir`, `plat_nomor`) VALUES
('PARK001', '4342211050', '2024-12-14 07:00:00', '2024-12-14 06:00:00', 'keluar', 'BP 1234 TA'),
('PARK002', '4342211036', '2024-12-14 07:00:00', '2024-12-14 05:00:00', 'keluar', 'BP 5678 EL'),
('PARK003', '4342211041', '2024-12-14 08:00:00', '2024-12-14 04:00:00', 'keluar', 'BP 9101 EC'),
('PARK004', '4342211045', '2024-12-14 07:00:00', '2024-12-14 07:00:00', 'keluar', 'BP 1121 AZ'),
('PARK005', '4342211046', '2024-12-14 09:00:00', '2024-12-14 08:00:00', 'keluar', 'BP 3141 MA'),
('PARK006', '4342211050', '2024-12-14 07:00:00', NULL, 'masuk', 'BP 1234 TA'),
('PARK007', '4342211036', '2024-12-14 08:00:00', NULL, 'masuk', 'BP 5678 EL'),
('PARK008', '4342211041', '2024-12-14 12:00:00', NULL, 'masuk', 'BP 9101 EC'),
('PARK009', '4342211045', '2024-12-14 13:00:00', NULL, 'masuk', 'BP 1121 AZ'),
('PARK010', '4342211050', '2024-12-14 15:00:00', NULL, 'masuk', 'BP 1234 TA'),
('PARK011', '4342211036', '2024-12-14 15:00:00', NULL, 'masuk', 'BP 5678 EL'),
('PARK012', '4342211041', '2024-12-14 17:00:00', NULL, 'masuk', 'BP 9101 EC'),
('PARK013', '4342211045', '2024-12-14 18:00:00', NULL, 'masuk', 'BP 1121 AZ'),
('PARK014', '4342211082', '2024-12-14 18:00:00', NULL, 'masuk', 'BP 0003 BW'),
('PARK015', '4342211083', '2024-12-14 18:00:00', NULL, 'masuk', 'BP 0004 AW'),
('PARK016', '222331', '2024-12-14 19:00:00', NULL, 'masuk', 'BP 0001 GA'),
('PARK017', '222332', '2024-12-14 20:00:00', NULL, 'masuk', 'BP 0002 IQ');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `kendaraan`
--
ALTER TABLE `kendaraan`
  ADD PRIMARY KEY (`plat_nomor`),
  ADD KEY `kendaraan_id_pengguna_foreign` (`id_pengguna`);

--
-- Indexes for table `migrations`
--
ALTER TABLE `migrations`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `pengelola_parkir`
--
ALTER TABLE `pengelola_parkir`
  ADD PRIMARY KEY (`id_pengelola`),
  ADD UNIQUE KEY `pengelola_parkir_email_unique` (`email`);

--
-- Indexes for table `pengguna_parkir`
--
ALTER TABLE `pengguna_parkir`
  ADD PRIMARY KEY (`id_pengguna`),
  ADD UNIQUE KEY `pengguna_parkir_email_unique` (`email`);

--
-- Indexes for table `personal_access_tokens`
--
ALTER TABLE `personal_access_tokens`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `personal_access_tokens_token_unique` (`token`),
  ADD KEY `personal_access_tokens_tokenable_type_tokenable_id_index` (`tokenable_type`,`tokenable_id`);

--
-- Indexes for table `riwayat_parkir`
--
ALTER TABLE `riwayat_parkir`
  ADD KEY `riwayat_parkir_id_pengguna_foreign` (`id_pengguna`),
  ADD KEY `riwayat_parkir_plat_nomor_foreign` (`plat_nomor`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `migrations`
--
ALTER TABLE `migrations`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `personal_access_tokens`
--
ALTER TABLE `personal_access_tokens`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `kendaraan`
--
ALTER TABLE `kendaraan`
  ADD CONSTRAINT `kendaraan_id_pengguna_foreign` FOREIGN KEY (`id_pengguna`) REFERENCES `pengguna_parkir` (`id_pengguna`) ON DELETE CASCADE;

--
-- Constraints for table `riwayat_parkir`
--
ALTER TABLE `riwayat_parkir`
  ADD CONSTRAINT `riwayat_parkir_id_pengguna_foreign` FOREIGN KEY (`id_pengguna`) REFERENCES `pengguna_parkir` (`id_pengguna`) ON DELETE CASCADE,
  ADD CONSTRAINT `riwayat_parkir_plat_nomor_foreign` FOREIGN KEY (`plat_nomor`) REFERENCES `kendaraan` (`plat_nomor`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
