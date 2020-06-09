<?php
/**
 * @package WooCommerce/Functions
 * @version 2.1.0
 */
/*
Plugin Name: Conversión de Moneda
Plugin URI: 
Description: Este plugin permite hacer conversion de moneda en su tienda.
Author: Cristian Aguilera
Version: 1.0
Author URI: 
*/
require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
require_once plugin_dir_path( __FILE__ ) . 'includes/class-bs-price.php';
// include( ABSPATH . 'wp-admin/admin-header.php' );



//Comprobar que el plugin woocommerce esta activo
register_activation_hook( __FILE__, 'comprobar_woocommerce_activo' );

function comprobar_woocommerce_activo(){

    if ( ! is_plugin_active( 'woocommerce/woocommerce.php' ) 
        and current_user_can( 'activate_plugins' ) ) {
           // Para las máquinas y muestra un error
           wp_die('Uppsss. Este plugin necesita que esté activado el plugin "Woocommerce" por lo que no se puede activar.. <br>
                    <a href="' . admin_url( 'plugins.php' ) . '">&laquo; 
                    Volver a la página de Plugins</a>');
    }
}

// ejemplo de plugin para crear una tabla en WordPress 


function db_dolar_price() {

  global $wpdb;
  $nombreTabla = $wpdb->prefix . "conversion";
  
  $created = dbDelta(  
    "CREATE TABLE $nombreTabla (
      ID bigint(20) unsigned NOT NULL AUTO_INCREMENT,
      moneda varchar(4) NOT NULL,
      monto decimal(11,2) NOT NULL,
      UNIQUE(monto),
      PRIMARY KEY (ID)
    ) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_520_ci;"
  );
  
  $wpdb->insert( $nombreTabla, 
  
    array( 
 
      'moneda' => 'USD',
      'monto'=> '00' 
    )
  );

} 

register_activation_hook( __FILE__, 'db_dolar_price' );

add_action("admin_menu", "crear_menu");
function crear_menu() {
  add_menu_page('Conversion de Dolares a Bolivres', 'Conversión de moneda', 'manage_options', 'test_menu_slug', 'output_menu');
  add_submenu_page('test_menu_slug', 'Titular para submenú', 'Proximamente', 'manage_options', 'test_submenu_slug', 'output_submenu');
}


function output_menu() {

    global $wpdb;
    $nombreTabla = $wpdb->prefix . "conversion";
  $wpdb->update( $nombreTabla, 
    // Datos que se remplazarán
    array( 
     
      'moneda' => $_POST['moneda'],
      'monto' => $_POST['monto'],

      
    ),
    // Cuando el ID del campo es igual al número 1
    array( 'ID' => $_POST['dolar_checkbox'] )
  );

    $registros = $wpdb->get_results( "SELECT monto,moneda FROM $nombreTabla" );
  ?>

      <div class=''><h1>Conversion de moneda</h1>
        <h2>Monto actual en tu pagina: <h2>
        <h1 style='color:blue;'> <?php echo $registros[0]->moneda ." ". $registros[0]->monto ?></h1>

  
        <br>
        <form method="POST">
            <h3>Ingresa monto para la conversión : </h3>
            <select name="moneda">
                <option value="USD">USD</option>
                <option value="VEF">VEF</option>
                <option value="EUR">EUR</option>
                <option value="GXD">GXD</option>
          </select>
            <input class="tel" type="text" name="monto" placeholder="Ingresar monto" required />
            <br>
            <input type="hidden" name="dolar_checkbox" value="1" />
            <?php submit_button(); ?>
        </form>
</div>

  <?php

  
}


function output_submenu() {
  echo '<h1>Proximamente...</h1>';


}




