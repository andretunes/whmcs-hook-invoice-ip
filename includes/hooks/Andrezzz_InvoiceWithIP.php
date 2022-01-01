<?php
/**
 *	Invoice with Primary/Dedicated IP WHMCS Hook version 1.0
 *
 *	@package     WHMCS
 *	@copyright   Andrezzz
 *	@link        https://www.andrezzz.pt
 *	@author      André Antunes <andreantunes@andrezzz.pt>
 */

if (!defined('WHMCS')) {
    exit(header('Location: https://www.andrezzz.pt'));
}

use WHMCS\Database\Capsule;

add_hook('InvoiceCreation', 1, function($vars) {
    global $_LANG;
    $invoiceItems = Capsule::table('tblinvoiceitems')->where('invoiceid', $vars['invoiceid'])->get();

    foreach ($invoiceItems as $invoiceItem) {
        if ($invoiceItem->relid === 0) return;
        
        $dedicatedIP = Capsule::table('tblhosting')->where('id', $invoiceItem->relid)->value('dedicatedip');
        if ($dedicatedIP === '') return;

        Capsule::table('tblinvoiceitems')->where('id', $invoiceItem->id)->update(array(
            'description' => $invoiceItem->description . "\n" . $_LANG['primaryIP'] . ': ' . $dedicatedIP
        ));
    }
});