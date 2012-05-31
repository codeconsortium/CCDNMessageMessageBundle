Installing CCDNMessage MessageBundle 1.0
========================================

## Dependencies:

1. [PagerFanta](https://github.com/whiteoctober/Pagerfanta).
2. [PagerFantaBundle](http://github.com/whiteoctober/WhiteOctoberPagerfantaBundle).
3. [CCDNComponent BBCodeBundle](https://github.com/codeconsortium/BBCodeBundle).
4. [CCDNComponent CrumbTrailBundle](https://github.com/codeconsortium/CrumbTrailBundle).
5. [CCDNComponent CommonBundle](https://github.com/codeconsortium/CommonBundle).
6. [CCDNComponent DashboardBundle](https://github.com/codeconsortium/DashboardBundle).
7. [CCDNComponent AttachmentBundle](https://github.com/codeconsortium/AttachmentBundle).

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
	git=http://github.com/codeconsortium/CCDNMessage.git
	target=/bundles/CCDNMessage/MessageBundle
	
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
        profile_route: cc_profile_show_by_id
    template:
        engine: twig
        theme: CCDNMessageMessageBundle:Form:fields.html.twig
    folder:
        messages_per_page: 40
		truncate_subject: 50
        layout_templates:
            show: CCDNComponentCommonBundle:Layout:layout_body_left.html.twig
    message:
        layout_templates:
            compose: CCDNComponentCommonBundle:Layout:layout_body_left.html.twig
            show: CCDNComponentCommonBundle:Layout:layout_body_left.html.twig
    quotas:
        max_messages: 10
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

[Return back to the docs index](http://github.com/codeconsortium/CCDNMessageMessageBundle/blob/master/Resources/doc/index.md).
