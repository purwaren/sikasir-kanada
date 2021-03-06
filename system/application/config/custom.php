<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
* Configuration for shop
*/
$config['shop_code'] = "KND";
$config['shop_name'] = "TOKO KANADA";
$config['shop_address'] = "Citra Raya Square1 B01A 9-10, Tangerang";
$config['shop_phone'] = "(021) 596 1444 / (021) 594 06633";

/**
*Screen configuration
* 14 inch => 1024px x 640px=>default
* 17 inch => 1360px x 768px
*/
$config['screen'] = '14';

/**
* jam kerja normal dalam satu hari, sisanya dianggap lembur
*/
$config['work_cycle'] = 8;

/**
 * Port printer
 */
$config['port'] = 20000;

/**
 * Setting open price
 * true -> enabled
 * false ->disabled
 */
$config['open_price'] = true;

/**
 * Setting length of item code
 * default 10digit,
 */
$config['item_length'] = 15;

/**
 * Set timezone
 */
date_default_timezone_set("Asia/Jakarta");

/* End of file doctypes.php */
/* Location: application/config/doctypes.php */