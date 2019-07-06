<?php
/***********************************************************************************************
RIVAI GANTENG 123
***********************************************************************************************/
error_reporting(0);
error_reporting(E_ALL ^ (E_NOTICE | E_WARNING));

include_once __DIR__ . "/Class/Instagram.php";
include_once __DIR__ . "/Function/function.php";

echo "=================================== Created By Teuku Muhammad Rivai " . date("Y") . " ======================================\n";
sleep(1);
echo "Ingin Menggunakan Socks (1 = ya)? : ";
$pakaiSock = trim(fgets(STDIN));
if ($pakaiSock == 1) {
    echo "Silahkan Masukkan Nama File Yang Berisi SOCKS Input : ";
    $socks        = trim(fgets(STDIN));
    $getSocks     = file_get_contents($socks);
    $explodeSocks = explode("\n", $getSocks);
    echo "Jumlah Socks : " . count($explodeSocks) . "\n";
}
echo "Masukkan Username IG agar di follow oleh akun Tumbal Input : ";
$target = trim(fgets(STDIN));
echo "Pilih Perulangan / Jumlah Akun yang ingin dibuat Input : ";
$jumlah = trim(fgets(STDIN));
echo "Pilih, Ingin Membuat Akun Berjenis Kelamin apa? 1 = wanita,2 = pria, 3 = campuran : ";
$tipe = trim(fgets(STDIN));
echo "\n\n";
$randomData = 1;
$mulaiSocks = 0;
for ($i = 0; $i < $jumlah; $i++) {
    if($pakaiSock){
        if($explodeSocks[$mulaiSocks] == null){
            echo "socks habis!....\n\n";
            break;
        }
    }
    if ($tipe === "3") {
        if ($randomData % 2 == 0) {
            $data = curlClient("https://api.namefake.com/indonesian-indonesia/female/")[1];
            $explode = explode(" ", $data['name']);
            $directory   = __DIR__."Profile/Wanita/";
        } else {
            $data = curlClient("https://api.namefake.com/indonesian-indonesia/male/")[1];
            $explode = explode(" ", $data['name']);
            $directory   = __DIR__."Profile/Pria/";
        }
    }elseif($tipe === "2"){
        $data = curlClient("https://api.namefake.com/indonesian-indonesia/male/")[1];
        $explode = explode(" ", $data['name']);
        $directory   = __DIR__."Profile/Pria/";
    }elseif($tipe === "1"){
        $data = curlClient("https://api.namefake.com/indonesian-indonesia/female/")[1];
        $explode = explode(" ", $data['name']);
        $directory   = __DIR__."Profile/Wanita/";
    }
    $domain         = array(
        '@gmail.com',
        '@yahoo.com',
        '@mail.com',
        '@yandex.com',
        '@gmx.de',
        '@t-online.de',
        '@yahoo.co.id',
        '@yahoo.co.uk'
    );
    $randDomain     = rand(0, count($domain) - 1);
    $first          = strtolower(preg_replace('/\s/', '', $explode[0]));
    $last           = strtolower(preg_replace('/\s/', '', $explode[1]));
    $angka3        = preg_replace('/\s/', '', angka(3));
    $angka4         = preg_replace('/\s/', '', angka(4));
    $name           = ucwords($first). ' '.ucwords($last);
    $email          = strtolower($first) . strtolower($last) . $angka3 . preg_replace('/\s/', '', $domain[$randDomain]);
    $usernameArray  = array(
        $first . $angka3 . "_",
        $last . $angka4 . "_",
        $first . $last . $angka3,
        $first . $last . $angka4,
        $first . $last . "_",
        $first . $last . "__"
    );
    $usernameRandom = rand(0, count($usernameArray) - 1);
    $password       = preg_replace('/\s/', '', stringPass(8));
    
    $instagram = new InstagramUpload();
    if ($pakaiSock == 1) {
        $instagram->socks = $explodeSocks[$mulaiSocks];
        $createAccount = $instagram->createAccount($usernameArray[$usernameRandom], $password, $email, $name, $socksData);
    } else {
        $createAccount = $instagram->createAccount($usernameArray[$usernameRandom], $password, $email, $name);
    }
    $statusAccount = $createAccount[0];
    if ($statusAccount['status'] == 'ok') {
        if ($statusAccount['error_type'] == 'username_is_taken') {
            echo "USERNAME TAKEN! | " . $email . "|" . $usernameArray[$usernameRandom] . "|" . $password . "\n\n";
        } else {
            if($pakaiSock == 1){
                $loginAccount = $instagram->loginAccount($instagram->socks);
            }else{
                $loginAccount = $instagram->loginAccount();
            }
            $globProfile = glob($directory . "*.jpg");
            $globPhoto   = glob(__DIR__. "/Gambar/*.jpg");
            $followUser = $instagram->followUser($target);
            $followStatus = $followUser === "ok" ? "SUCCESS FOLLOW => ".$target : $followUser;
            $gantiProfile = $instagram->profileChange($directory . $globProfile[rand(0, count(glob($directory . "*.jpg")) - 1)]);
            $profileStatus = $profileStatus === "ok" ? "SUCCESS GANTI PROFILE" : $profileStatus;
            $uploadPhoto = $instagram->UploadPhoto(__DIR__."/Gambar/".$globPhoto[rand(0, count(glob("Gambar/*.*")) - 1)], "Test Upload Photo");
            echo "SUKSES MEMBUAT ACCOUNT! | ".$followStatus." | ".$profileStatus." | ".$uploadPhoto."\n\n";
            
            
            /* VIDEO */
               // $instagram->UploadVideo(glob("Video/*.mp4")[rand(count(glob("Video/*.mp4")))], $globPhoto[rand(0,count(glob("Gambar/*.*")) - 1)], "Test Upload Video"); /* Upload Video! Delay 20 Detik */
               // $instagram->reportUser($target); /* Report Instagram! */
            /* VIDEO */


        }
        sleep(rand(20, 22));
        flush();
    } else {
        if ($pakaiSock == 1) {
            $mulaiSocks++;
            echo "ERROR! |" . $statusAccount['message'] . "|" . $statusAccount['feedback_message'] . "|" . $socksData . "|" . $usernameArray[$usernameRandom] . "|" . $password . "\n\n";
        } else {
            echo "ERROR! |" . $statusAccount['message'] . "|" . $statusAccount['feedback_message'] . "|" . $usernameArray[$usernameRandom] . "|" . $password . "\n\n";
        }
        sleep(rand(20, 22));
        flush();
    }
    $randomData++;
}
?> 
