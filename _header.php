<?php
session_start();

require 'config.php';

if ( isset($_SESSION['username']) == false ) {

	header('Location: login.php');
	exit();

}

?>
<html>
<head>
	<title>Daftar Prodi - Sistem Informasi Daya Tampung</title>
	<link rel="stylesheet" href="https://cdn.simplecss.org/simple.min.css">
</head>
<body>
	<header>
	<h1>Nutnessr</h1>
	<nav>
	    <a href="home.php">Tentang Kami</a>
	    <a href="Profile.php">Profile</a>
            <a href="Penyewaan_Kebutuhan.php">Penyewaan Kebutuhan Olahraga</a>
            <a href="food-diary.php">Food Diary</a>
            <a href="community.php">Community</a>
            <a href="logout.php">Logout</a>
	</nav>	
	</header>
