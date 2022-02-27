# Woocommerce City Selector

Welcome to the Woocommerce City Selector plugin, which is an extension for the [Woocommerce](https://wordpress.org/plugins/woocommerce/) Wordpress plugin. This is not a stand-alone plugin, you'll need Woocommerce (active) for it to run.

- [Version](#version)
- [Description](#description)
- [Impact](#impact)
- [Installation](#installation)
- [Setup](#setup)
- [Cities](#cities)
- [Actions](#actions)
- [Filters](#filters)
- [Functions](#functions)
- [Compatibility](#compatibility)
- [Support](#support)
- [Changelog](#changelog)

<a name="version"></a>
### Version

0.1 - released xx.03.22

<a name="description"></a>
### Description

This plugin allows you to select a city, based on country and province/state on a Woocommerce address field (checkout and account).

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

This plugin has been tested with Wordpress 5.9.1 and Woocommerce 6.2.1.

<a name="support"></a>
### Support

If you need support, please turn to [Github](https://github.com/Beee4life/woocommerce-city-selector/issues). It's faster than the Wordpress support.

<a name="changelog"></a>
### Changelog

0.1 
* initial setup
