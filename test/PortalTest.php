<?php
namespace App\Test;
use App\Portal;
use App\Partner;

if(isset($_ENV["TEST_PORTAL"]) && $_ENV["TEST_PORTAL"] === "1") {
    class PortalTest extends \PHPUnit\Framework\TestCase {
        public function testSetup() {
            $partner    = new Partner();
            $portal     = new Portal();
            $login = $partner->login($_ENV["PARTNER_USERNAME"], $_ENV["PARTNER_PASSWORD"])->content;
            $data = $partner->data($login->user_id,$login->session_id)->content;
            self::assertObjectHasAttribute(
                "key1",
                $data->security
            );
            self::assertObjectHasAttribute(
                "key2",
                $data->security
            );
            $portal->curl("/setup/", [
                "portal_title" => "TestingPortal",
                "administrator_email" => $_ENV["PARTNER_USERNAME"],
                "currency"  => "978",
                "portal_url"    => $_ENV["MERCHANT_URL"],
                "partner_id"    => $data->id,
                "partner_key1"  => $data->security->key1,
                "partner_key2"  => $data->security->key2,
            ]);
            $portal_html = $portal->curl("/", []);
            $this->assertStringContainsString("TestingPortal", $portal_html );
        }
        /**
         * @depends testSetup
         */
        public function testPartnerData() {
            $partner = new Partner();
            $login = $partner->login($_ENV["PARTNER_USERNAME"], $_ENV["PARTNER_PASSWORD"])->content;
            $data = $partner->data($login->user_id,$login->session_id)->content;
            self::assertObjectHasAttribute(
                "name",
                $data
            );
        }
    }
} else {
    class PortalTest extends \PHPUnit\Framework\TestCase
    {
    }
}
