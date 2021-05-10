Installation
============

Basically, there is enough to execute the command:
```shell
$ composer require --dev aeliot-tm/translation-maintain
```

**NOTE:** Information about configuration see [here](configuration.md)

### Installation with Flex

Bundle will be registered in the file `./config/bundles.php` automatically. 
```php
<?php

return [
    //...
    Aeliot\Bundle\TransMaintain\AeliotTransMaintainBundle::class => ['all' => false, 'dev' => true, 'test' => true, 'prod' => false],
];
```
You should set TRUE for the "dev" and "test" keys and FALSE for others.

After that testing and transformation commands will be available. 

If you want to use some specific [configuration](configuration.md) you should add root node `aeliot_trans_maintain:` to configuration files (create them):
- `./config/packages/dev/aeliot_trans_maintain.yaml`
- `./config/packages/test/aeliot_trans_maintain.yaml`


### Installation for older versions

You should register this bundle in the `./app/AppKernel.php` file.
```php 
<?php

use Symfony\Component\HttpKernel\Kernel;

class AppKernel extends Kernel
{
    //...

    public function registerBundles(): array
    {
        $bundles = [
            //...
        ];

        if (in_array($this->getEnvironment(), ['dev', 'test'])) {
            $bundles[] = new Aeliot\Bundle\TransMaintain\AeliotTransMaintainBundle();
        }

        return $bundles;
    }

    //...
}
```

Add root node `aeliot_trans_maintain:` to the config files:
- `./app/config/config_dev.yml`
- `./app/config/config_test.yml`

See additional information in [configuration](configuration.md) section.

---
*[Read Me](../README.md)*
