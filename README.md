ohmgraph
============================

Simple web application built on the Yii 2.0 framework that pulls data from the existing OpenHardwareMonitor and presents it in a live-updating (AJAX for now) graph.  I leave this running on a tablet, so that when I'm rendering or running games, I have relevant data available at a glance.  I've included the meta tags for apple, so if you have an iPad or iPhone, you can add the page to your homescreen and it'll open without tabs or the address bar, in fullscreen.

DIRECTORY STRUCTURE
-------------------

      assets/             contains assets definition
      commands/           contains console commands (controllers)
      config/             contains application configurations
      controllers/        contains Web controller classes
      mail/               contains view files for e-mails
      models/             contains model classes
      runtime/            contains files generated during runtime
      tests/              contains various tests for the basic application
      vendor/             contains dependent 3rd-party packages
      views/              contains view files for the Web application
      web/                contains the entry script and Web resources

REQUIREMENTS
------------
PHP 5.4.0.
Composer

*tested on Ubuntu running Apache, and Windows 10 running XAMPP

INSTALLATION
------------

### After cloning, install dependencies via Composer

If you do not have [Composer](http://getcomposer.org/), you may install it by following the instructions
at [getcomposer.org](http://getcomposer.org/doc/00-intro.md#installation-nix).


Allow composer to manage bower and node requirements:
~~~
php composer.phar global require "fxp/composer-asset-plugin:^1.2.0"
install dependencies:
~~~
php composer.phar install
~~~


CONFIGURATION
-------------


**NOTE:**
- If you have two GPUs, the app may work without editing anything


1. Navigate to https://127.0.0.1:8805, or your computer's local ip on the port that the OpenHardwareMonitor web interface is running.  Default is 8805.  This will give you an idea of the structure of the data being provided.
2. Open controllers/SiteController.php and edit the sensors being collected under the "actionTemps()" function, or use that function as a template and create your own. Make sure to keep the prefix "action" and keep the camelcase naming convention.  In this file, actionTemps() gets called via ajax, while actionChart() is the controller function that gets called when the page loads.
3. Open views/site/chart.php. In this file, you'll find the URL being generated that gets called via ajax (in this case, site/temps.) If you created your own function instead of editing "actionTemps()," you'll need to edit "site/temps" to match your function.  For example, if your function is called "actionFan()," you'll need to replace "site/temps" with "site/fan."  This file also calls the Highchart config "web/js/tempchart.js," and the Highchart theme "web/js/dark-unica2.js.
4. Open and edit the highchart config file found in web/js/tempchart.js, and edit it to suit the sensors data you configured in SiteController.php.  This is also where you can configure the polling interval, which is set to 2000 (2000ms, or 2 seconds) by default.


