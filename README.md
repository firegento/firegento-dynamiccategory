FireGento_DynamicCategory
=========================

This extension enables you to dynamically add products to a specific category based on product attributes.

Facts
-----
- Version: 1.0.0
- [extension on GitHub](https://github.com/firegento/firegento-dynamiccategory)


The module adds a new section "Dynamic Category Product Relater" at the tab "Category Products" of categories in the backend.
You can define rules for products to be included in the category.

If a rule should be defined according to a specific attribute, that attribute needs to be enabled for "Use for Promo Rule Conditions" in its attribute configuration.

Description
-----------

The module adds a new section "Dynamic Category Product Relater" at the tab "Category Products" while reading or editing a category into the backend.
You can define rules for products to be included in the category.

![Dynamic Category Products](./docs/images/screenshot-tab-dynamic-products.png)

If a rule should be defined according to a specific attribute, that attribute needs to be enabled for "Use for Promo Rule Conditions" in its attribute configuration (See Catalog > Attributes > YOUR ATTRIBUTE > Edit it).

![Attribute Edit](./docs/images/attribute-rule-promotion.png)


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
(c) 2012-2013 FireGento Team
