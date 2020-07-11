<?php

namespace Madmatt\UUID\Tests;

use Madmatt\UUID\UUIDField;
use SilverStripe\Dev\SapphireTest;

class UUIDFieldTest extends SapphireTest
{
    public function testIsValidUuidVersion()
    {
        $this->assertTrue(UUIDField::is_valid_uuid_version('uuidv1'));
        $this->assertTrue(UUIDField::is_valid_uuid_version('uuidv4'));

        $this->assertFalse(UUIDField::is_valid_uuid_version('uuidv2'));
        $this->assertFalse(UUIDField::is_valid_uuid_version('uuidv3'));
        $this->assertFalse(UUIDField::is_valid_uuid_version('uuidv5'));
        $this->assertFalse(UUIDField::is_valid_uuid_version('uuidv6'));
        $this->assertFalse(UUIDField::is_valid_uuid_version('random string'));
        $this->assertFalse(UUIDField::is_valid_uuid_version(''));
    }

    public function testGenerateUuid()
    {
        $this->markTestIncomplete('TODO: Write tests for testGenerateUuid()');
    }
}
