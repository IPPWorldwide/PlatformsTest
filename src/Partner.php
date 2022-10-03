<?php
namespace App;

class Partner {
    private $ENV = null;
    private function curl ($url,$post_data) {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($post_data));
        $server_output = curl_exec($ch);
        curl_close($ch);
        return json_decode($server_output);
    }
    public function login($username, $password):\stdClass {
        $post_data = ["username" => $username, "password" => $password];
        return $this->curl($_ENV["API_URL"]."/partner/login/", $post_data);
    }
    public function data($user_id, $session_id):\stdClass {
        $post_data = ["user_id" => $user_id, "session_id" => $session_id];
        return $this->curl($_ENV["API_URL"]."/partner/data/", $post_data);
    }
}