<?php
class Debug{
  public $username = 'rvvrivai';
  public $password = 'huskargg69';
  public $csrftoken;
  public $phone_id;
  public $guid;
  public $uid;
  public $device_id;
  public $cookies;
  public $curl;

  public $api_url = 'https://i.instagram.com/api/v1';
  public $ig_sig_key = '5ad7d6f013666cc93c88fc8af940348bd067b68f0dce3c85122a923f4f74b251';

  public $sig_key_version = '4';
  public $x_ig_capabilities = '3ToAAA==';
  public $android_version = 18;
  public $android_release = '4.3';
  public $android_manufacturer = "Huawei";
  public $android_model = "EVA-L19";
  public $headers = array();
  public $socks;
  public $token;
  public $user_agent = 'Instagram 10.3.2 Android (18/4.3; 320dpi; 720x1280; Huawei; HWEVA; EVA-L19; qcom; en_US)';

  	public function GetToken(){
    $strUrl = $this->api_url."/si/fetch_headers/?challenge_type=signup";

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL,$strUrl);
    curl_setopt($ch, CURLOPT_HEADER, true);
    curl_setopt($ch, CURLOPT_POST, false);
    curl_setopt($ch, CURLOPT_HTTPHEADER, $this->headers);
    curl_setopt($ch, CURLOPT_USERAGENT, $this->user_agent);
    if($this->socks != 0){
      curl_setopt($ch, CURLOPT_HTTPPROXYTUNNEL, true); 
      curl_setopt($ch, CURLOPT_PROXYTYPE, CURLPROXY_SOCKS4); 
      curl_setopt($ch, CURLOPT_PROXY, $this->socks);
    }
    curl_setopt($ch, CURLOPT_RETURNTRANSFER,true);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    $result = curl_exec($ch);
    curl_close ($ch);

    preg_match_all("|csrftoken=(.*);|U",$result,$arrOut, PREG_PATTERN_ORDER);
    $csrftoken = $arrOut[1][0];

    if($csrftoken != ""){
      return $csrftoken;
    }else{
      print $result;
    }
  }

  public function generate_useragent($sign_version = '10.8.0'){
    $resolusi = array('1080x1776','1080x1920','720x1280', '320x480', '480x800', '1024x768', '1280x720', '768x1024', '480x320');
    $versi = array('GT-N7000', 'SM-N9000', 'GT-I9220', 'GT-I9100');
    $dpi = array('120', '160', '320', '240');
    $ver = $versi[array_rand($versi)];
    return 'Instagram '.$sign_version.' Android ('.mt_rand(10,11).'/'.mt_rand(1,3).'.'.mt_rand(3,5).'.'.mt_rand(0,5).'; '.$dpi[array_rand($dpi)].'; '.$resolusi[array_rand($resolusi)].'; samsung; '.$ver.'; '.$ver.'; smdkc210; en_US)';
  }

	public function GetLoginUidAndCookie(){
    $arrPostData = array();
    $arrPostData['login_attempt_count'] = "0";
    $arrPostData['_csrftoken'] = $this->GetToken();
    $arrPostData['phone_id'] = $this->generateUUID();
    $arrPostData['guid'] = $this->generateUUID();
    $arrPostData['device_id'] = $this->generateDeviceId();
    $arrPostData['username'] = 'rzdzdsas';
    $arrPostData['password'] = 'huskasrgg71';

    $strUrl = $this->api_url."/accounts/login/";

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL,$strUrl);
    curl_setopt($ch, CURLOPT_HEADER, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, $this->headers);
    curl_setopt($ch, CURLOPT_USERAGENT, $this->user_agent);
    if($this->socks != 0){
      curl_setopt($ch, CURLOPT_HTTPPROXYTUNNEL, true); 
      curl_setopt($ch, CURLOPT_PROXYTYPE, CURLPROXY_SOCKS4); 
      curl_setopt($ch, CURLOPT_PROXY, $this->socks);
    }
    curl_setopt($ch, CURLOPT_POSTFIELDS, $this->generateSignature(json_encode($arrPostData)));
    curl_setopt($ch, CURLOPT_RETURNTRANSFER,true);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
    $result = curl_exec($ch);
    curl_close ($ch);

    list($header, $body) = explode("\r\n\r\n", $result, 2);

    preg_match_all('/^Set-Cookie:\s*([^;]*)/mi', $header, $matches);
    $cookies = implode(";", $matches[1]);

    $arrResult = json_decode($body, true);

    if($arrResult['status'] == "ok"){
      $uid = $arrResult['logged_in_user']['pk'];
      return $matches;
      //return array($uid, $cookies);
    }else{
      print $body;
    }
  }

  public function generateSignature($data){
      $hash = hash_hmac('sha256', $data, $this->ig_sig_key);

      return 'ig_sig_key_version='.$this->sig_key_version.'&signed_body='.$hash.'.'.urlencode($data);
  }

  public function generateUploadId(){
      return number_format(round(microtime(true) * 1000), 0, '', '');
  }
  public function generateUUID(){
      $uuid = sprintf(
          '%04x%04x-%04x-%04x-%04x-%04x%04x%04x',
          mt_rand(0, 0xffff),
          mt_rand(0, 0xffff),
          mt_rand(0, 0xffff),
          mt_rand(0, 0x0fff) | 0x4000,
          mt_rand(0, 0x3fff) | 0x8000,
          mt_rand(0, 0xffff),
          mt_rand(0, 0xffff),
          mt_rand(0, 0xffff)
      );

      return $uuid;
  }

  public function request($endpoint, $post = null) {

   $ch = curl_init();
   curl_setopt($ch, CURLOPT_URL, $this->api_url . '/'. $endpoint);
   curl_setopt($ch, CURLOPT_USERAGENT, $this->user_agent);
   curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
   curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
   curl_setopt($ch, CURLOPT_HEADER, true);

   if ($post) {

   curl_setopt($ch, CURLOPT_POST, true);
   curl_setopt($ch, CURLOPT_POSTFIELDS, $post);

   }

   $resp       = curl_exec($ch);
   $header_len = curl_getinfo($ch, CURLINFO_HEADER_SIZE);
   $header     = substr($resp, 0, $header_len);
   $body       = substr($resp, $header_len);
   curl_close($ch);
if(preg_match('/{"status": "fail"/i', $body)){
    echo "Failed coeg";
}elseif(preg_match('/taken/i', $body)){
echo "username dah ada yang punya beb";
}

   return array($header, json_decode($body, true));

  }
  
  public function instagram($ighost, $useragent, $url, $cookie = 0, $data = 0, $socks = 0, $httpheader = array()){
  $url = $ighost ? 'https://i.instagram.com/api/v1/' . $url : $url;
  $this->curl = curl_init($url);
  curl_setopt($this->curl, CURLOPT_USERAGENT, $useragent);
  curl_setopt($this->curl, CURLOPT_RETURNTRANSFER, 1);
  curl_setopt($this->curl, CURLOPT_SSL_VERIFYPEER, 0);
  curl_setopt($this->curl, CURLOPT_FOLLOWLOCATION, 1);
  curl_setopt($this->curl, CURLOPT_TIMEOUT, 20);
  if($socks) :
    curl_setopt ($this->ch, CURLOPT_HTTPPROXYTUNNEL, true); 
    curl_setopt ($this->ch, CURLOPT_PROXYTYPE, CURLPROXY_SOCKS4); 
    curl_setopt ($this->ch, CURLOPT_PROXY, $this->socks);
  endif;
  if($httpheader) curl_setopt($this->curl, CURLOPT_HTTPHEADER, $httpheader);
  curl_setopt($this->curl, CURLOPT_HEADER, 1);
  if($cookie) curl_setopt($this->curl, CURLOPT_COOKIE, $cookie);
  if ($data):
    curl_setopt($this->curl, CURLOPT_POST, 1);
    curl_setopt($this->curl, CURLOPT_POSTFIELDS, $data);
  endif;
  $response = curl_exec($this->curl);
  $httpcode = curl_getinfo($this->curl);
  if(!$httpcode) return false; else{
    $header = substr($response, 0, curl_getinfo($this->curl, CURLINFO_HEADER_SIZE));
    $body = substr($response, curl_getinfo($this->curl, CURLINFO_HEADER_SIZE));
    curl_close($this->curl);
    return array($header, $body);
  }
}

  private function generateDeviceId(){
      return 'android-'.substr(md5(time()), 16);
  }

  public function createAccount(){
  	$explodeData = ['rivka','rivai','revi','ajijing','rizal','furqan','bidir','malek','wawan'];
  	$domain   = array(
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
      $randBio  = rand(0,count($explodeData) - 1);
      $randBio2  = rand(0,count($explodeData) - 1);
      $first    = preg_replace('/\s/', '', $explodeData[$randBio]);
      $last     = preg_replace('/\s/', '', $explodeData[$randBio2]);
      $name     = ucwords($first) . ' ' . ucwords($last);
      $angka    = preg_replace('/\s/', '', angka(3));
      $email    = strtolower($first) . strtolower($last) . $angka . preg_replace('/\s/', '', $domain[$randDomain]);
      $username = strtolower($first) . strtolower($last) . $angka;
      $password = preg_replace('/\s/', '', string(8));
      $data = json_encode(array(
         'phone_id'    => $this->generateUUID(),
          '_csrftoken'  => 'midssing',
          'username'    => $username,
          'first_name'  => $name,
          'guid'        => $this->generateUUID(),
          'device_id'   => 'android-' . str_split(md5(mt_rand(1000, 9999)), 17)[mt_rand(0, 1)],
          'email'       => $email,
          'force_sign_up_code' => '',
          'qs_stamp'    => '',
          'password'    => 'husfasdaaldaask1'
      ));
      $result = $this->request("accounts/create/", $this->generateSignature($data));
      if (isset($result[1]['account_created']) && ($result[1]['account_created'] == true)){
      	 preg_match_all('%Set-Cookie: (.*?);%', $result[0], $match);
         $this->token = $match[1];
         $parseCookie = array('ds_user='.$username,$this->token[5],$this->token[0],$this->token[1],$this->token[2],$this->token[3],$this->token[4]);

         $implode = implode(";", $parseCookie).';';
         $this->cookies = $implode;

  
     }
    return array($result[1],$this->cookies);
  }

  public function followUser(){
    $getFile = file_get_contents("http://api.bahasakomputer.com/instagram/?username=rvvrivai");
    $decode = json_decode($getFile,true);
    $id = $decode['id'];
    $follow = $this->instagram(1, $this->generate_useragent(), "friendships/create/".$id."/", $this->cookies, $this->generateSignature('{"user_id":"'.$id.'"}'));
    $followStatus = json_decode($follow[1],true);
    return $followStatus;
  }
}
// include_once __DIR__ . "/Function/function.php";
// $debug = new Debug();
// for($i = 1; $i <= 20; $i++){
// var_dump($debug->createAccount());
// echo "\n";
// var_dump($debug->followUser());
// sleep(10);
// }



$size = 10;
$p = 0;
$myarray = array();
while($p < $size) {
  $myarray[] = array("number" => $p, "data" => $p, "status" => "A");
  $p++;
}
print_r($myarray);



?>