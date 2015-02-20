<?php

use Otinfo\Otinfo;

class SimpleTest extends PHPUnit_Framework_TestCase {

    public function testOnline() {
        $server = new Otinfo('underwar.org');

        $this->assertTrue($server->execute());
    }

    public function testOffline() {
        $server = new Otinfo('underwar.orx');

        $this->assertFalse($server->execute());
    }

}
