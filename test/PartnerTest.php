<?php

namespace App\Test;
final class PartnerTest extends \PHPUnit\Framework\TestCase
{
    public function testClassConstructor()
    {
        $request    = new IPPRequest("","");
        $partner    = new IPPPartner($request,"","");
        $login = $partner->login($_ENV["PARTNER_USER"],$_ENV["PARTNER_PASSWORD"]);
        $this->assertSame(true, $login->success);
        $this->assertSame("Request performed successfully", $login->message);
        $this->assertSame(200, $login->code);
        $user_id = $login->content->user_id;
        $session_id = $login->content->session_id;
        $this->assertNotEmpty($user_id);
        $this->assertNotEmpty($session_id);
        $check_login = $partner->CheckLogin();
        $this->assertNotEmpty($check_login->content->id);
    }
    public function testPartnerData()
    {
        $request    = new IPPRequest("","");
        $partner    = new IPPPartner($request,"","");
        $login = $partner->login($_ENV["PARTNER_USER"],$_ENV["PARTNER_PASSWORD"]);
        $check_login = $partner->CheckLogin();
        $this->assertNotEmpty($check_login->content->id);
        $this->assertNotEmpty($check_login->content->name);
        $this->assertSame("Request performed successfully", $check_login->message);
        $this->assertSame(200, $check_login->code);
        $this->assertSame(true, $check_login->success);
    }
}
