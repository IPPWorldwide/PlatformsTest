<?php

namespace App\Test;
use App\Company;
use App\IPP;
use App\IPPCurrency;
use App\IPPGateway;
use App\IPPLanguages;
use App\IPPMenu;
use App\IPPPartner;
use App\IPPPartnerGraph;
use App\IPPPayments;
use App\IPPPlugins;
use App\IPPRequest;
use App\IPPUtils;
use App\Partner;
use App\Portal;
use App\User;

final class CompanyTest extends \PHPUnit\Framework\TestCase
{
    public function testClassConstructor()
    {
        $request    = new IPPRequest("","");
        $company    = new IPP($request,"","");
        $login = $company->login($_ENV["COMPANY_USERNAME"],$_ENV["COMPANY_PASSWORD"]);
        $this->assertSame(true, $login->success);
        $this->assertSame("Request performed successfully", $login->message);
        $this->assertSame(200, $login->code);
        $user_id = $login->content->user_id;
        $session_id = $login->content->session_id;
        $this->assertNotEmpty($user_id);
        $this->assertNotEmpty($session_id);
        $check_login = $company->CheckLogin();
        $this->assertNotEmpty($check_login->content->id);
    }
    public function testCompanyData()
    {
        $request    = new IPPRequest("","");
        $company    = new IPP($request,"","");
        $login = $company->login($_ENV["COMPANY_USERNAME"],$_ENV["COMPANY_PASSWORD"]);
        $check_login = $company->CheckLogin();
        $this->assertNotEmpty($check_login->content->id);
        $this->assertNotEmpty($check_login->content->name);
        $this->assertNotEmpty($check_login->content->security->key1);
        $this->assertNotEmpty($check_login->content->security->key2);
        $this->assertSame("Request performed successfully", $check_login->message);
        $this->assertSame(200, $check_login->code);
        $this->assertSame(true, $check_login->success);
    }

    public function testVersion()
    {
        $request    = new IPPRequest("","");
        $company    = new IPP($request,"","");
        $version = $company->version();
        $this->assertGreaterThan($_ENV["COMPANY_VERSION"],$version->content->version);
    }

    public function testSecurePayment()
    {
        $request    = new IPPRequest("","");
        $company    = new IPP($request,"","");
        $login = $company->login($_ENV["COMPANY_USERNAME"],$_ENV["COMPANY_PASSWORD"]);
        $check_login = $company->CheckLogin();

        $gateway    = new IPPGateway($check_login->content->id,$check_login->content->security->key2);

//        $currency = ["DKK","SEK","NOK","EUR","USD","GBP","PLN"];
        $currency = ["DKK"];

        foreach($currency as $c_value) {
            $random_amount = rand(500,8000);
            $data   = [];
            $data["currency"] = $c_value;
            $data["amount"] = number_format(str_replace(",",".",$random_amount),2,"","");
            $data["order_id"] = "Testing Order";
            $data["transaction_type"] = "ECOM";

            $data = $gateway->checkout_id($data);

            $data_url = $data->checkout_id;
            $cryptogram = $data->cryptogram;

            $this->assertNotEmpty($data_url);
            $this->assertNotEmpty($cryptogram);

            $request    = new IPPRequest($check_login->content->id,$login->content->session_id);

            $status_code = $request->request("payments/status", [
                "id" => $check_login->content->id,
                "key2" => $check_login->content->security->key2,
                "checkout_id" => $data_url,
            ]);
            $this->assertSame("WAIT", $status_code->content->result);

            $secure_checkout = $request->request("payments/secure", [
                "method" => "card",
                "cipher" => "2020",
                "checkout_id" => $data_url,
                "holder"        => "Test Customer",
                "cardno"        => $_ENV["TESTING_CARDNO"],
                "expmonth"      => date("m", strtotime("+1 month")),
                "expyear"       => date("Y", strtotime("+1 year")),
                "cvv"           => "123"
            ]);

            $this->assertSame("Transaction has been executed successfully.", $secure_checkout->content->transaction_data->z3);

            $status_code = $request->request("payments/status", [
                "id" => $check_login->content->id,
                "key2" => $check_login->content->security->key2,
                "checkout_id" => $data_url,
            ]);
            $this->assertSame("COMPLETED", $status_code->content->result);

            $secure_authorize = $request->request("payments/authorize", [
                "cipher" => "2020",
                "method" => "card",
                "checkout_id" => $data_url,
                "holder"        => "Test Customer",
                "cardno"        => $_ENV["TESTING_CARDNO"],
                "expmonth"      => date("m", strtotime("+1 month")),
                "expyear"       => date("Y", strtotime("+1 year")),
                "cvv"           => "123"
            ]);
            $this->assertSame(true, $secure_authorize->success);
            $this->assertSame(200, $secure_authorize->code);
            $this->assertSame("ACK", $secure_authorize->content->result);
            $this->assertSame("NEW", $secure_authorize->content->status);
            $this->assertSame(false, $secure_authorize->content->consumer->recurring);
            $this->assertSame(1, $secure_authorize->content->consumer->purchases);
            $this->assertSame(0, $secure_authorize->content->risk->score);
            $this->assertSame("SERVE", $secure_authorize->content->risk->suggestion);
            $this->assertSame(number_format(str_replace(",",".",$random_amount),2,"",""), $secure_authorize->content->presentation->amount);
            $this->assertSame("success.php", $secure_authorize->content->success_url);
            $this->assertSame(date("m", strtotime("+1 month")), $secure_authorize->content->card_data->month);
            $this->assertSame(date("Y", strtotime("+1 year")), $secure_authorize->content->card_data->year);

        }



    }
}
