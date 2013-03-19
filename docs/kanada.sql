-- phpMyAdmin SQL Dump
-- version 3.4.5
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: Mar 19, 2013 at 03:50 PM
-- Server version: 5.5.16
-- PHP Version: 5.3.8

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Database: `kasir1`
--

-- --------------------------------------------------------

--
-- Table structure for table `absensi`
--

CREATE TABLE IF NOT EXISTS `absensi` (
  `NIK` varchar(10) NOT NULL,
  `tanggal` date NOT NULL,
  `pulang` int(11) NOT NULL,
  `datang` int(11) NOT NULL,
  `status` enum('masuk','izin','alpha','libur/off') DEFAULT NULL,
  PRIMARY KEY (`NIK`,`tanggal`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `barang`
--

CREATE TABLE IF NOT EXISTS `barang` (
  `id_barang` varchar(10) NOT NULL,
  `nama` varchar(128) NOT NULL,
  `harga` varchar(128) NOT NULL,
  `total_barang` int(11) NOT NULL,
  `stok_awal` int(11) NOT NULL,
  `stok_barang` int(11) NOT NULL,
  `stok_opname` int(11) NOT NULL,
  `mutasi_masuk` int(11) NOT NULL,
  `mutasi_keluar` int(11) NOT NULL,
  `kelompok_barang` varchar(10) NOT NULL,
  `diskon` varchar(10) NOT NULL,
  `jumlah_terjual` int(11) NOT NULL,
  PRIMARY KEY (`id_barang`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `barang_masuk`
--

CREATE TABLE IF NOT EXISTS `barang_masuk` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_mutasi_masuk` varchar(11) NOT NULL,
  `id_barang` varchar(10) NOT NULL,
  `tanggal` date NOT NULL,
  `qty` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `id_barang` (`id_barang`),
  KEY `id_mutasi_masuk` (`id_mutasi_masuk`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=19566 ;

-- --------------------------------------------------------

--
-- Table structure for table `cashdrawer`
--

CREATE TABLE IF NOT EXISTS `cashdrawer` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `kassa` int(11) NOT NULL,
  `shift` int(1) NOT NULL,
  `ra` varchar(11) NOT NULL,
  `cash` varchar(11) NOT NULL,
  `tanggal` date NOT NULL,
  `kasir1` int(11) NOT NULL,
  `kasir2` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `item_transaksi_penjualan`
--

CREATE TABLE IF NOT EXISTS `item_transaksi_penjualan` (
  `id_transaksi` varchar(16) NOT NULL DEFAULT '',
  `id_barang` varchar(10) NOT NULL DEFAULT '',
  `qty` int(11) NOT NULL,
  `diskon` varchar(10) NOT NULL,
  KEY `id_transaksi` (`id_transaksi`),
  KEY `id_barang` (`id_barang`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `karyawan`
--

CREATE TABLE IF NOT EXISTS `karyawan` (
  `NIK` varchar(10) NOT NULL,
  `nama` varchar(128) NOT NULL,
  `alamat` varchar(128) NOT NULL,
  `telepon` varchar(14) NOT NULL,
  `divisi` int(11) NOT NULL,
  PRIMARY KEY (`NIK`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `penggantian_barang`
--

CREATE TABLE IF NOT EXISTS `penggantian_barang` (
  `id_barang` varchar(10) NOT NULL,
  `tanggal` date NOT NULL,
  `harga_ganti` varchar(10) NOT NULL,
  `qty` int(11) NOT NULL,
  KEY `id_barang` (`id_barang`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `pengguna`
--

CREATE TABLE IF NOT EXISTS `pengguna` (
  `userid` int(11) NOT NULL AUTO_INCREMENT,
  `NIK` varchar(10) NOT NULL,
  `username` varchar(128) NOT NULL,
  `passwd` varchar(128) NOT NULL,
  `jabatan` enum('admin','supervisor','kasir','pramuniaga') DEFAULT NULL,
  `status` tinyint(4) NOT NULL,
  `flag_hapus` int(1) NOT NULL,
  `login_status` int(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`userid`),
  UNIQUE KEY `username` (`username`),
  UNIQUE KEY `NIK` (`NIK`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=35 ;

-- --------------------------------------------------------

--
-- Table structure for table `prestasi_karyawan`
--

CREATE TABLE IF NOT EXISTS `prestasi_karyawan` (
  `NIK` varchar(10) NOT NULL,
  `bulan` varchar(10) NOT NULL,
  `prestasi` text NOT NULL,
  PRIMARY KEY (`NIK`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `retur_barang`
--

CREATE TABLE IF NOT EXISTS `retur_barang` (
  `id_retur` varchar(11) NOT NULL,
  `id_barang` varchar(10) NOT NULL,
  `tanggal` date NOT NULL,
  `qty` int(11) NOT NULL,
  PRIMARY KEY (`id_retur`,`id_barang`),
  KEY `id_barang` (`id_barang`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `toko`
--

CREATE TABLE IF NOT EXISTS `toko` (
  `id_toko` int(11) NOT NULL,
  `nama` varchar(128) NOT NULL,
  `alamat` varchar(128) NOT NULL,
  `telepon` varchar(14) NOT NULL,
  PRIMARY KEY (`id_toko`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `transaksi_penjualan`
--

CREATE TABLE IF NOT EXISTS `transaksi_penjualan` (
  `kassa` int(11) NOT NULL DEFAULT '1',
  `id_transaksi` varchar(16) NOT NULL,
  `tanggal` date NOT NULL,
  `total` varchar(128) NOT NULL,
  `diskon` varchar(10) NOT NULL,
  `infaq` double NOT NULL DEFAULT '0',
  `no_cc` varchar(16) NOT NULL,
  `id_kasir` varchar(10) NOT NULL,
  `id_pramuniaga` varchar(128) NOT NULL,
  PRIMARY KEY (`id_transaksi`),
  KEY `id_kasir` (`id_kasir`),
  KEY `id_pramuniaga` (`id_pramuniaga`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `absensi`
--
ALTER TABLE `absensi`
  ADD CONSTRAINT `absensi_ibfk_1` FOREIGN KEY (`NIK`) REFERENCES `karyawan` (`NIK`);

--
-- Constraints for table `barang_masuk`
--
ALTER TABLE `barang_masuk`
  ADD CONSTRAINT `barang_masuk_ibfk_1` FOREIGN KEY (`id_barang`) REFERENCES `barang` (`id_barang`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `item_transaksi_penjualan`
--
ALTER TABLE `item_transaksi_penjualan`
  ADD CONSTRAINT `item_transaksi_penjualan_ibfk_1` FOREIGN KEY (`id_transaksi`) REFERENCES `transaksi_penjualan` (`id_transaksi`),
  ADD CONSTRAINT `item_transaksi_penjualan_ibfk_2` FOREIGN KEY (`id_barang`) REFERENCES `barang` (`id_barang`);

--
-- Constraints for table `karyawan`
--
ALTER TABLE `karyawan`
  ADD CONSTRAINT `karyawan_ibfk_1` FOREIGN KEY (`NIK`) REFERENCES `pengguna` (`NIK`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `penggantian_barang`
--
ALTER TABLE `penggantian_barang`
  ADD CONSTRAINT `penggantian_barang_ibfk_1` FOREIGN KEY (`id_barang`) REFERENCES `barang` (`id_barang`) ON DELETE NO ACTION;

--
-- Constraints for table `prestasi_karyawan`
--
ALTER TABLE `prestasi_karyawan`
  ADD CONSTRAINT `prestasi_karyawan_ibfk_1` FOREIGN KEY (`NIK`) REFERENCES `karyawan` (`NIK`);

--
-- Constraints for table `retur_barang`
--
ALTER TABLE `retur_barang`
  ADD CONSTRAINT `retur_barang_ibfk_1` FOREIGN KEY (`id_barang`) REFERENCES `barang` (`id_barang`);

--
-- Constraints for table `transaksi_penjualan`
--
ALTER TABLE `transaksi_penjualan`
  ADD CONSTRAINT `transaksi_penjualan_ibfk_1` FOREIGN KEY (`id_kasir`) REFERENCES `karyawan` (`NIK`);

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
