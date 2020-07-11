<?php

namespace Madmatt\UUID;

use SilverStripe\Forms\FieldList;
use SilverStripe\Forms\ReadonlyField;
use SilverStripe\ORM\DataExtension;
use SilverStripe\ORM\DataObject;
use SilverStripe\ORM\DB;

class UUIDExtension extends DataExtension
{
    private static $db = [
        'UUID' => UUIDField::class
    ];

    private static $indexes = [
        'UUID' => [
            'type' => 'unique'
        ]
    ];

    public function onBeforeWrite()
    {
        if (!$this->owner->UUID) {
            $this->owner->UUID = UUIDField::generate_uuid();
        }
    }

    public function updateCMSFields(FieldList $fields)
    {
        $fields->insertAfter('ID', ReadonlyField::create('UUID', 'Unique ID'));
    }

    public function requireDefaultRecords()
    {
        // Find any records of this class that don't have a UUID already and write() them to generate the UUID
        // (see onBeforeWrite() above)
        $records = $this->owner::get()->filter('UUID', null);

        if ($records) {
            /** @var DataObject $r */
            foreach ($records as $r) {
                $r->write();
                DB::alteration_message(sprintf('Added UUID to %s, ID %d', get_class($r), $r->ID), 'changed');
            }
        }
    }
}
