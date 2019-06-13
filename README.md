# Savellab Plugin Boilerplate

## Requirements

* `nodejs` installed

* `composer` installed
* `PHP 7.0` installed on your local machine

## Setup

1. change **plugin_name** in `/package.hson` file with the plugin folder name you are going to develop

1. change **{Plugin Name}** and **{Plugin Description}** inside of `/bootstrap.php` file with the appropriate information
1. change all occurrences of namespace **Plugin_Scope** with your desire namespace
1. change all occurrences of **Text_Domain** with the plugin's name which should be the same as folder name
1. change Text_Domain.pot file's which should be the same as folder name
1. run `npm install` and `composer install`
1. for compiling:
   * run `npm start` for development

      * **Note:** after the initial build, webpack will continue to watch for changes in any of the resolved files and it will sync the root folder
   * run `npm run prod` for production

1. for testing:
   * run `npm test` for running JS and PHP tests

## Important Notes

* all files you need for uploading on the server should exclude `/node_modules`
* all files inside of `/src/php/autoload` folder which have `class-` prefix will be automatically included
* use `Object-oriented programming (OOP)` while develop the functionality
* use namespace to encapsulate your PHP code (see point 3 of the **Setup**)
* use comments to describe your code
* think modularly, extendable, and optimized as possible
* use Preprocessing instead of writing pure CSS. We like to use SASS(SCSS)

* if you are using `composer` to install PHP dependencies:
   * open `/webpack.config` file and uncomment:
      ```javascript
      // new WebpackSynchronizableShellPlugin({
      //    onBuildStart:{
      //      scripts: [
      //         'composer install --no-dev'
      //       ],
      //      blocking: true,
      //      parallel: false
      //    },
      //    onBuildEnd:{
      //      scripts: [
      //         'composer install'
      //       ],
      //      blocking: true,
      //      parallel: false
      //    }
      // }),
      ```

      ```javascript
      //fs.copySync('vendor/', DIST_DIR+'/'+pluginName+'/vendor');
      ```

   * open `php/includes/class-core.php` file and uncomment:
      ```php
      //include_once PLUGIN_DIR . '/vendor/autoload.php';
      ```
      