The main advantage of Easy Module is that you get all the hooks' information
directly from the pages Drupal provides for the API documentation, as well as
this module allows you to expose the generator module as an open service. Easy
Module internally uses a Web crawler to scan the hooks' API of Drupal 6/7 in
order to keep them updated at all times.

Easy Module
provides a block with the module generation form ready to be embedded on any
page you want. The process for generating a module is fairly simple, just
specify the basic data of the module to be generated (name, description,
dependencies, version of Drupal, and the hooks to implement), you can further
specify whether you want to include a sample code for each implemented hook;
Easy Module will use this information to generate a compressed package (. zip)
of our module's files (.info, .README, .install and .module).

Moreover, Easy Module can also be easily used to study the API of the Drupal 6/7
hooks, by looking in the table the desired hook and clicking the link sample
code located next to each hook, then will appears an dialog with the sample code
for the specified hook.

After activating this module, it will appear the item Module > Create new Module
in the administration-menu (URL: admin/modules/easy-module/create). For
regenerating the hooks' data (by scanning the Drupal's API), go to the
administration form of this module located under Cofiguration > Development >
EasyModule (URL: admin/config/development/easy-module).

For seeing Easy Module in action go to:
http://zonesoftware.co/es/tool/easy_module