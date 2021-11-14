# Installation

If you are using Pimcore 6 sceleton you need to add the following line to yout `composer.json`

``` json
"minimum-stability": "dev",
```
Pimcore X includes it anyway.

``` bash
composer require spotbot2k/pimcore-learning-management-framework
```
``` bash
php bin/console pimcore:bundle:enable PimcoreLearningManagementFrameworkBundle
```
``` bash
php bin/console pimcore:bundle:install PimcoreLearningManagementFrameworkBundle
```