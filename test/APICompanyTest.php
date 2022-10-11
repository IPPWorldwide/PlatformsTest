<?php
namespace App\Test;

use App\Company;

class APICompanyTest extends \PHPUnit\Framework\TestCase {
    public function testLogin() {
        $partner = new Company();
        $login = $partner->login($_ENV["COMPANY_USERNAME"], $_ENV["COMPANY_PASSWORD"])->content;
        self::assertObjectHasAttribute(
            "user_id",
            $login
        );
        self::assertObjectHasAttribute(
            "session_id",
            $login
        );
    }
    public function testCompanyData() {
        $partner = new Company();
        $login = $partner->login($_ENV["COMPANY_USERNAME"], $_ENV["COMPANY_PASSWORD"])->content;
        $data = $partner->data($login->user_id,$login->session_id)->content;
        self::assertObjectHasAttribute(
            "name",
            $data
        );
    }

    public function testStartPayment() {
        $partner = new Company();
        $login = $partner->login($_ENV["COMPANY_USERNAME"], $_ENV["COMPANY_PASSWORD"])->content;
        $data = $partner->data($login->user_id,$login->session_id)->content;
        $checkout_id = $partner->checkout_id($data->id,$data->security->key2)->content;
        self::assertObjectHasAttribute(
            "checkout_id",
            $checkout_id
        );
    }
    public function testCompanyAcquirer() {
        $partner = new Company();
        $login = $partner->login($_ENV["COMPANY_USERNAME"], $_ENV["COMPANY_PASSWORD"])->content;
        $data = $partner->data($login->user_id,$login->session_id)->content;
        self::assertGreaterThan(
            "0",
            count((array)$data->acquirers)
        );
    }
    public function testCompanyUpdate() {
        $partner = new Company();
        $login = $partner->login($_ENV["COMPANY_USERNAME"], $_ENV["COMPANY_PASSWORD"])->content;
        $update_data = []; $update_data["company"] = ["name" => "test"];
        $partner->data_set($login->user_id,$login->session_id,"meta",$update_data,"test");
        $data = $partner->data($login->user_id,$login->session_id)->content;
        self::assertEquals(
            "test",
            $data->meta_data->company->name
        );
        $update_data = []; $update_data["company"] = ["name" => "demo"];
        $partner->data_set($login->user_id,$login->session_id,"meta",$update_data,"test");
        $data = $partner->data($login->user_id,$login->session_id)->content;
        self::assertEquals(
            "demo",
            $data->meta_data->company->name
        );
        $update_data = []; $update_data["address"] = ["postal" => "2600"];
        $partner->data_set($login->user_id,$login->session_id,"meta",$update_data,"test");
        $data = $partner->data($login->user_id,$login->session_id)->content;
        self::assertEquals(
            "2600",
            $data->meta_data->address->postal
        );
        $update_data = []; $update_data["address"] = ["postal" => "2620"];
        $partner->data_set($login->user_id,$login->session_id,"meta",$update_data,"test");
        $data = $partner->data($login->user_id,$login->session_id)->content;
        self::assertEquals(
            "2620",
            $data->meta_data->address->postal
        );
    }
    public function testVersion()
    {
        $partner = new Company();
        $version = $partner->version();
        $this->assertGreaterThan($_ENV["COMPANY_VERSION"],$version->content->version);
    }
    public function testSpecificDomainProvideranderssonsdk() {
        $company    = new Company();
        $this->assertSame("bambora",$company->domains_checkout("anderssons.dk"));
    }
    public function testSpecificDomainProvidertildinfisk() {
        $company    = new Company();
        $this->assertSame("quickpay",$company->domains_checkout("tildinfisk.dk"));
    }/*
    public function testSpecificDomainProviderttll() {
        $company    = new Company();
        $this->assertSame("stripe",$company->domains_checkout("ttll.dk"));
    }*/
    public function testSpecificDomainProviderdkuliving() {
        $company    = new Company();
        $this->assertSame("pensopay",$company->domains_checkout("dkuliving.dk"));
    }
    public function testSpecificDomainProviderbykragh() {
        $company    = new Company();
        $this->assertSame("stripe",$company->domains_checkout("bykragh.dk"));
    }
    public function testSpecificDomainProvidernicechairs() {
        $company    = new Company();
        $this->assertSame("quickpay",$company->domains_checkout("nicechairs.dk"));
    }
    public function testSpecificDomainProviderskumbutikken() {
        $company    = new Company();
        $this->assertSame("dibs",$company->domains_checkout("skumbutikken.dk"));
    }
    public function testSpecificDomainProviderokklusionsudstyr() {
        $company    = new Company();
        $this->assertSame("reepay",$company->domains_checkout("okklusionsudstyr.dk"));
    }
}
