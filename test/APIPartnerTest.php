<?php
namespace App\Test;

use App\Partner;

class APIPartnerTest extends \PHPUnit\Framework\TestCase {
    public function testLogin() {
        $partner = new Partner();
        $login = $partner->login($_ENV["PARTNER_USERNAME"], $_ENV["PARTNER_PASSWORD"])->content;
        self::assertObjectHasAttribute(
            "user_id",
            $login
        );
        self::assertObjectHasAttribute(
            "session_id",
            $login
        );
    }
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