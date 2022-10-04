<?php
namespace App;

class Portal {
    private $ENV = null;
    public function curl ($url,$post_data) {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, "http://merchantadmin/" . $url);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($post_data));
        $server_output = curl_exec($ch);
        curl_close($ch);
        var_dump($_ENV["MERCHANT_URL"] . $url);
        var_dump($server_output);
        return $server_output;
    }
}