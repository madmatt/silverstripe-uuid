# silverstripe-uuid

This module provides an easy system for generating, storing and retrieving `DataObject`s by UUID, instead of the auto-incrementing IDs provided in Silverstripe CMS by default.

This module *does not* replace the auto-incrementing ID system, and this is still used as the standard primary key for all tables. It only adds a supplementary UUID as a secondary way of accessing an object. Therefore, it also does not provide the database-level benefits that using UUIDs can provide (e.g. the ability to asynchronously create unique IDs for objects in a multi-master replication setup).

The key reason for using this module is to ensure you don't leak information about your system to potential attackers. You should use UUIDs instead of auto-incrementing IDs in any public-facing context where you are referring to unique identifiers for an object.

For example, a URL to view an individual member's profile might be `/members/view-profile/ae9d059a-88b7-4480-b9fa-07b63e480e9a` instead of `/members/view-profile/273`. This ensures that attackers can't easily guess other IDs by just adding or subtracting numbers, and more importantly ensures that nobody knows how many customers you have etc.

## Requirements
* SilverStripe ^4.0

## Installation
```
composer require madmatt/silverstripe-uuid
```

## Quick Usage
There are two ways to use this module:
* The simple but non-customisable way (adding the `UUIDExtension`)
* The advanced, DIY way (using one or more `UUIDField`s.

Which way you choose depends on how you want to use the module - in particular review the details below carefully to understand what each option means.

### Easy: Add the `UUIDExtension`
The easiest way to use this module is to simply add the `UUIDExtension` to any `DataObject` that you want a UUID to be generated for:

**With YAML:**
```yaml
App\Model\MyDataObject:
  extensions:
    - 'Madmatt\UUID\UUIDExtension'
```

**With PHP:**
```php
<?php
namespace App\Model;

use SilverStripe\ORM\DataObject;
use Madmatt\UUID\UUIDExtension;

class MyDataObject extends DataObject
{
    private static $extensions = [
        UUIDExtension::class
    ];
}
```

Either of the above methods will create a new column on your `MyDataObject` table called 'UUID', and this will be automatically populated whenever the object is saved (including during the first `dev/build` after adding the extension - all existing records will have a UUID generated and stored). The UUID will only be written once, and must never change once it's created (e.g. if you attempt to edit it in code, you will get an `Exception` when writing the object).

You can then use this in the same way that you use any other DB field, for example:

```php
namespace App\Model;

use SilverStripe\Control\Controller;
use SilverStripe\ORM\DataObject;

class MyDataObject extends DataObject
{
    public function Link()
    {
        // Returns: /members/view-profile/ae9d059a-88b7-4480-b9fa-07b63e480e9a
        return Controller::join_links('members', 'view-profile', $this->UUID);
    }
}
```

### Harder: Add one or more `UUIDField` DB fields
Alternatively, you can implement this yourself by creating your own database field of type `Madmatt\UUID\UUIDField`, for example:

```php
<?php
namespace App\Model;

use SilverStripe\ORM\DataObject;
use Madmatt\UUID\UUIDField;

class MyDataObject extends DataObject
{
    private static $db = [
        'MyCustomUUID' => UUIDField::class
    ];
}
```

If you use this method, you will need to take care of generating the UUIDs yourself (e.g. implement your own `onBeforeWrite()` method etc). The module will not ensure that the UUID never changes, so beware about using this as a primary key if you use this method.

## Customisation
By default, this module will generate random UUIDs (using [UUIDv4](https://uuid.ramsey.dev/en/latest/rfc4122/version4.html)), and this is recommended unless you specifically want to encode details such as the date/time that the UUID was generated or the machine that generated it.

You can change this globally (however existing UUIDs that have already been generated will not be re-generated, and changing after some UUIDs are already generated has not been tested).

```yaml
---
Name: uuid-override
After: '#uuid-base-config'
---
Madmatt\UUID\UUIDField:
  uuid_version: uuidv1
```

We don't officially support the 'non-standard UUIDs' such as ordered-time UUIDs and Microsoft's similarly-named GUID format, but if you want to add support for them please feel free to make a pull request. These are supported by the underlying `ramsey/uuid` library, so it's just a case of wiring them up.

You can define the style of UUID you want to use from the following list:
- `uuidv1`: Time-based
- `uuidv4`: Random

Note that custom input into the UUID generation process (e.g. custom node value or clock sequence for UUIDv1) is not supported.

This means that the following aren't supported:
- `uuidv2`: DCE Security (requires a specified 'domain' value)
- `uuidv3`: Name-based (MD5) (requires a namespace type and value)
- `uuidv5`: Name-based (SHA-1) (requires a namespace type and value)

Pull requests are welcome to help resolve this if you need V2, V3 or V5 UUIDs for your purposes.

## License
See [License](LICENSE.md)

## Maintainers
 * [madmatt](https://github.com/madmatt)

## Bugtracker
Bugs are tracked in the issues section of this repository. Before submitting an issue please read over
existing issues to ensure yours is unique.

If the issue does look like a new bug:

 - Create a new issue
 - Describe the steps required to reproduce your issue, and the expected outcome. Unit tests, screenshots
 and screencasts can help here.
 - Describe your environment as detailed as possible: SilverStripe version, Browser, PHP version,
 Operating System, any installed SilverStripe modules.

Please report security issues to the module maintainers directly by emailing signups+uuidsecurity [at] madman.dev. Please don't file security issues in the bugtracker.

## Development and contribution
If you would like to make contributions to the module please raise a pull request. If you'd like to help but aren't sure how, please feel free to open an issue - we'd love to help you!
