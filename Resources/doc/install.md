Installing CCDNMessage MessageBundle 1.x
========================================

## Dependencies:

1. [PagerFanta](https://github.com/whiteoctober/Pagerfanta).
2. [PagerFantaBundle](http://github.com/whiteoctober/WhiteOctoberPagerfantaBundle).
3. [CCDNComponent BBCodeBundle](https://github.com/codeconsortium/BBCodeBundle/tree/v1.2).
4. [CCDNComponent CrumbTrailBundle](https://github.com/codeconsortium/CrumbTrailBundle/tree/v1.2).
5. [CCDNComponent CommonBundle](https://github.com/codeconsortium/CommonBundle/tree/v1.2).

## Installation:

Installation takes only 4 steps:

1. Download and install dependencies via Composer.
2. Register bundles with AppKernel.php.
3. Update your app/config/routing.yml.
4. Update your database schema.

### Step 1: Download and install dependencies via Composer.

Append the following to end of your applications composer.json file (found in the root of your Symfony2 installation):

``` js
// composer.json
{
    // ...
    "require": {
        // ...
        "codeconsortium/ccdn-message-bundle": "dev-master"
    }
}
```

NOTE: Please replace ``dev-master`` in the snippet above with the latest stable branch, for example ``2.0.*``.

Then, you can install the new dependencies by running Composer's ``update``
command from the directory where your ``composer.json`` file is located:

``` bash
$ php composer.phar update
```

### Step 2: Register bundles with AppKernel.php.

Now, Composer will automatically download all required files, and install them
for you. All that is left to do is to update your ``AppKernel.php`` file, and
register the new bundle:

``` php
// app/AppKernel.php
public function registerBundles()
{
    $bundles = array(
		new CCDNMessage\MessageBundle\CCDNMessageMessageBundle(),
		...
	);
}
```

### Step 3: Update your app/config/routing.yml.

In your app/config/routing.yml add:

``` yml
CCDNMessageMessageBundle:
    resource: "@CCDNMessageMessageBundle/Resources/config/routing.yml"
    prefix: /
```

You can change the route of the standalone route to any route you like, it is included for convenience.

### Step 4: Update your database schema.

Make sure to add the MessageBundle to doctrines mapping configuration:

```
# app/config/config.yml
# Doctrine Configuration
doctrine:
    orm:
        default_entity_manager: default
        auto_generate_proxy_classes: "%kernel.debug%"
        resolve_target_entities:
            Symfony\Component\Security\Core\User\UserInterface: FOS\UserBundle\Entity\User
        entity_managers:
            default:
                mappings:
                    FOSUserBundle: ~
                    CCDNMessageMessageBundle:
                        mapping:              true
                        type:                 yml
                        dir:                  "Resources/config/doctrine"
                        alias:                ~
                        prefix:               CCDNMessage\MessageBundle\Entity
                        is_bundle:            true
```

> FOSUserBundle is noted as an additional example, you can add multiple bundles here. You should however choose a UserBundle of your own and change the user entity that UserInterface will resolve to.

From your projects root Symfony directory on the command line run:

``` bash
$ php app/console doctrine:schema:update --dump-sql
```

Take the SQL that is output and update your database manually.

**Warning:**

> Please take care when updating your database, check the output SQL before applying it.

### Translations

If you wish to use default texts provided in this bundle, you have to make sure you have translator enabled in your config.

``` yaml
# app/config/config.yml

framework:
    translator: ~
```

## Next Steps.

Change the layout template you wish to use for each page by changing the configs under the labelled section 'layout_templates'.


Installation should now be complete!

If you need further help/support, have suggestions or want to contribute please join the community at [Code Consortium](http://www.codeconsortium.com)

- [Return back to the docs index](index.md).
- [Configuration Reference](configuration_reference.md).
