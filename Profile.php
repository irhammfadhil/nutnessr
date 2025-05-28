<?php
require('_header.php'); 

$pesan = "";

if ( isset($_POST['act']) == true ){
	if ( $_POST['act'] == "chgPassword" ){
		$passwordLama = $_POST['password_lama'];
		$passwordBaru = $_POST['password_baru'];
		$passwordBaruKonfirmasi = $_POST['password_baru_konfirmasi'];
		
		if ( $passwordBaru == $passwordBaruKonfirmasi ) {
			
			$conn = mysqli_connect($db_host,$db_user,$db_pass,$db_name);
			
			$sql = "SELECT password FROM users WHERE username='".mysqli_real_escape_string($conn,$_SESSION['username'])."'";
			
			$res = mysqli_query($conn,$sql);
			$row = mysqli_fetch_assoc($res);
			
			$passwordLamaDb = $row['password'];
			
			if ( md5($passwordLama) == $passwordLamaDb ){

				$sql = "UPDATE users 
						SET password = md5('".$passwordBaru."')
						WHERE username='".mysqli_real_escape_string($conn,$_SESSION['username'])."';";
				$res = mysqli_query($conn,$sql);
				
				$pesan = "password berhasil diupdate";
			} else {
				$pesan = "password lama tidak valid";
			}
			
			
			mysqli_close($conn);
			
		} else {
			$pesan = "Password Baru belum terkonfirmasi";
		}
	}
	
	if ( $_POST['act'] == "uploadFoto" ) {
		// cek extensi harus .jpg atau .png
		
		$a_extensi = explode(".",$_FILES['foto']['name']);
		
		$jml_a_extensi = count($a_extensi);
		
		$extensi = $a_extensi[$jml_a_extensi - 1];
		$extensi = strtolower($extensi);
		
		$ukuran = $_FILES['foto']['size'];
		
		if (($extensi == "jpg" || $extensi == "png") && $ukuran <= 90000 ) {
		
			// ambil data file
			$namaFile = generateRandomString() . str_replace(" ", "_", $_FILES['foto']['name']);
			$namaSementara = $_FILES['foto']['tmp_name'];

			// tentukan lokasi file akan dipindahkan
			$dirUpload = "img/";

			echo "Temp file: $namaSementara<br>";
			echo "Destination: " . $dirUpload.$namaFile . "<br>";
			$terupload = move_uploaded_file($namaSementara, $dirUpload.$namaFile);

			if ($terupload) {
				$pesan =  "Upload Foto berhasil!";
				$conn = mysqli_connect($db_host,$db_user,$db_pass,$db_name);
				
				$sql = "UPDATE users
						SET foto = '".$namaFile."'
						WHERE username='".mysqli_real_escape_string($conn,$_SESSION['username'])."'";
				$res = mysqli_query($conn,$sql);
				
				mysqli_close($conn);
				
			} else {
				$pesan =  "Upload Foto Gagal!";
			}
		}else{
		    $pesan = "File Foto harus .jpg atau .png dan Maksimal 80 Kb";
		}
	}
	if ($_POST['act'] == "deleteFoto") {
	    $conn = mysqli_connect($db_host,$db_user,$db_pass,$db_name);
	    $sql = "SELECT foto FROM daftar_user WHERE username='".mysqli_real_escape_string($conn,$_SESSION['username'])."'";
	    $res = mysqli_query($conn, $sql);
	    $row = mysqli_fetch_assoc($res);
	    $foto = $row['foto'];

		// Hapus file jika ada
		if ($foto != "" && file_exists("img/".$foto)) {
			unlink("img/".$foto);
		}

		// Update database
		$sql = "UPDATE users SET foto = NULL WHERE username='".mysqli_real_escape_string($conn,$_SESSION['username'])."'";
		mysqli_query($conn, $sql);
		mysqli_close($conn);

		$pesan = "Foto berhasil dihapus!";
	}

}

$conn = mysqli_connect($db_host,$db_user,$db_pass,$db_name);

$sql = "SELECT foto FROM users WHERE username='".mysqli_real_escape_string($conn,$_SESSION['username'])."'";

$res = mysqli_query($conn,$sql);
$row = mysqli_fetch_assoc($res);

$foto = $row['foto'];

mysqli_close($conn);
?>

<article>
		<?php if( $pesan != "" ){ ?>
		<mark><?=$pesan?></mark>
		<?php } ?>
		<img src="img/<?=$foto?>" />
		<p>
		Username : <?=$_SESSION['username']?>
		</p>
		<hr />
		<form action="Profile.php" method="post" enctype="multipart/form-data">
		<input type="hidden" name="act" value="uploadFoto" />
		<p>
		foto harus bertipe .jpg 
		ukuran maksimal 80 Kb
		</p>
		<p>
		<label>Foto</label>
		<input type="file" name="foto" />
		</p>
		<p>
		<input type="submit" value="upload_foto" />
		</p>
		</form>
		<?php if ($foto != "" && file_exists("img/".$foto)) { ?>
    		<img src="img/<?=$foto?>" width="120" />
    		<form action="Profile.php" method="post" style="display:inline;">
        	<input type="hidden" name="act" value="deleteFoto" />
        	<button type="submit" onclick="return confirm('Hapus foto ini?')" class="btn btn-danger btn-sm" title="Hapus Foto">
            	 🗑️
       		</button>
    		</form>
		<?php } else { ?>
    		<p><i>Foto belum diunggah</i></p>
		<?php } ?>
		<hr />
		<form action="Profile.php" method="post">
		<input type="hidden" name="act" value="chgPassword" />
		<p>
		<label>Password Lama</label>
		<input type="password" name="password_lama" />
		</p>
		<p>
		<label>Password Baru</label>
		<input type="password" name="password_baru" />
		</p>
		<p>
		<label>Password Baru Konfirmasi</label>
		<input type="password" name="password_baru_konfirmasi" />
		</p>
		<p>
		<input type="submit" value="Ubah Password" />
		</p>
		</form>
	</article>
</body>
</html>
