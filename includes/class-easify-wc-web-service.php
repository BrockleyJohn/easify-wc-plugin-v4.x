<?php

require_once( 'class-easify-generic-web-service.php' );

/**
 * Implementation of the abstract Easify_Generic_Web_Service class to provide
 * the shop functionality for a osCommerce system.
 * 
 * The shop class provides functionality to manipulate products in the online shop,
 * i.e. Add/Update/Delete products.
 * 
 * Because each online shop requires different code, you can subclass the Easify_Generic_Web_Service
 * class as done here in order to provide a shop class that is compatible with your online shop. 
 * 
 */
class Easify_osC_Web_Service extends Easify_Generic_Web_Service {

    /**
     * Factory method to create a osCommerce shop class...
     * 
     * Returns a osCommerce shop class to the superclass.
     */
    public function create_shop() {
        // Create osCommerce shop class...
        return new Easify_osC_Shop($this->easify_server_url, $this->username, $this->password);
    }
}
?>