// $Id:

CONTENTS OF THIS FILE
---------------------

 * Introduction
 * Install
 * Database Information

INTRODUCTION
------------

Current Maintainers: Rijas Madurakuzhi (rijas.mk@gmail.com)
Original Author: Rijas Madurakuzhi (rijas.mk@gmail.com)

A Simple and Light Feedback Form. 
  * Customizable Form Title, Field Labels, Submit Button Text. 
  * CAPTCHA option, if module CAPTCHA (https://drupal.org/project/captcha) enabled. 
  * Customizable To email addresses.


INSTALL
-------
See INSTALL file for important instructions.


DATABASE DESCRIPTION
--------------------

TABLE: lite_feedback_optionset
+--------------------+------------------+------+-----+---------+-------+
| Field              | Type             | Null | Key | Default | Extra |
+--------------------+------------------+------+-----+---------+-------+
| option_row         | int(10) unsigned | NO   |     | NULL    |       |
| to_addresses       | tinytext         | YES  |     | NULL    |       |
| form_title         | varchar(255)     | NO   |     | NULL    |       |
| user_name          | varchar(255)     | NO   |     | NULL    |       |
| user_name_desc     | varchar(255)     | YES  |     | NULL    |       |
| user_email         | varchar(255)     | NO   |     | NULL    |       |
| user_email_desc    | varchar(255)     | YES  |     | NULL    |       |
| user_feedback      | varchar(255)     | NO   |     | NULL    |       |
| user_feedback_desc | varchar(255)     | YES  |     | NULL    |       |
| form_submit        | varchar(255)     | NO   |     | NULL    |       |
| form_captcha       | int(10) unsigned | NO   |     | NULL    |       |
+--------------------+------------------+------+-----+---------+-------+

TABLE: lite_feedback_list
+---------------+---------------------+------+-----+---------+----------------+
| Field         | Type                | Null | Key | Default | Extra          |
+---------------+---------------------+------+-----+---------+----------------+
| id            | bigint(20) unsigned | NO   | PRI | NULL    | auto_increment |
| user_name     | varchar(255)        | NO   |     | NULL    |                |
| user_email    | varchar(255)        | NO   |     | NULL    |                |
| user_feedback | longtext            | NO   |     | NULL    |                |
+---------------+---------------------+------+-----+---------+----------------+