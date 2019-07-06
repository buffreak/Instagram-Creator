<?php
/*****************************************************************
Author : Teuku Muhammad Rivai (BuffFreak)
Rewrite Code From : https://github.com/mgp25/Instagram-API
*****************************************************************/
class InstagramUpload {
    private $username;
    private $password;
    private $csrftoken;
    private $phone_id;
    private $guid;
    private $uid;
    private $device_id;
    private $cookies;
    private $curl;
    private $ig_sig_key = 'ac5f26ee05af3e40a81b94b78d762dc8287bcdd8254fe86d0971b2aded8884a4';
    private $api_url = 'https://i.instagram.com/api/v1';
    private $sig_key_version = '4';
    private $x_ig_capabilities = '3ToAAA==';
    private $android_version = 18;
    private $android_release = '4.3';
    private $android_manufacturer = "Huawei";
    private $android_model = "EVA-L19";
    private $headers = array();
    public $socks;
    private $token;
    private $user_agent = "Instagram 10.3.2 Android (18/4.3; 320dpi; 720x1280; Huawei; HWEVA; EVA-L19; qcom; en_US)";
    public function __construct() {
        $this->guid =$this->generateUUID();
        $this->phone_id =$this->generateUUID();
        $this->device_id = $this->generateDeviceId();
        $this->upload_id = $this->generateUploadId();
        $this->headers[] = "X-IG-Capabilities: " . $this->x_ig_capabilities;
        $this->headers[] = "X-IG-Connection-Type: WIFI";
    }
    public function curl($url, $cookie = 0, $data = 0, $useragent, $returntransfer = 0, $timeout = 0, $followlocation = 0, $host = 0, $peer = 0, $customrequest = 0, $encoding = 0, $verbose = 0, $httpheader = array(), $debug = 1) {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_USERAGENT, $useragent);
        if ($httpheader):
            curl_setopt($ch, CURLOPT_HEADER, 1);
            curl_setopt($ch, CURLOPT_HTTPHEADER, $httpheader);
        endif;
        if ($cookie):
            curl_setopt($ch, CURLOPT_COOKIE, $cookie);
        endif;
        if ($data):
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        endif;
        if ($returntransfer):
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, $returntransfer);
        endif;
        if ($followlocation):
            curl_setopt($ch, CURLOPT_FOLLOWLOCATION, $followlocation);
        endif;
        if ($this->socks != 0):
            curl_setopt($ch, CURLOPT_HTTPPROXYTUNNEL, true);
            curl_setopt($ch, CURLOPT_PROXYTYPE, CURLPROXY_SOCKS4);
            curl_setopt($ch, CURLOPT_PROXY, $this->socks);
        endif;
        if ($timeout):
            curl_setopt($ch, CURLOPT_TIMEOUT, $timeout);
        endif;
        if ($peer):
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, $peer);
        else:
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        endif;
        if ($host):
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, $host);
        else:
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        endif;
        if ($encoding):
            curl_setopt($ch, CURLOPT_ENCODING, $encoding);
        endif;
        if ($verbose):
            curl_setopt($ch, CURLOPT_VERBOSE, $verbose);
        endif;
        if ($customrequest):
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $customrequest);
        endif;
        $response = curl_exec($ch);
        if ($debug):
            $header_len = curl_getinfo($ch, CURLINFO_HEADER_SIZE);
            $header = substr($response, 0, $header_len);
            $body = substr($response, $header_len);
            return array($header, $body);
        else:
            return $response;
        endif;
        curl_close($ch);
    }
    public function createAccount($username, $password, $email, $name, $socks = 0) {
        $this->username = $username;
        $this->password = $password;
        $this->socks = $socks;
        $this->csrftoken = $this->GetToken();
        $data = json_encode(
            array('phone_id' => $this->phone_id, '_csrftoken' => $this->csrftoken, 'username' => $this->username, 'first_name' => $name, 'guid' => $this->guid, 'device_id' => 'android-' . str_split(md5(mt_rand(1000, 9999)), 17) [mt_rand(0, 1) ], 'email' => $email, 'force_sign_up_code' => '', 'qs_stamp' => '', 'password' => $this->password));
        $result = $this->curl($this->api_url . "/accounts/create/", 0, $this->generateSignature($data), $this->generate_useragent(), 1, 20, 1, 0, 0, 0, 0, 0, 1, 1);
        $status = json_decode($result[1], true);
        // if (isset($status['account_created']) && ($status['account_created'] == true)) {
        //     preg_match_all('%set-cookie:(.*?);%', $result[0], $match);
        //     $this->cookies = implode(";", $match[1]) . ';';
        // }
        return array($status, $this->cookies);
    }
    public function loginAccount($socks = 0){
        $this->socks = $socks;
        $postq = json_encode([
            'phone_id' => $this->generateUUID(true),
            '_csrftoken' => $this->GetToken(),
            'username' => $this->username,
            'guid' => $this->generateUUID(true),
            'device_id' => $this->generateUUID(true),
            'password' => $this->password,
            'login_attempt_count' => 0
        ]);
        $a = $this->curl($this->api_url . "/accounts/login/", 0, $this->generateSignature($postq), $this->generate_useragent(), 1, 20, 1, 0, 0, 0, 0, 0, 1, 1);
        $header = $a[0];
        $a = json_decode($a[1], true);
	    if($a['status'] == 'ok'){
            preg_match_all('/set-cookie:([^;]+)/',$header,$d);
            $this->cookies = '';
            for($o=0;$o<count($d[0]);$o++){
                $this->cookies .= trim($d[1][$o].";");
            }
            return [$a['status'], $this->cookies];
        }else{
           return $a['status'];
        }
    }
    public function generateUUID($type = true)
    {
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
        return $type ? $uuid : str_replace('-', '', $uuid);
    }
    public function generate_useragent($sign_version = '10.8.0') {
        $resolusi = array('1080x1776', '1080x1920', '720x1280', '320x480', '480x800', '1024x768', '1280x720', '768x1024', '480x320');
        $versi = array('GT-N7000', 'SM-N9000', 'GT-I9220', 'GT-I9100');
        $dpi = array('120', '160', '320', '240');
        $ver = $versi[array_rand($versi) ];
        return 'Instagram ' . $sign_version . ' Android (' . mt_rand(10, 11) . '/' . mt_rand(1, 3) . '.' . mt_rand(3, 5) . '.' . mt_rand(0, 5) . '; ' . $dpi[array_rand($dpi) ] . '; ' . $resolusi[array_rand($resolusi) ] . '; samsung; ' . $ver . '; ' . $ver . '; smdkc210; en_US)';
    }
    public function UploadPhoto($image, $caption) {
        $config = $this->UploadPhotoApi($image);
        $upload = $this->ConfigPhotoApi($caption);
        if($config === true && $upload === true){
            return true;
        }else{
            return $upload." ~ ".$config;
        }
    }
    public function UploadVideo($video, $image, $caption) {
        $this->UploadVideoApi($video);
        $this->UploadPhotoApi($image);
        sleep(20);
        $this->ConfigVideoApi($caption);
    }
    private function GetToken() {
        $strUrl = $this->api_url . "/si/fetch_headers/?challenge_type=signup";
        $result = $this->curl($strUrl, 0, 0, $this->user_agent, 1, 0, 0, 0, 0, 0, 0, 0, $this->headers);
        preg_match_all("|csrftoken=(.*);|U", $result[0], $arrOut, PREG_PATTERN_ORDER);
        $csrftoken = $arrOut[1][0];
        if ($csrftoken != "") {
            return $csrftoken;
        } else {
            return $result;
        }
    }
    public function commentPost($mode = 0, $komen = array()){
        if($mode){
            
        }
    }
    public function directMessage($target,$ua,$cookie,$text){
        $apinya = $this->curl($this->api_url . "/users/".$target."/usernameinfo/", $this->cookies, 0, $this->generate_useragent(), 1, 20, 1, 0, 0, 0, 0, 0, 1, 1)[1];
        $hpr = json_decode($apinya, 1);
        $uid = $hpr['user']['pk'];
        $boundary = $this->generateUUID(true);
        $bodies = array(
            array(
                'type' => 'form-data',
                'name' => 'recipient_users',
                'data' => "[[$uid]]",
            ),
            array(
                'type' => 'form-data',
                'name' => 'client_context',
                'data' => $this->generateUUID(true),
            ),
            array(
                'type' => 'form-data',
                'name' => 'thread_ids',
                'data' => '["0"]',
            ),
            array(
                'type' => 'form-data',
                'name' => 'text',
                'data' => is_null($text) ? '' : $text,
            ),
        );
        $data = $this->buildBody($bodies, $boundary);
        $headers = array(
            'Proxy-Connection: keep-alive',
            'Connection: keep-alive',
            'Accept: */*',
            'Content-Type: multipart/form-data; boundary='.$boundary,
            'Accept-Language: en-en',
        );
        $post = $this->curl($this->api_url.'/direct_v2/threads/broadcast/text/', $this->cookies, $data, $this->generate_useragent(), 1, 0, 0, 0, 0, 0, 0, 0, $headers, 1)[1];   
        return $post;
    }

    public function setBio($bio = array()){
        $getinfo = $this->curl($this->api_url.'/accounts/current_user/?edit=true', $this->cookies, 0, $this->generate_useragent(), 1, 0, 0, 0, 0, 0, 0, 0, $this->headers,1);
        $getinfo = json_decode($getinfo[1], true);

        $post = $this->curl($this->api_url.'/accounts/edit_profile/', $this->cookies ,$this->generateSignature('{"_uid":"'.$this->uid.'","_csrftoken":"'.$this->csrftoken.'","first_name":"'.$getinfo['user']['full_name'].'","is_private":"false","phone_number":"'.$getinfo['user']['phone_number'].'","biography":"'.$bio[array_rand($bio)].'","username":"'.$getinfo['user']['username'].'","gender":"'.$getinfo['user']['gender'].'","email":"'.$getinfo['user']['email'].'","_uuid":"'.$this->upload_id.'","external_url":"'.$getinfo['user']['external_url'].'"}'), $this->generate_useragent(), 1, 0, 0, 0, 0, 0, 0, 0, $this->headers,1)[1];
        return $post;
    }

    private function GetLoginUidAndCookie() {
        $arrPostData = array();
        $arrPostData['login_attempt_count'] = "0";
        $arrPostData['_csrftoken'] = $this->csrftoken;
        $arrPostData['phone_id'] = $this->phone_id;
        $arrPostData['guid'] = $this->guid;
        $arrPostData['device_id'] = $this->device_id;
        $arrPostData['username'] = $this->username;
        $arrPostData['password'] = $this->password;
        $strUrl = $this->api_url . "/accounts/login/";
        $result = $this->curl($strUrl, 0, $this->generateSignature(json_encode($arrPostData)), $this->user_agent, 1, 0, 0, 0, 0, 0, 0, 0, $this->headers);
        preg_match_all('/^set-cookie:\s*([^;]*)/mi', $result[0], $matches);
        $this->cookies = implode(";", $matches[1]).";";
        $arrResult = json_decode($result[1], true);
        if ($arrResult['status'] == "ok") {
            $uid = $arrResult['logged_in_user']['pk'];
            return array($uid, $this->cookies);
        } else {
            return $body;
        }
    }
    public function buildBody($bodies, $boundary) {
        $body = '';
        foreach ($bodies as $b) {
            $body.= '--' . $boundary . "\r\n";
            $body.= 'Content-Disposition: ' . $b['type'] . '; name="' . $b['name'] . '"';
            if (isset($b['filename'])) {
                $ext = pathinfo($b['filename'], PATHINFO_EXTENSION);
                $body.= '; filename="' . 'pending_media_' . number_format(round(microtime(true) * 1000), 0, '', '') . '.' . $ext . '"';
            }
            if (isset($b['headers']) && is_array($b['headers'])) {
                foreach ($b['headers'] as $header) {
                    $body.= "\r\n" . $header;
                }
            }
            $body.= "\r\n\r\n" . $b['data'] . "\r\n";
        }
        $body.= '--' . $boundary . '--';
        return $body;
    }
    public function profileChange($photo) {
        $uData = json_encode(['_csrftoken' => $this->csrftoken, '_uuid' => $this->upload_id, '_uid' => $this->uid]);
        $endpoint = $this->api_url . '/accounts/change_profile_picture/';
        $boundary =$this->generateUUID();
        $bodies = [['type' => 'form-data', 'name' => 'sig_key_version', 'data' => 4], ['type' => 'form-data', 'name' => 'signed_body', 'data' => $this->generateSignature($uData) ], ['type' => 'form-data', 'name' => 'profile_pic', 'data' => file_get_contents($photo), 'filename' => 'profile_pic', 'headers' => ['Content-type: application/octet-stream', 'Content-Transfer-Encoding: binary']]];
        $data = $this->buildBody($bodies, $boundary);
        $headers = ['Connection: close', 'Accept: */*', 'X-IG-Capabilities: ' . $this->x_ig_capabilities, 'X-IG-Connection-Type: WIFI ', 'X-IG-Connection-Speed: ' . mt_rand(1000, 3700) . 'kbps', 'X-FB-HTTP-Engine: Liger', 'Content-Type: multipart/form-data; boundary=' . $boundary, 'Content-Length: ' . strlen($data), 'Accept-Language: ' . 'en-US', 'X-Forwarded-For: ' . $_SERVER['SERVER_ADDR']];
        $resp = json_decode($this->curl($endpoint, $this->cookies, $data, $this->user_agent, 1, 0, 1, 0, 0, 0, '', 0, $headers)[1], true);
        return $resp['status'];
    }
    public function followUser($username) {
        $getID = json_decode($this->curl($this->api_url . "/users/".$target."/usernameinfo/", $this->cookies, 0, $this->generate_useragent(), 1, 20, 1, 0, 0, 0, 0, 0, 1, 1), true);
        $id = $getID['user']['pk'];
        $follow = json_decode($this->curl($this->api_url . "/friendships/create/" . $id . "/", $this->cookies, $this->generateSignature('{"user_id":"' . $id . '"}'), $this->generate_useragent(), 1, 20, 1, 0, 0, 0, 0, 0, 1, 1)[1], true);
        return $follow['status'];
    }
    public function reportUser($username) {
        $getID = json_decode($this->curl($this->api_url . "/users/".$target."/usernameinfo/", $this->cookies, 0, $this->generate_useragent(), 1, 20, 1, 0, 0, 0, 0, 0, 1, 1), true);
        $id = $getID['user']['pk'];
        $report = $this->curl($this->api_url . "/users/" . $id . "/flag_user/", $this->cookies, $this->generateSignature('{"source_name":"profile","reason_id":1}'), $this->generate_useragent(), 1, 20, 1, 0, 0, 0, 0, 0, 1, 1)[1];
        return $report['status'];
    }
    private function UploadPhotoApi($file) {
        $arrPostData = array();
        $arrPostData['_uuid'] = $this->upload_id;
        $arrPostData['_csrftoken'] = $this->csrftoken;
        $arrPostData['upload_id'] = $this->upload_id;
        $arrPostData['image_compression'] = '{"lib_name":"jt","lib_version":"1.3.0","quality":"100"}';
        $arrPostData['photo'] = curl_file_create(realpath($file));
        $strUrl = $this->api_url . "/upload/photo/";
        $result = $this->curl($strUrl, $this->cookies, $arrPostData, $this->user_agent, 1, 0, 0, 0, 0, 0, 0, 0, $this->headers)[1];
        $arrResult = json_decode($result, true);
        if ($arrResult['status'] == "ok") {
            return true;
        } else {
            return $arrResult['status'];
        }
    }
    private function UploadVideoApi($file) {
        $arrPostData = array();
        $arrPostData['_uuid'] = $this->upload_id;
        $arrPostData['_csrftoken'] = $this->csrftoken;
        $arrPostData['upload_id'] = $this->upload_id;
        $arrPostData['media_type'] = '2';
        $strUrl = $this->api_url . "/upload/video/";
        $result = $this->curl($strUrl, $this->cookies, $arrPostData, $this->user_agent, 1, 0, 0, 0, 0, 0, 0, 0, $this->headers)[1];
        $arrResult = json_decode($result, true);
        $uploadUrl = $arrResult['video_upload_urls'][3]['url'];
        $job = $arrResult['video_upload_urls'][3]['job'];
        $headers = $this->headers;
        $headers[] = "Session-ID: " . $this->upload_id;
        $headers[] = "job: " . $job;
        $headers[] = "Content-Disposition: attachment; filename=\"video.mp4\"";
        $result = $this->curl($uploadUrl, $this->cookies, file_get_contents(realpath($file)), $this->user_agent, 1, 0, 0, 0, 0, 'POST', 0, 0, $headers)[1];
        $arrResult = json_decode($result, true);
        if ($arrResult['status'] == "ok") {
            return true;
        } else {
            return $arrResult['status'];
        }
    }
    private function ConfigPhotoApi($caption) {
        $arrPostData = array();
        $arrPostData['media_folder'] = "Instagram";
        $arrPostData['source_type'] = "4";
        $arrPostData['filter_type'] = "0";
        $arrPostData['_csrftoken'] = $this->csrftoken;
        $arrPostData['_uid'] = $this->uid;
        $arrPostData['_uuid'] = $this->upload_id;
        $arrPostData['upload_id'] = $this->upload_id;
        $arrPostData['caption'] = $caption;
        $arrPostData['device']['manufacturer'] = $this->android_manufacturer;
        $arrPostData['device']['model'] = $this->android_model;
        $arrPostData['device']['android_version'] = $this->android_version;
        $arrPostData['device']['android_release'] = $this->android_release;
        $strUrl = $this->api_url . "/media/configure/";
        $result = $this->curl($strUrl, $this->cookies, $this->generateSignature(json_encode($arrPostData)), $this->user_agent, 1, 0, 0, 0, 0, 0, 0, 0, $this->headers)[1];
        $arrResult = json_decode($result, true);
        if ($arrResult['status'] == "ok") {
            return true;
        } else {
            return $arrResult['status'];
        }
    }
    private function ConfigVideoApi($caption) {
        $arrPostData = array();
        $arrPostData['source_type'] = "3";
        $arrPostData['filter_type'] = "0";
        $arrPostData['poster_frame_index'] = "0";
        $arrPostData['length'] = "0.00";
        $arrPostData['"length":0'] = '"length":0.00';
        $arrPostData['audio_muted'] = "false";
        $arrPostData['video_result'] = "deprecated";
        $arrPostData['_csrftoken'] = $this->csrftoken;
        $arrPostData['_uid'] = $this->uid;
        $arrPostData['_uuid'] = $this->upload_id;
        $arrPostData['upload_id'] = $this->upload_id;
        $arrPostData['caption'] = $caption;
        $arrPostData['device']['manufacturer'] = $this->android_manufacturer;
        $arrPostData['device']['model'] = $this->android_model;
        $arrPostData['device']['android_version'] = $this->android_version;
        $arrPostData['device']['android_release'] = $this->android_release;
        $strUrl = $this->api_url . "/media/configure/?video=1";
        $result = $this->curl($strUrl, $this->cookies, $this->generateSignature(json_encode($arrPostData)), $this->user_agent, 1, 0, 0, 0, 0, 0, 0, 0, $this->headers)[1];
        $arrResult = json_decode($result, true);
        if ($arrResult['status'] == "ok") {
            return true;
        } else {
            return $arrResult['status'];
        }
    }
    private function generateDeviceId() {
        return "android-".substr(md5(time()), 16);
    }
    private function generateSignature($data) {
        $hash = hash_hmac('sha256', $data, $this->ig_sig_key);
        return 'ig_sig_key_version=' . $this->ig_sig_key . '&signed_body=' . $hash . '.' . urlencode($data);
    }
    private function generateUploadId() {
        return number_format(round(microtime(true) * 1000), 0, '', '');
    }
}
?>
