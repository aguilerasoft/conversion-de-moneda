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
require_once plugin_dir_path( __FILE__ ) . 'includes/class-conversion-price.php';
// include( ABSPATH . 'wp-admin/admin-header.php' );



//Comprobar que el plugin woocommerce esta activo
register_activation_hook( __FILE__, 'cris_woo_active' );

function cris_woo_active(){

    if ( ! is_plugin_active( 'woocommerce/woocommerce.php' ) 
        and current_user_can( 'activate_plugins' ) ) {
           // Para las máquinas y muestra un error
           wp_die('Uppsss. Este plugin necesita que esté activado el plugin "Woocommerce" por lo que no se puede activar.. <br>
                    <a href="' . admin_url( 'plugins.php' ) . '">&laquo; 
                    Volver a la página de Plugins</a>');
    }
}

// ejemplo de plugin para crear una tabla en WordPress 


function cris_db_conversion() {

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

register_activation_hook( __FILE__, 'cris_db_conversion' );

add_action("admin_menu", "cris_crear_menu");
function cris_crear_menu() {
  add_menu_page('Conversion de Dolares a Bolivres', 'Conversión de moneda', 'manage_options', 'test_menu_slug', 'cris_output_menu');
  add_submenu_page('test_menu_slug', 'Proximamente', 'Proximamente', 'manage_options', 'test_submenu_slug', 'cris_output_submenu');
}


function cris_output_menu() {

  $select_cris_moneda=sanitize_text_field($_POST['moneda']);
  update_post_meta($post->ID, 'moneda', $select_cris_moneda);

  $input_cris_monto=sanitize_text_field($_POST['monto']);
  update_post_meta($post->ID, 'monto', $input_cris_monto);

  $input_cris_id=sanitize_text_field($_POST['dolar_checkbox']);
  update_post_meta($post->ID, 'dolar_checkbox', $input_cris_monto);


    global $wpdb;
    $nombreTabla = $wpdb->prefix . "conversion";
  $wpdb->update( $nombreTabla, 
    // Datos que se remplazarán
    array( 
     
      'moneda' => $select_cris_moneda,
      'monto' => $input_cris_monto,

      
    ),
    // Cuando el ID del campo es igual al número 1
    array( 'ID' => $input_cris_id )
  );

    $registros = $wpdb->get_results( "SELECT monto,moneda FROM $nombreTabla" );
  ?>

      <div class=''><h1>Conversion de moneda</h1>
        <h2>Monto actual en tu pagina: <h2>
        <h1 style='color:blue;'> <?php esc_html_e($registros[0]->moneda ." ". $registros[0]->monto)  ?></h1>

  
        <br>
        <form method="POST">
            <h3>Ingresa monto para la conversión : </h3>
            <select id="moneda" name="moneda">
                <option value="USD">USD</option>
                <option value="VEF">VEF</option>
                <option value="EUR">EUR</option>
                <option value="GXD">GXD</option>
          </select>
            <input id="monto" class="tel" type="text" name="monto" placeholder="Ingresar monto" required />
            <br>
            <input id="dolar_checkbox" type="hidden" name="dolar_checkbox" value="1" />
            <?php submit_button(); ?>
        </form>
</div>

  <?php

  
}


function cris_output_submenu() {
  esc_html_e( '<h1>Proximamente...</h1>', 'text_domain' );


}




