# Changelog

All notable changes to `laravel-redsys-gateway` will be documented in this file

## 2.0.0 - 2021-05-31

- Added support for PSD2 normative in COF/MIT transactions.
- Added new migrations to modify existing tables with new columns for COF transactions and card data.
- Updated FakerRedsysGateway for better tests support.
- Updated to Laravel 8.
- Added support for PHP8.
- Updated tests.


## 1.0.5 - 2020-07-08

- Changed field amount handling. Now it must be a double or float number.
- Added support for _tokenization_ mode. You can request the customer+card+merchant identifier (token) that allow you to make 1-click purchases.
- Minimum PHP version now is 7.4
- Added a `FakeRedsysGateway` to fake Redsys notifications to help in development proccess.
- Improve the migrations with better fields definition.


## 1.0.4 - 2020-07-07

- Added `RedsysNotificationArrived` event to notify when a Redsys notification arrives.
- Added PEST as testing framework.
- Added/improved tests.


## 1.0.3 - 2020-07-06

- Change the Ok, KO base controller name to `RedsysRedirectController.php`.


## 1.0.2 - 2020-07-06

- Small fixes
- Coding style fixes


## 1.0.1 - 2020-07-05

- Small fixes
- Coding style fixes


## 1.0.0 - 2020-07-04

- Initial release
