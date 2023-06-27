# epfl-diploma-verification
---

**epfl-diploma-verification** is a WordPress plugin used to verify a student diploma. This plugin calls an IS-Academia API by passing the first name, surname and diploma number of the student to verify it.
PHP is the main languages used to code the plugin.

## Get epfl-diploma-verification
To get the plugin :
- Download **wp-dev** that you will find at the following url: _https://github.com/epfl-si/wp-dev_ ;
- Find the **epfl-diploma-verification** EPFL plugin at the following path: _wp-dev\volumes\wp\6.1.3\wp-content\plugins\epfl-diploma-verification_ ;
- Import **epfl-diploma-verification** in your favorite IDE ;
- ✅

## Required environment
To access to the IS-Academia API, we need to add the kubernet cluster IP address to the authorized hosts.

## epfl-diploma-verification file organization
The files of the plugin are organised as below:

_epfl-diploma-verification.php_ :
Contains the call to the ISA web service and the html code to display according to the call result.

_diploma-verification_form.php_ :
This file contains the form to be displayed to get student information (first name, surname and diploma number).