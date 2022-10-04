<?php
namespace App;

class IPPRequest {
    private $user_id;
    private $session_id;
    
    function __construct($user_id,$session_id) {
        $this->user_id = $user_id;
        $this->session_id = $session_id;
    }

    public function download($url, $dest, $fileName) {
        $fr = @fopen($url, 'r');

        $fw = fopen($dest, 'w');
        if ($fw === false) {
            throw new Exception('Writing to file "' . $dest . '" failed');
        }

        $deadline = time() + 5000;

        while(!feof($fr)) {
            $bufferString = fread($fr, 10000);
            fwrite($fw, $bufferString);
            if ($deadline - time() < 10) {
                fclose($fw);
                fclose($fr);
            }
        }
        fclose($fw);
        fclose($fr);
    }

    public function request($url, $data){
        return $this->curl($_ENV["API_URL"]."/".$url, "POST", [], $data);
    }

    public function curl($url, $type = 'POST', $query = [], $data = [], $headers = [],$file=false){

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, "$url?".http_build_query($query, "", "&", PHP_QUERY_RFC3986));
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $type);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

        if($type == "POST") {
            curl_setopt($ch, CURLOPT_POST, 1);
            if($file) {
                curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
            } else
                curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data));
        }
        if (is_array($headers) && sizeof($headers) > 0) {
            curl_setopt($ch, CURLOPT_HEADER, $headers);
            curl_setopt($ch, CURLINFO_HEADER_OUT, true);
        }
        $server_output = curl_exec($ch);
        curl_close($ch);
        $json = json_decode($server_output);
        if (json_last_error() == JSON_ERROR_NONE) {
            return $json;
        }
        return $server_output;
    }
}
