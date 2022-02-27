# Woocommerce City Selector

Welcome to the Woocommerce City Selector plugin, which is an extension for the [Woocommerce](https://wordpress.org/plugins/woocommerce/) Wordpress plugin. This is not a stand-alone plugin, you'll need Woocommerce (active) for it to run.

- [Version](#version)
- [Description](#description)
- [Impact](#impact)
- [Installation](#installation)
- [Setup](#setup)
- [Usage](#usage)
- [Cities](#cities)
- [Actions](#actions)
- [Filters](#filters)
- [Functions](#functions)
- [Compatibility](#compatibility)
- [Tested on](#tested)
- [Support](#support)
- [Disclaimer](#disclaimer)
- [Credit](#credit)
- [Changelog](#changelog)

<a name="version"></a>
### Version

0.1 - released xx.03.22

<a name="description"></a>
### Description

This plugin allows you to select a city, based on country and province/state in an ACF Field.

![Screenshot Woocommerce City Selector](https://beee4life.github.io/images/screenshot-acf-city-selector.png)

<a name="impact"></a>
### Impact

The plugin adds a database table named `{$wpdb->prefix}cities` upon plugin activation and imports cities from 2 different countries.

<a name="installation"></a>
### Installation

1. Download the [latest release zip file](https://github.com/Beee4life/woocommerce-city-selector/releases/latest).
1. In your WordPress admin, go to Plugins -> Add New
1. Click Upload Plugin
1. Upload the zip file that you just downloaded.
1. Activate the `Woocommerce City Selector` plugin via the plugins page.

If you use a composer file to add any plugins/libraries. Add the following to your composer.json:

```
  "repositories": [
    {
      "type": "composer",
      "url": "https://wpackagist.org"
    }
  ]
```

Then run `composer require "wpackagist-plugin/woocommerce-city-selector"` 

<a name="setup"></a>
### Setup

1. @TODO
2. (optional) Import new cities with help of the included Excel sheet.
3. (optional) Import new cities by csv (available on the website).

<a name="usage"></a>
### Usage

When the field is used by a single field, 3 values are stored in an array: 

```php
array(3) {
  ["countryCode"]=>
  string(2) "NL"
  ["stateCode"]=>
  string(5) "NL-NH"
  ["cityName"]=>
  string(9) "Amsterdam"
}
```

The reason why the state is prefixed (with the country code) in the database is because there can be states/provinces which use the same abbreviation as in another country. You won't notice this, since this value is formatted on return.

The return value gets overridden, so you get 'more return info' and a properly formatted (stateCode). 5 values are returned:
```php
array(5) {
  ["countryCode"]=>
  string(2) "NL"
  ["stateCode"]=>
  string(5) "NH"
  ["cityName"]=>
  string(9) "Amsterdam"
  ["stateName"]=>
  string(13) "Noord-Holland"
  ["countryName"]=>
  string(11) "Netherlands"
}
```

Echo it as follows:

```php
$city_selector = get_field('field_name');
echo 'I live in ' . $city_selector['cityName'];
echo 'which is in ' . city_selector['stateName'] . ' (' . city_selector['stateCode'] . ')'; 
echo ' which lies in the country: ' . $city_selector['country'] . ' (' . $city_selector['countryCode'] . ')';
```

This outputs:

```
"I live in Amsterdam which is in the state Noord-Holland (NH) which lies in the country Netherlands (NL)".
```
        
<a name="cities"></a>
### Cities

The plugin comes with all cities in Belgium and the Netherlands pre-installed.

You can also add more countries yourself, through SQL or CSV import. There's a simple Excel sheet included in the plugin and can be found in the `import` folder. With this sheet, you can easily create an SQL insert statement or a CSV data set.

The explanation on how to do this, can be found on the first tab/sheet of the excel file.

There are a few country packages (csv files) available. These packages can be imported as is. These are available through the [ACFCS website](https://acf-city-selector.com).

<a name="actions"></a>
### Actions

There are a few actions available to add your own custom actions. 

Find all actions [here](https://acf-city-selector.com/documentation/actions/).

<a name="filters"></a>
### Filters

Find all filters [here](https://acf-city-selector.com/documentation/filters/).

<a name="functions"></a>
### Functions

A few custom functions are available for you to easily retrieve data.

Find all functions and their info [here](https://acf-city-selector.com/documentation/functions/).

<a name="compatibility"></a>
### Compatibility

This plugin is compatible/tested with Woocommerce 5.x.

<a name="tested"></a>
### Tested with

* [X] Wordpress 5.9.1
* [X] Woocommerce 6.2.1

<a name="support"></a>
### Support

If you need support, please turn to [Github](https://github.com/Beee4life/woocommerce-city-selector/issues). It's faster than the Wordpress support.

<a name="disclaimer"></a>
### Disclaimer

The plugin works in the following situations: 
* in a single field
* in a repeater field
* in a group
* in a flexible content block
* in an accordion field
* as a cloned field
* on taxonomy terms
* on settings pages

The plugin does NOT work properly yet in the following situations: 
* when multiple instances of the field are used in 1 group/on 1 post

It might have some twitches with taxonomies, but need some more testing.

Sometimes the loading of states/cities, takes a few seconds... Don't know why yet...
This seems to be very random and unpredictable.

<a name="credit"></a>
### Credit

I got the idea for this plugin through [Fabrizio Sabato](https://github.com/fab01) who used it a bit differently, which can ben seen [here](http://www.deskema.it/en/articles/multi-level-country-state-city-cascading-select-wordpress).

[Jarah de Jong](https://github.com/inquota) helped me out with some JS at the start and [John McDonald](https://github.com/mrjohnmc) did some of the German translations.

<a name="changelog"></a>
### Changelog

0.1 
* initial setup
