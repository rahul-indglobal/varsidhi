##StoreLocator Extension

**Magento 2.x version compatible**

###Instalation

Download the latest zip file from Magento Marketplace.<br />
Copy content of zip file to *magento_dir/app/code/Ideo/StoreLocator* directory.<br />
After that, run following commands. 
```
php bin/magento setup:upgrade 
php bin/magento setup:static-content:deploy
php bin/magento cache:clean
```

**You need to use your own google maps api key.**<br />
Go to https://developers.google.com/maps/documentation/javascript/get-api-key and get your own key and then insert it in Stores > Configuration > Ideo Extensions > Store Locator > Google Api Key *Frontend Key* and *Backend Key* fields.
Otherwise the map may not work because of Google maps daily limits.

###Uninstall

* remove the folder app/code/Ideo/StoreLocator
* drop table ideo_storelocator_category: `DROP TABLE ideo_storelocator_category;`
* drop table ideo_storelocator_store: `DROP TABLE ideo_storelocator_store;`
* remove the config settings: `DELETE FROM core_config_data WHERE path LIKE 'storelocator/%';`
* remove the module Ideo_StoreLocator from app/etc/config.php
* remove the module Ideo_StoreLocator from table setup_module: `DELETE FROM setup_module WHERE module='Ideo_StoreLocator';`