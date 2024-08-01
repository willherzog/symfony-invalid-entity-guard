# WHInvalidEntityGuardBundle
 A decorator for the Symfony validator service to prevent a Doctrine entity which has failed validation from being persisted in its current state.


## Installation

Make sure Composer is installed globally, as explained in the
[installation chapter](https://getcomposer.org/doc/00-intro.md)
of the Composer documentation.

### Applications that use Symfony Flex

Open a command console, enter your project directory and execute:

```console
$ composer require willherzog/symfony-invalid-entity-guard
```

### Applications that don't use Symfony Flex

#### Step 1: Download the Bundle

Open a command console, enter your project directory and execute the
following command to download the latest stable version of this bundle:

```console
$ composer require willherzog/symfony-invalid-entity-guard
```

#### Step 2: Enable the Bundle

Then, enable the bundle by adding it to the list of registered bundles
in the `config/bundles.php` file of your project:

```php
// config/bundles.php

return [
    // ...
    WHSymfony\WHInvalidEntityGuardBundle\WHInvalidEntityGuardBundle::class => ['all' => true],
];
```

## Configuration

Affects all Doctrine entities by default (in which case no configuration is needed), but individual entity classes can be excluded as follows:

```yaml
# config/packages/wh_invalid_entity_guard.yaml

wh_invalid_entity_guard:
    exclude:
        - App\Entity\MyEntityClassToExclude
```

Additionally, if you need to disable this bundle entirely (e.g. for testing), you can do so as follows:

```yaml
# config/packages/wh_invalid_entity_guard.yaml

wh_invalid_entity_guard:
    enable: false
```