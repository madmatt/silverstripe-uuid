<?php

namespace Madmatt\UUID;

use Exception;
use InvalidArgumentException;
use Ramsey\Uuid\Uuid;
use SilverStripe\Core\Config\Config;
use SilverStripe\Core\Config\Configurable;
use SilverStripe\ORM\FieldType\DBVarchar;

class UUIDField extends DBVarchar
{
    use Configurable;

    private static $uuid_version = '';

    const UUIDV1 = 'uuidv1';
    const UUIDV4 = 'uuidv4';

    const VALID_UUID_VERSIONS = [
        self::UUIDV1,
        self::UUIDV4
    ];

    /**
     * @var int The maximum length of a UUID. All RFC4122 UUIDs are 36 characters long when in hexadecimal string format
     *
     * We could represent this as a BINARY(16) column, but that would be a significant amount of pain for admins - we
     * would need to convert the value into and out of the database, but also it makes searching for values in the
     * database directly much more challenging. Instead, we take the modest width penalty and store the full readable
     * value.
     */
    const UUID_MAX_LENGTH = 36;

    /**
     * @inheritDoc
     * @codeCoverageIgnore
     */
    public function requireField()
    {
        $this->size = self::UUID_MAX_LENGTH;

        // The UUID version must be one of the valid versions
        $version = $this->config()->uuid_version;

        if (!self::is_valid_uuid_version($version)) {
            throw new InvalidArgumentException(sprintf('The given UUID version (%s) is not valid.', $version));
        }

        return parent::requireField();
    }

    public static function generate_uuid()
    {
        $version = Config::inst()->get(self::class, 'uuid_version');

        if (!self::is_valid_uuid_version($version)) {
            throw new InvalidArgumentException(sprintf('The given UUID version (%s) is not valid.', $version));
        }

        switch ($version) {
            case self::UUIDV1:
                return Uuid::uuid1()->toString();

            case self::UUIDV4:
                return Uuid::uuid4()->toString();

            default:
                throw new Exception('You appear to be in the fourth dimension, this code should be unreachable.');
        }
    }

    public static function is_valid_uuid_version($version)
    {
        return in_array($version, self::VALID_UUID_VERSIONS);
    }
}
