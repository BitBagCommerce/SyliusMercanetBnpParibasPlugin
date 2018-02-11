![BitBag](https://bitbag.pl/static/bitbag-logo.png)


# BitBag MercanetBnpParibasPlugin [![Build Status](https://travis-ci.org/BitBagCommerce/SyliusMercanetBnpParibasPlugin.svg?branch=master)](https://travis-ci.org/BitBagCommerce/SyliusMercanetBnpParibasPlugin)

## Overview

This plugin enables using Mercanet BNP Paribas payments in Sylius based stores.

## Support

Do you want us to customize this plugin for your specific needs? Write us an email on mikolaj.krol@bitbag.pl 💻

## Installation
```bash
$ composer require bitbag/mercanet-bnp-paribas-plugin
```
    
Add plugin dependencies to your AppKernel.php file:
```php
public function registerBundles()
{
    return array_merge(parent::registerBundles(), [
        ...
        
        new \BitBag\MercanetBnpParibasPlugin\BitBagMercanetBnpParibasPlugin(),
    ]);
}
```

## Usage

Go to the payment methods in your admin panel. Now you should be able to add new payment method for Mercanet BNP Paribas gateway.

## Testing
```bash
$ wget http://getcomposer.org/composer.phar
$ php composer.phar install
$ yarn install
$ yarn run gulp
$ php bin/console sylius:install --env test
$ php bin/console server:start --env test
$ open http://localhost:8000
$ bin/behat features/*
$ bin/phpspec run
```

## Contribution

Learn more about our contribution workflow on http://docs.sylius.org/en/latest/contributing/.
