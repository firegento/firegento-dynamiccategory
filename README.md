FireGento_DynamicCategory
=========================

This extension enables you to dynamically add products to categories based on product attributes.

Facts
-----
- version: 1.0.0
- [extension on GitHub](https://github.com/firegento/firegento-dynamiccategory)

Description
-----------
This extension enables you to dynamically add products to categories based on product attributes.

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
1. Install the extension via Magento Connect with the key shown above or copy all the files into your document root.
2. Clear the cache, logout from the admin panel and then login again.
3. You can now dynamically add products to categories based on attributes.

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
