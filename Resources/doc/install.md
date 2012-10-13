Installing CCDNMessage MessageBundle 1.2
========================================

## Dependencies:

1. [PagerFanta](https://github.com/whiteoctober/Pagerfanta).
2. [PagerFantaBundle](http://github.com/whiteoctober/WhiteOctoberPagerfantaBundle).
3. [CCDNComponent BBCodeBundle](https://github.com/codeconsortium/BBCodeBundle/tree/v1.2).
4. [CCDNComponent CrumbTrailBundle](https://github.com/codeconsortium/CrumbTrailBundle/tree/v1.2).
5. [CCDNComponent CommonBundle](https://github.com/codeconsortium/CommonBundle/tree/v1.2).
6. [CCDNComponent DashboardBundle](https://github.com/codeconsortium/DashboardBundle/tree/v1.2).
7. [CCDNComponent AttachmentBundle](https://github.com/codeconsortium/AttachmentBundle/tree/v1.2).

## Installation:

Installation takes only 9 steps:

1. Download and install the dependencies.
2. Register bundles with autoload.php.
3. Register bundles with AppKernel.php.  
4. Run vendors install script.
5. Update your app/config/routing.yml. 
6. Update your app/config/config.yml. 
7. Update your database schema.
8. Symlink assets to your public web directory.
9. Warmup the cache.

### Step 1: Download and install the dependencies.

Append the following to end of your deps file (found in the root of your Symfony2 installation):

``` ini
[CCDNMessageMessageBundle]
	git=https://github.com/codeconsortium/CCDNMessageMessageBundle.git
	target=/bundles/CCDNMessage/MessageBundle
    version=v1.2	
```

### Step 2: Register bundles with autoload.php.

Add the following to the registerNamespaces array in the method by appending it to the end of the array.

``` php
// app/autoload.php
$loader->registerNamespaces(array(
    'CCDNMessage'      => __DIR__.'/../vendor/bundles',
	**...**
));
```
### Step 3: Register bundles with AppKernel.php.  

In your AppKernel.php add the following bundles to the registerBundles method array:  

``` php
// app/AppKernel.php
public function registerBundles()
{
    $bundles = array(
		new CCDNMessage\MessageBundle\CCDNMessageBundle(),
		**...**
	);
}
```

### Step 4: Run vendors install script.

From your projects root Symfony directory on the command line run:

``` bash
$ php bin/vendors install
```

### Step 5: Update your app/config/routing.yml. 

In your app/config/routing.yml add:  

``` yml
CCDNMessageMessageBundle:
    resource: "@CCDNMessageMessageBundle/Resources/config/routing.yml"
    prefix:   /
```

### Step 6: Update your app/config/config.yml. 

In your app/config/config.yml add:   

``` yml
#        
# for CCDNMessage MessageBundle      
#
ccdn_message_message:  
    user:
        profile_route: ccdn_user_profile_show_by_id
    template:
        engine: twig
    quotas:
        max_messages: 100

```

**Warning:**

>Set the appropriate layout templates you want under the sections 'layout_templates' and the 
route to a users profile if you are not using the [CCDNUser\ProfileBundle](http://github.com/codeconsortium/CCDNUserProfileBundle). Otherwise use defaults.

### Step 7: Update your database schema.

From your projects root Symfony directory on the command line run:

``` bash
$ php app/console doctrine:schema:update --dump-sql
```

Take the SQL that is output and update your database manually.

**Warning:**

> Please take care when updating your database, check the output SQL before applying it.

### Step 8: Symlink assets to your public web directory.

From your projects root Symfony directory on the command line run:

``` bash
$ php app/console assets:install --symlink web/
```

### Step 9: Warmup the cache.

From your projects root Symfony directory on the command line run:

``` bash
$ php app/console cache:warmup
```

Change the layout template you wish to use for each page by changing the configs under the labelled section 'layout_templates'.

## Next Steps.

Installation should now be complete!

If you need further help/support, have suggestions or want to contribute please join the community at [Code Consortium](http://www.codeconsortium.com)

- [Return back to the docs index](index.md).
- [Configuration Reference](configuration_reference.md).
