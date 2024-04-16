-- phpMyAdmin SQL Dump
-- version 4.9.0.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Apr 16, 2024 at 02:07 PM
-- Server version: 10.3.16-MariaDB
-- PHP Version: 7.3.6

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `db_inventory`
--

-- --------------------------------------------------------

--
-- Table structure for table `barang`
--

CREATE TABLE `barang` (
  `id` int(11) NOT NULL,
  `kode_barang` varchar(20) NOT NULL,
  `nama_barang` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `barang`
--

INSERT INTO `barang` (`id`, `kode_barang`, `nama_barang`) VALUES
(1, 'PS-PLD24T500', 'CINEMAX LED'),
(2, 'PS-PLD24T605', 'CINEMAX LED');

-- --------------------------------------------------------

--
-- Table structure for table `lokasi`
--

CREATE TABLE `lokasi` (
  `id` int(11) NOT NULL,
  `kode_lokasi` varchar(20) NOT NULL,
  `deskripsi` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `lokasi`
--

INSERT INTO `lokasi` (`id`, `kode_lokasi`, `deskripsi`) VALUES
(1, 'GBJ01', 'Krapyak Site');

-- --------------------------------------------------------

--
-- Table structure for table `stok_barang`
--

CREATE TABLE `stok_barang` (
  `id` int(11) NOT NULL,
  `kode_barang` int(11) NOT NULL,
  `id_lokasi` int(11) NOT NULL,
  `tanggal_masuk` date NOT NULL,
  `saldo` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `transaksi_history`
--

CREATE TABLE `transaksi_history` (
  `id` int(11) NOT NULL,
  `bukti` varchar(50) NOT NULL,
  `tanggal_transaksi` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `id_lokasi` int(11) NOT NULL,
  `kode_barang` int(11) NOT NULL,
  `qty` int(11) NOT NULL,
  `program` enum('masuk','keluar') NOT NULL,
  `user` varchar(20) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `barang`
--
ALTER TABLE `barang`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `lokasi`
--
ALTER TABLE `lokasi`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `stok_barang`
--
ALTER TABLE `stok_barang`
  ADD PRIMARY KEY (`id`),
  ADD KEY `kode_barang` (`kode_barang`),
  ADD KEY `id_lokasi` (`id_lokasi`);

--
-- Indexes for table `transaksi_history`
--
ALTER TABLE `transaksi_history`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `barang`
--
ALTER TABLE `barang`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `lokasi`
--
ALTER TABLE `lokasi`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `stok_barang`
--
ALTER TABLE `stok_barang`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `transaksi_history`
--
ALTER TABLE `transaksi_history`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=23;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `stok_barang`
--
ALTER TABLE `stok_barang`
  ADD CONSTRAINT `id_lokasi` FOREIGN KEY (`id_lokasi`) REFERENCES `lokasi` (`id`),
  ADD CONSTRAINT `kode_barang` FOREIGN KEY (`kode_barang`) REFERENCES `barang` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
