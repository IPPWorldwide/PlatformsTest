<?php
namespace App;

class Company {
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
        return $this->curl($_ENV["API_URL"]."/company/login/", $post_data);
    }
    public function data($user_id, $session_id):\stdClass {
        $post_data = ["user_id" => $user_id, "session_id" => $session_id];
        return $this->curl($_ENV["API_URL"]."/company/data/", $post_data);
    }
    public function data_set($user_id, $session_id,$field,$value):\stdClass {
        $post_data = ["user_id" => $user_id, "session_id" => $session_id, "field" => $field, "value" => $value];
        return $this->curl($_ENV["API_URL"]."/company/data/update?", $post_data);
    }
    public function version():\stdClass {
        return $this->curl($_ENV["API_URL"]."/version", []);
    }

    public function checkout_id($username,$password){
        $data = [];
        $data["currency"] = "EUR";
        $data["amount"] = 100;
        $data["order_id"] = "UnitTest";
        $data["transaction_type"] = "ECOM";
        $data["ipn"] = "";
        $data["id"] = $username;
        $data["key2"] = $password;
        $data["origin"] = "LocalTesting";
        return $this->curl($_ENV["API_URL"]."/payments/checkout_id.php", $data);
    }
    public function payment_status($transaction_id,$transaction_key){
        $data = ["transaction_id" => $transaction_id, "transaction_key" => $transaction_key];
        return $this->curl($_ENV["API_URL"]."/payments/status", "POST", [], $data)->content;
    }
}