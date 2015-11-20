FireGento_DynamicCategory
=========================

DynamicCategory is a **free community module** for Magento which allows you to add automatically products to a category, thanks to a set of rules based on the product attribute.

The interface to set the rules is very similar to the one to create shopping cart rules. See the [**Usage**](#usage) Chapter below to see how to use it.

Facts
-----

- Version: 1.2.2
- [extension on GitHub](https://github.com/firegento/firegento-dynamiccategory)

The module adds a new section "Dynamic Category Product Relater" at the tab "Category Products" of categories in the backend.
You can define rules for products to be included in the category.

If a rule should be defined according to a specific attribute, that attribute needs to be enabled for "Use for Promo Rule Conditions" in its attribute configuration.

Requirements
------------

- PHP >= 5.3.0

Compatibility
-------------
- Magento >= 1.6

Installation Instructions
-------------------------

### Via modman

- Install [modman](https://github.com/colinmollenhour/modman)
- Use the command from your Magento installation folder: `modman clone https://github.com/firegento/firegento-dynamiccategory`
- Please make sure that the setting "Allow Symlinks" in System Configuration under *Developer -> Template Settings* is set to "YES".

### Via composer
- Install [composer](http://getcomposer.org/download/)
- Install [Magento Composer](https://github.com/magento-hackathon/magento-composer-installer)
- Create a composer.json into your project like the following sample:

```json
{
    ...
    "require": {
        "firegento/dynamiccategory":"*"
    },
    "repositories": [
	    {
            "type": "composer",
            "url": "http://packages.firegento.com"
        }
    ],
    "extra":{
        "magento-root-dir": "./"
    }
}
```

- Then from your `composer.json` folder: `php composer.phar install` or `composer install`

### Via modgit

- Install [modgit](https://github.com/jreinke/modgit)
- Use the command from your Magento installation folder: `modgit add -i src/app/:app/ -b develop Firegento_DynamicCategory https://github.com/firegento/firegento-dynamiccategory`
- Please make sure that the setting "Allow Symlinks" in System Configuration under *Developer -> Template Settings* is set to "YES".

### Manually
- You can copy the files from the folders of this repository to the same folders of your installation


### Installation in ALL CASES
1. Clear the cache, logout from the admin panel and then login again.
2. You can now dynamically add products to categories based on attributes.

Uninstallation
--------------
1. Remove all extension files from your Magento installation
2. Run the following sql script in your database:

```sql
DROP TABLE dynamiccategory_rule;
DELETE FROM eav_attribute WHERE attribute_code = 'dynamiccategory';
```

<a name="usage">
Usage
-----

### How to use in category management

The module adds a new section "Dynamic Category Product Relater" at the tab "Category Products" while reading or editing a category into the backend.
You can define rules for products to be included in the category.

![Dynamic Category Products](./docs/images/screenshot-tab-dynamic-products.png)

To define a rule, click on the `+` button and add one or more attributes then fill which value(s) must be valid or not to make the condition TRUE. Then it will apply the product to the current category if conditions are ok. You can set if it must be applied when only one or all conditions are valid.

When you finished to define the rules, save the current category.

### How to setup an attribute to be available for Dynamic Category

If a rule should be defined according to a specific attribute, that attribute needs to be enabled for "Use for Promo Rule Conditions" in its attribute configuration (See Catalog > Attributes > YOUR ATTRIBUTE > Edit it).

![Attribute Edit](./docs/images/attribute-rule-promotion.png)


### Cronjob

By default, there is a cronjob which runs every morning at 2 a.m. to reindex all dynamic categories.

If you want to change this time, please go to *System > Configuration > Advanced > System > Dynamic Category* and 
change the cron expression.

![System Config Cron Expr](./docs/images/system-config-cron-expr.png)


Support
-------
If you have any issues with this extension, open an issue on [GitHub](https://github.com/firegento/firegento-customer/issues).

Contribution
------------
Any contribution is highly appreciated. The best way to contribute code is to open a [pull request on GitHub](https://help.github.com/articles/using-pull-requests).

Developer
---------
FireGento Team
* Website: [http://firegento.com](http://firegento.com)
* Twitter: [@firegento](https://twitter.com/firegento)

License
-------
[GNU General Public License, version 3 (GPLv3)](http://opensource.org/licenses/gpl-3.0)

Copyright
---------
(c) 2012-2015 FireGento Team
