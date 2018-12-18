 <?php
/***********************************************************************************************
########## Author : Mr. Khwanchai Kaewyos (LookHin), Teuku Muhammad Rivai (BuffFreak) ##########
##########            Rewrite Code From : https://github.com/mgp25/Instagram-API           ##########
##########                     CHANGE THIS LINE DONT MAKE YOU A CODER!                   ##########
##########                    Official Blog : https://bahasakomputer.com                   ##########
##########                   Official Instagram LFL : https://instakuy.com               ##########
***********************************************************************************************/
error_reporting(0);
error_reporting(E_ALL ^ (E_NOTICE | E_WARNING));

include_once __DIR__ . "/Class/Instagram.php";
include_once __DIR__ . "/Function/function.php";

echo "===================================Created By Teuku Muhammad Rivai " . date("Y") . "======================================\n";
sleep(1);
echo "Masukkan Password\nInput : ";
$pass       = trim(fgets(STDIN));
$passa      = file_get_contents("https://api.bahasakomputer.com/pass.php");
$decodePass = json_decode($passa);
if (base64_encode($pass) === $decodePass->password) {
    echo "Ingin Menggunakan Socks (1 = ya) ?\nInput : ";
    $pakaiSock = trim(fgets(STDIN));
    if ($pakaiSock == 1) {
        echo "Silahkan Masukkan Nama File Yang Berisi SOCKS\nInput : ";
        $socks        = trim(fgets(STDIN));
        $getSocks     = file_get_contents($socks);
        $explodeSocks = explode("\n", $getSocks);
        echo "Jumlah Socks : " . count($explodeSocks) . "\n";
    }
    sleep(1);
    echo "Masukkan Username IG agar di follow oleh akun Tumbal\nInput : ";
    $target       = trim(fgets(STDIN));
    $decodeTarget = json_decode(file_get_contents("http://api.bahasakomputer.com/instagram/?username=" . $target));
    echo "Username Target : " . $target . " ID : " . $decodeTarget->id . "\n";
    echo "Pilih Perulangan / Jumlah Akun yang ingin dibuat\nInput : ";
    $jumlah = trim(fgets(STDIN));
    echo "Pilih, Ingin Membuat Akun Berjenis Kelamin apa? 1 = wanita,2 = pria, 3 = campuran\nInput : ";
    $tipe = trim(fgets(STDIN));
    if ($tipe == 1) {
        $data        = file_get_contents(__DIR__ . "/Nama/Wanita/wanita.txt");
        $explodeData = explode("\n", $data);
        $directory   = "Profile/Wanita/";
        // if (glob($directory . "*.jpg") !== false){
        //  $filecount = count(glob($directory . "*.jpg"));
        // }
    } elseif ($tipe == 2) {
        $data        = file_get_contents(__DIR__ . "/Nama/Pria/pria.txt");
        $explodeData = explode("\n", $data);
        $directory   = "Profile/Pria/";
        // if (glob($directory . "*.jpg") !== false){
        //  $filecount = count(glob($directory . "*.jpg"));
        // }
    }
    $randomData = 1;
    $mulaiSocks = 0;
    for ($i = 1; $i < $jumlah; $i++) {
        if ($tipe == 3) {
            if ($randomData % 2 == 0) {
                $data        = file_get_contents(__DIR__ . "/Nama/Wanita/wanita.txt");
                $explodeData = explode("\n", $data);
                $directory   = "Profile/Wanita/";
                // if (glob($directory . "*.jpg") != false){
                //   $filecount = count(glob($directory . "*.jpg"));
                // }
            } else {
                $data        = file_get_contents(__DIR__ . "/Nama/Pria/pria.txt");
                $explodeData = explode("\n", $data);
                $directory   = "Profile/Pria/";
                // if (glob($directory . "*.jpg") != false){
                //   $filecount = count(glob($directory . "*.jpg"));
                // }
            }
        }
        if ($pakaiSock == 1) {
            $socksData = $explodeSocks[$mulaiSocks];
        }
        
        $domain     = array(
            '@gmail.com',
            '@yahoo.com',
            '@mail.com',
            '@yandex.com',
            '@gmx.de',
            '@t-online.de',
            '@yahoo.co.id',
            '@yahoo.co.uk'
        );
        $randDomain = rand(0, count($domain) - 1);
        $randBio    = rand(0, count($explodeData) - 1);
        $randBio2   = rand(0, count($explodeData) - 1);
        $first      = strtolower(preg_replace('/\s/', '', $explodeData[$randBio]));
        $last       = strtolower(preg_replace('/\s/', '', $explodeData[$randBio2]));
        $name       = ucwords($first) . ' ' . ucwords($last);
        $angka2     = preg_replace('/\s/', '', angka(2));
        $angka3     = preg_replace('/\s/', '', angka(3)); 
        $email      = strtolower($first) . strtolower($last) . $angka3 . preg_replace('/\s/', '', $domain[$randDomain]);
        $usernameArray = array(
        	$first.$angka2."_",
        	$last.$angka2."_",
        	$first.$last, 
        	$first.$last.rand(0,9), 
        	$first.$last.$angka2, 
        	$first.$last.$angka3,
        	$first.$last."_",
        	$first.$last."__",
        	$first.".".$last,
        	$first."_".$last,        
        );
        $usernameRandom = rand(0, count($usernameArray) - 1);
        //$username   = strtolower($first) . strtolower($last) . $angka;
        $password   = preg_replace('/\s/', '', string(8));
        
        $instagram = new InstagramUpload();
        if ($pakaiSock == 1) {
            $createAccount = $instagram->createAccount($usernameArray[$usernameRandom], $password, $email, $name, $socksData);
        } else {
            var_dump($createAccount = $instagram->createAccount($usernameArray[$usernameRandom], $password, $email, $name));
        }
        $statusAccount = $createAccount[0];
        
        if ($statusAccount['status'] == 'ok') {
        	if($statusAccount['error_type'] == 'username_is_taken'){
        		 echo "USERNAME TAKEN! | " . $email . "|" . $usernameArray[$usernameRandom] . "|" . $password . "\n";
        	}else{
                echo "SUCCESS CREATE | " . $email . "|" . $usernameArray[$usernameRandom] . "|" . $password . "\n";
                $dataInsert = "SUCCESS CREATE|" . $email . "|" . $usernameArray[$usernameRandom] . "|" . $password . "\r\n";
                $fh         = fopen("success.txt", "a");
                fwrite($fh, $dataInsert);
                fclose($fh);
                $globProfile = glob($directory . "*.jpg");
                $globPhoto   = glob("Gambar/*.jpg");
                //var_dump($instagram->followUser($target));
                //var_dump($instagram->profileChange($globProfile[rand(0, count(glob($directory . "*.jpg")) - 1)]));
                //$instagram->UploadPhoto($globPhoto[rand(0, count(glob("Gambar/*.*")) - 1)], "Test Upload Photo");
                
                //$instagram->UploadVideo(glob("Video/*.mp4")[rand(count(glob("Video/*.mp4")))], $globPhoto[rand(0,count(glob("Gambar/*.*")) - 1)], "Test Upload Video"); /* Upload Video! Delay 20 Detik */
                //$instagram->reportUser($target); /* Report Instagram! */
            }
            sleep(rand(20, 22));
            flush();
        } else {
        	if ($pakaiSock == 1) {
            	echo "ERROR! |" . $statusAccount['message'] . "|" . $statusAccount['feedback_message'] . "|" . $socksData . "|" . $usernameArray[$usernameRandom] . "|" . $password . PHP_EOL;
        	}else{
        		echo "ERROR! |" . $statusAccount['message'] . "|" . $statusAccount['feedback_message'] . "|" . $usernameArray[$usernameRandom] . "|" . $password . PHP_EOL;
        	}
            // $dataInsert =  "UNKNOWN ERROR | ".$socksData." | ".$email." | ".$username." | ".$password."\r\n";
            // $fh = fopen("gagal.txt", "a");
            // fwrite($fh, $dataInsert);
            // fclose($fh);
            $mulaiSocks++;
            sleep(rand(20, 22));
            flush();
        }
        $randomData++;
    }
}else{
	echo "Password Salah!...";
}
?> 