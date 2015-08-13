<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

/*

Plugin Name: MetaboxUI

Plugin URI: http://rajilesh.in/metaboxui-for-optiontree

Description: This is for developers who love optiontree

Author: Rajilesh Panoli

Version: 6.03

Author URI: http://rajilesh.in

*/
 define( 'RJ_OT_THEME_MODE', apply_filters( 'rj_ot_theme_mode', false ) );

 define( 'RJ_OT_CHILD_THEME_MODE', apply_filters( 'rj_ot_child_theme_mode', false ) );

	
 if ( false == RJ_OT_THEME_MODE && false == RJ_OT_CHILD_THEME_MODE ) {
        define( 'RJ_OT_DIR', plugin_dir_path( __FILE__ ) );
        define( 'RJ_OT_URL', plugin_dir_url( __FILE__ ) );
      } else {
        if ( true == RJ_OT_CHILD_THEME_MODE ) {
          $path = ltrim( end( @explode( get_stylesheet(), str_replace( '\\', '/', dirname( __FILE__ ) ) ) ), '/' );
          define( 'RJ_OT_DIR', trailingslashit( trailingslashit( get_stylesheet_directory() ) . $path ) );
          define( 'RJ_OT_URL', trailingslashit( trailingslashit( get_stylesheet_directory_uri() ) . $path ) );
        } else {
          $path = ltrim( end( @explode( get_template(), str_replace( '\\', '/', dirname( __FILE__ ) ) ) ), '/' );
          define( 'RJ_OT_DIR', trailingslashit( trailingslashit( get_template_directory() ) . $path ) );
          define( 'RJ_OT_URL', trailingslashit( trailingslashit( get_template_directory_uri() ) . $path ) );
        }
      }


include_once( ABSPATH . 'wp-admin/includes/plugin.php' );	
include_once( 'includes/user_meta_box.php' );		
include_once( 'includes/taxonomy_meta_box.php' );
include_once( 'includes/widget_meta_box.php' );	


if(!is_plugin_active('option-tree/ot-loader.php')){

	deactivate_plugins( plugin_basename( __FILE__ ) );

			wp_die( 'This plugin requires <a href="'.get_bloginfo('url').'/wp-admin/plugin-install.php?tab=search&type=term&s=optiontree">Optiontree</a> plugin' );


			die();

}

add_action( 'admin_init', 'rj_ot_save_settings', 6 );


function rj_ot_admin_styless(){
    global $wp_styles, $post,$pagenow;;
    
    /* execute styles before actions */
    do_action( 'ot_admin_styles_before' );
    
    /* load WP colorpicker */
    wp_enqueue_style( 'wp-color-picker' );
    
    /* load admin styles */
   
    wp_enqueue_style( 'ot-admin-css', OT_URL . 'assets/css/ot-admin.css', false, OT_VERSION );
    if($pagenow=='widgets.php'){
    wp_enqueue_style( 'ot-metabox-admin-css', RJ_OT_URL . 'css/metabox-widget-admin.css', false, OT_VERSION );
     }
    
    /* load the RTL stylesheet */
    $wp_styles->add_data( 'ot-admin-css','rtl', true );
    
    /* Remove styles added by the Easy Digital Downloads plugin */
    if ( isset( $post->post_type ) && $post->post_type == 'post' )
      wp_dequeue_style( 'jquery-ui-css' );

    /**
     * Filter the screen IDs used to dequeue `jquery-ui-css`.
     *
     * @since 2.5.0
     *
     * @param array $screen_ids An array of screen IDs.
     */
    $screen_ids = apply_filters( 'ot_dequeue_jquery_ui_css_screen_ids', array( 
      'toplevel_page_ot-settings', 
      'optiontree_page_ot-documentation', 
      'appearance_page_ot-theme-options' 
    ) );
    
   
    
    /* execute styles after actions */
    do_action( 'ot_admin_styles_after' );

  
}
function rj_ot_admin_scriptss(){
	global $pagenow;
    
   
    
    /* execute scripts before actions */
    do_action( 'ot_admin_scripts_before' );
    
    if ( function_exists( 'wp_enqueue_media' ) ) {
      /* WP 3.5 Media Uploader */
      wp_enqueue_media();
    } else {
      /* Legacy Thickbox */
      add_thickbox();
    }

    /* load jQuery-ui slider */
    wp_enqueue_script( 'jquery-ui-slider' );

    /* load jQuery-ui datepicker */
    wp_enqueue_script( 'jquery-ui-datepicker' );

    /* load WP colorpicker */
    wp_enqueue_script( 'wp-color-picker' );

    /* load Ace Editor for CSS Editing */
    wp_enqueue_script( 'ace-editor', RJ_OT_URL.'js/ace.js', null, '1.1.3' );   

    /* load jQuery UI timepicker addon */
    wp_enqueue_script( 'jquery-ui-timepicker', OT_URL . 'assets/js/vendor/jquery/jquery-ui-timepicker.js', array( 'jquery', 'jquery-ui-slider', 'jquery-ui-datepicker' ), '1.4.3' );

    /* load the post formats */
    if ( OT_META_BOXES == true && OT_POST_FORMATS == true ) {
      wp_enqueue_script( 'ot-postformats', OT_URL . 'assets/js/ot-postformats.js', array( 'jquery' ), '1.0.1' );
    }
    if($pagenow=='widgets.php'){
    /* load all the required scripts */
    wp_enqueue_script( 'ot-admin-js', RJ_OT_URL . 'js/metabox-widget-admin.js', array( 'jquery', 'jquery-ui-tabs', 'jquery-ui-sortable', 'jquery-ui-slider', 'wp-color-picker', 'ace-editor', 'jquery-ui-datepicker', 'jquery-ui-timepicker' ), OT_VERSION );
    }else{
    /* load all the required scripts */
    wp_enqueue_script( 'ot-admin-js', OT_URL . 'assets/js/ot-admin.js', array( 'jquery', 'jquery-ui-tabs', 'jquery-ui-sortable', 'jquery-ui-slider', 'wp-color-picker', 'ace-editor', 'jquery-ui-datepicker', 'jquery-ui-timepicker' ), OT_VERSION );
    }

    /* create localized JS array */
    $localized_array = array( 
      'ajax'                  => admin_url( 'admin-ajax.php' ),
      'upload_text'           => apply_filters( 'ot_upload_text', __( 'Send to OptionTree', 'option-tree' ) ),
      'remove_media_text'     => __( 'Remove Media', 'option-tree' ),
      'reset_agree'           => __( 'Are you sure you want to reset back to the defaults?', 'option-tree' ),
      'remove_no'             => __( 'You can\'t remove this! But you can edit the values.', 'option-tree' ),
      'remove_agree'          => __( 'Are you sure you want to remove this?', 'option-tree' ),
      'activate_layout_agree' => __( 'Are you sure you want to activate this layout?', 'option-tree' ),
      'setting_limit'         => __( 'Sorry, you can\'t have settings three levels deep.', 'option-tree' ),
      'delete'                => __( 'Delete Gallery', 'option-tree' ), 
      'edit'                  => __( 'Edit Gallery', 'option-tree' ), 
      'create'                => __( 'Create Gallery', 'option-tree' ), 
      'confirm'               => __( 'Are you sure you want to delete this Gallery?', 'option-tree' ),
      'date_current'          => __( 'Today', 'option-tree' ),
      'date_time_current'     => __( 'Now', 'option-tree' ),
      'date_close'            => __( 'Close', 'option-tree' ),
      'replace'               => __( 'Featured Image', 'option-tree' ),
      'with'                  => __( 'Image', 'option-tree' )
    );
    
    /* localized script attached to 'option_tree' */
    wp_localize_script( 'ot-admin-js', 'option_tree', $localized_array );
    
    /* execute scripts after actions */
    do_action( 'ot_admin_scripts_after' );

  
}


function rj_ot_save_settings(){
global $pagenow;
	if(isset($_REQUEST['page']) && $_REQUEST['page']=='rj_ot-settings'){

	  rj_ot_admin_scriptss();

	  rj_ot_admin_styless();
     if($pagenow=='widgets.php'){
       
     }else{  
         wp_enqueue_script( 'rj_ot_metabox_script', plugin_dir_url(__FILE__) . 'js/metabox-ui-settings.js' );
     }

	  

}

	  

	



    /* check and verify import settings nonce */

    if ( isset( $_POST['option_tree_settings_nonce'] ) && wp_verify_nonce( $_POST['option_tree_settings_nonce'], 'rj_option_tree_settings_form' ) && isset($_GET['page']) && $_GET['page']=='rj_ot-settings' ) {



      /* settings value */

      $settings = isset( $_POST[ot_settings_id()] ) ? $_POST[ot_settings_id()] : '';

      

      /* validate sections */

      if ( isset( $settings['sections'] ) ) {

        

        /* fix numeric keys since drag & drop will change them */

        $settings['sections'] = array_values( $settings['sections'] );

        

        /* loop through sections */

        foreach( $settings['sections'] as $k => $section ) {

          

          /* remove from array if missing values */

          if ( ( ! isset( $section['title'] ) && ! isset( $section['id'] ) ) || ( '' == $section['title'] && '' == $section['id'] ) ) {

          

            unset( $settings['sections'][$k] );

            

          } else {

            

            /* validate label */

            if ( '' != $section['title'] ) {

            

             $settings['sections'][$k]['title'] = wp_kses_post( $section['title'] );

              

            }

            

            /* missing title set to unfiltered ID */

            if ( ! isset( $section['title'] ) || '' == $section['title'] ) {

              

              $settings['sections'][$k]['title'] = wp_kses_post( $section['id'] );

            

            /* missing ID set to title */ 

            } else if ( ! isset( $section['id'] ) || '' == $section['id'] ) {

              

              $section['id'] = wp_kses_post( $section['title'] );

              

            }

            

            /* sanitize ID once everything has been checked first */

            $settings['sections'][$k]['id'] = ot_sanitize_option_id( wp_kses_post( $section['id'] ) );

            

          }

          

        }

        

        $settings['sections'] = ot_stripslashes( $settings['sections'] );

      

      }

      

      /* validate settings by looping over array as many times as it takes */

      if ( isset( $settings['settings'] ) ) {

        

        $settings['settings'] = ot_validate_settings_array( $settings['settings'] );

        

      }

      

      /* validate contextual_help */

      if ( isset( $settings['contextual_help']['content'] ) ) {

        

        /* fix numeric keys since drag & drop will change them */

        $settings['contextual_help']['content'] = array_values( $settings['contextual_help']['content'] );

        

        /* loop through content */

        foreach( $settings['contextual_help']['content'] as $k => $content ) {

          

          /* remove from array if missing values */

          if ( ( ! isset( $content['title'] ) && ! isset( $content['id'] ) ) || ( '' == $content['title'] && '' == $content['id'] ) ) {

          

            unset( $settings['contextual_help']['content'][$k] );

            

          } else {

            

            /* validate label */

            if ( '' != $content['title'] ) {

            

             $settings['contextual_help']['content'][$k]['title'] = wp_kses_post( $content['title'] );

              

            }

          

            /* missing title set to unfiltered ID */

            if ( ! isset( $content['title'] ) || '' == $content['title'] ) {

              

              $settings['contextual_help']['content'][$k]['title'] = wp_kses_post( $content['id'] );

            

            /* missing ID set to title */ 

            } else if ( ! isset( $content['id'] ) || '' == $content['id'] ) {

              

              $content['id'] = wp_kses_post( $content['title'] );

              

            }

            

            /* sanitize ID once everything has been checked first */

            $settings['contextual_help']['content'][$k]['id'] = ot_sanitize_option_id( wp_kses_post( $content['id'] ) );

            

          }

          

          /* validate textarea description */

          if ( isset( $content['content'] ) ) {

          

            $settings['contextual_help']['content'][$k]['content'] = wp_kses_post( $content['content'] );

            

          }

          

        }

      

      }

      

      /* validate contextual_help sidebar */

      if ( isset( $settings['contextual_help']['sidebar'] ) ) {

      

        $settings['contextual_help']['sidebar'] = wp_kses_post( $settings['contextual_help']['sidebar'] );

        

      }

      

      $settings['contextual_help'] = ot_stripslashes( $settings['contextual_help'] );

      

      /* default message */

      $message = 'failed';

      

      /* is array: save & show success message */

      if ( is_array( $settings ) ) {

        

        /* WPML unregister ID's that have been removed */

        if ( function_exists( 'icl_unregister_string' ) ) {

          

          $current = get_option( ot_settings_id() );

          $options = get_option( ot_options_id() );

          

          if ( isset( $current['settings'] ) ) {

            

            /* Empty ID array */

            $new_ids = array();

            

            /* Build the WPML IDs array */

            foreach( $settings['settings'] as $setting ) {

            

              if ( $setting['id'] ) {

                

                $new_ids[] = $setting['id'];



              }

              

            }

            

            /* Remove missing IDs from WPML */

            foreach( $current['settings'] as $current_setting ) {

              

              if ( ! in_array( $current_setting['id'], $new_ids ) ) {

              

                if ( ! empty( $options[$current_setting['id']] ) && in_array( $current_setting['type'], array( 'list-item', 'slider' ) ) ) {

                  

                  foreach( $options[$current_setting['id']] as $key => $value ) {

          

                    foreach( $value as $ckey => $cvalue ) {

                      

                      ot_wpml_unregister_string( $current_setting['id'] . '_' . $ckey . '_' . $key );

                      

                    }

                  

                  }

                

                } else if ( ! empty( $options[$current_setting['id']] ) && $current_setting['type'] == 'social-icons' ) {

                  

                  foreach( $options[$current_setting['id']] as $key => $value ) {

          

                    foreach( $value as $ckey => $cvalue ) {

                      

                      ot_wpml_unregister_string( $current_setting['id'] . '_' . $ckey . '_' . $key );

                      

                    }

                  

                  }

                  

                } else {

                

                  ot_wpml_unregister_string( $current_setting['id'] );

                  

                }

              

              }

              

            }



          }

          

        }

		

     

       update_option( 'rj'.ot_settings_id(), $settings );

        $message = 'success';

        

      }

      

      /* redirect */

      wp_redirect( add_query_arg( array( 'action' => 'save-settings', 'message' => $message ), $_POST['_wp_http_referer'] ) );

      exit;

      

    }

    

    return false;



  

}







if ( ! function_exists( 'rj_ot_type_theme_options_ui' ) ) {

  

  function rj_ot_type_theme_options_ui() {

    global $blog_id;

    

    echo '

	<h2>Metaboxes UI</h2>

	<form method="post" id="option-tree-settings-form">';

      

      /* form nonce */

      wp_nonce_field( 'rj_option_tree_settings_form', 'option_tree_settings_nonce' );

      

      /* format setting outer wrapper */

      echo '<div class="format-setting type-textblock has-desc">';

        

       

        

        /* get the saved settings */

        $settings = get_option(  'rj'.ot_settings_id() );



        /* wrap settings array */

        echo '<div class="format-setting-inner">';

          

          /* set count to zero */

          $count = 0;

  

          /* loop through each section and its settings */

          echo '<ul class="option-tree-setting-wrap option-tree-sortable" id="option_tree_settings_list" data-name="' . ot_settings_id() . '[settings]">';

          

          if ( isset( $settings['sections'] ) ) {

          

            foreach( $settings['sections'] as $section ) {

              

              /* section */

              echo '<li class="' . ( $count == 0 ? 'ui-state-disabled' : 'ui-state-default' ) . ' list-section">' . ot_sections_view( ot_settings_id() . '[sections]', $count, $section ) . '

			  </li>';

              

              /* increment item count */

              $count++;

              

              /* settings in this section */

              if ( isset( $settings['settings'] ) ) {

                

                foreach( $settings['settings'] as $setting ) {

                  

                  if ( isset( $setting['section'] ) && $setting['section'] == $section['id'] ) {

                    

                    echo '<li class="ui-state-default list-setting">' . ot_settings_view( ot_settings_id() . '[settings]', $count, $setting ) . '</li>';

                    

                    /* increment item count */

                    $count++;

                    

                  }

                  

                }

                

              }



            }

            

          }

          

          echo '</ul>';

          

          /* buttons */

          echo '<a href="javascript:void(0);" class="option-tree-section-add option-tree-ui-button button hug-left">' . __( 'Add Section', 'option-tree' ) . '</a>';

          echo '<a href="javascript:void(0);" class="option-tree-setting-add option-tree-ui-button button">' . __( 'Add Setting', 'option-tree' ) . '</a>';

          echo '<button class="option-tree-ui-button button button-primary right hug-right">' . __( 'Save Changes', 'option-tree' ) . '</button>';

          

          /* sidebar textarea */

          echo '

          <div class="format-setting-label" id="contextual-help-label">

            <h3 class="label">' . __( 'Contextual Help', 'option-tree' ) . '</h3>

          </div>

          <div class="format-settings" id="contextual-help-setting">

            <div class="format-setting type-textarea no-desc">

              <div class="description"><strong>' . __( 'Contextual Help Sidebar', 'option-tree' ) . '</strong>: ' . __( 'If you decide to add contextual help to the Theme Option page, enter the optional "Sidebar" HTML here. This would be an extremely useful place to add links to your themes documentation or support forum. Only after you\'ve added some content below will this display to the user.', 'option-tree' ) . '</div>

              <div class="format-setting-inner">

                <textarea class="textarea" rows="10" cols="40" name="' . ot_settings_id(). '[contextual_help][sidebar]">' . ( isset( $settings['contextual_help']['sidebar'] ) ? esc_html( $settings['contextual_help']['sidebar'] ) : '' ) . '</textarea>

              </div>

            </div>

          </div>';

          

          /* set count to zero */

          $count = 0;

          

          /* loop through each contextual_help content section */

          echo '<ul class="option-tree-setting-wrap option-tree-sortable" id="option_tree_settings_help" data-name="' . ot_settings_id(). '[contextual_help][content]">';

          

          if ( isset( $settings['contextual_help']['content'] ) ) {

          

            foreach( $settings['contextual_help']['content'] as $content ) {

              

              /* content */

              echo '<li class="ui-state-default list-contextual-help">' . ot_contextual_help_view( ot_settings_id() . '[contextual_help][content]',  $count, $content ) . '</li>';

              

              /* increment content count */

              $count++;



            }

            

          }

          

          echo '</ul>';



          echo '<a href="javascript:void(0);" class="option-tree-help-add option-tree-ui-button button hug-left">' . __( 'Add Contextual Help Content', 'option-tree' ) . '</a>';

          echo '<button class="option-tree-ui-button button button-primary right hug-right">' . __( 'Save Changes', 'option-tree' ) . '</button>';

 

        echo '</div>';

        

      echo '</div>';

    

    echo '</form>';

	?>
	
     <form name="seamless-donations-form" action="https://www.paypal.com/cgi-bin/webscr" method="post">
  <div class="seamless-donations-forms-error-message" style="display:none"></div>
  <div id="dgx-donate-container">
    <div id="dgx-donate-form-donation-section" class="dgx-donate-form-section">
      <div id="donation_header">
        <div id="donation_header-error-message" style="display:none" class="seamless-donations-error-message-field"></div>
        <h2>Donation Information</h2>
      </div>
      <div id="header_desc">
        <div id="header_desc-error-message" style="display:none" class="seamless-donations-error-message-field"></div>
        <p>I would like to make a donation in the amount of:</p>
      </div>
      <span id="dgx_donate_giving_level_1000"><span id="dgx_donate_giving_level_1000-error-message" style="display:none" class="seamless-donations-error-message-field"></span>
      <input type="radio" name="_dgx_donate_amount" checked="" value="1000"  onclick="document.querySelector('#anmdna2').value='1000';document.querySelector('#anmdna').value='1000';" data-conceal=".other-donation-level">
      $1,000</span><span id="dgx_donate_giving_level_500" class="horiz"><span id="dgx_donate_giving_level_500-error-message" style="display:none" class="seamless-donations-error-message-field"></span>
      <input type="radio" name="_dgx_donate_amount" value="500"   onclick="document.querySelector('#anmdna').value='500';document.querySelector('#anmdna2').value='500';" data-conceal=".other-donation-level">
      $500</span><span id="dgx_donate_giving_level_100" class="horiz"><span id="dgx_donate_giving_level_100-error-message" style="display:none" class="seamless-donations-error-message-field"></span>
      <input type="radio" name="_dgx_donate_amount" value="100"   onclick="document.querySelector('#anmdna').value='100';document.querySelector('#anmdna2').value='100';" data-conceal=".other-donation-level">
      $100</span><span id="dgx_donate_giving_level_50" class="horiz"><span id="dgx_donate_giving_level_50-error-message" style="display:none" class="seamless-donations-error-message-field"></span>
      <input type="radio" name="_dgx_donate_amount" value="50"  onclick="document.querySelector('#anmdna').value='50';document.querySelector('#anmdna2').value='50';" data-conceal=".other-donation-level">
      $50</span><span id="other_radio_button" class="horiz"><span id="other_radio_button-error-message" style="display:none" class="seamless-donations-error-message-field"></span>
      <input type="radio" name="_dgx_donate_amount" value="OTHER" onclick="document.querySelector('#_dgx_donate_user_amount').style.display='block'" id="dgx-donate-other-radio" data-reveal=".other-donation-level">
      Other</span>
      <div id="_dgx_donate_user_amount" class="aftertext other-donation-level" style="display:none">
        <div id="_dgx_donate_user_amount-error-message" style="display:none" class="seamless-donations-error-message-field"></div>
        Other:
        <input type="text" name="_dgx_donate_user_amount" onkeyup="document.querySelector('#anmdna').value=this.value;document.querySelector('#anmdna2').value=this.value;" value="" size="15" id="_dgx_donate_user_amount" data-validate="currency">
      </div>
      <div>
        <div id="_dgx_donate_repeating">
          <div id="_dgx_donate_repeating-error-message" style="display:none" class="seamless-donations-error-message-field"></div>
          <p>
            <input type="checkbox" name="_dgx_donate_repeating" id="dgx-donate-repeating" onclick="document.querySelector('#donation_astype').value='_xclick-subscriptions'">
            I would like this donation to automatically repeat each month</p>
        </div>
      </div>
    </div>
    <div></div>
    <div id="dgx-donate-form-paypal-hidden-section" class="dgx-donate-form-section" style="display:none">
      <div id="cmd">
        <div id="cmd-error-message" style="display:none" class="seamless-donations-error-message-field"></div>
        <input type="hidden" name="cmd" id="donation_astype" value="_donations">
      </div>
      <div id="business">
        <div id="business-error-message" style="display:none" class="seamless-donations-error-message-field"></div>
        <input type="hidden" name="business" value="rajilesh@outlook.com">
      </div>
      <div id="return">
        <div id="return-error-message" style="display:none" class="seamless-donations-error-message-field"></div>
        <input type="hidden" name="return" value="http://localhost/wordpress/?page_id=28&amp;thanks=1&amp;sessionid=sojguvpni55imjngd251sd5s12">
      </div>
      <div id="first_name">
        <div id="first_name-error-message" style="display:none" class="seamless-donations-error-message-field"></div>
        <input type="hidden" name="first_name" value="Asd">
      </div>
      <div id="last_name">
        <div id="last_name-error-message" style="display:none" class="seamless-donations-error-message-field"></div>
        <input type="hidden" name="last_name" value="asd">
      </div>
      <div id="address1">
        <div id="address1-error-message" style="display:none" class="seamless-donations-error-message-field"></div>
        <input type="hidden" name="address1" value="">
      </div>
      <div id="address2">
        <div id="address2-error-message" style="display:none" class="seamless-donations-error-message-field"></div>
        <input type="hidden" name="address2" value="">
      </div>
      <div id="city">
        <div id="city-error-message" style="display:none" class="seamless-donations-error-message-field"></div>
        <input type="hidden" name="city" value="">
      </div>
      <div id="state">
        <div id="state-error-message" style="display:none" class="seamless-donations-error-message-field"></div>
        <input type="hidden" name="state" value="">
      </div>
      <div id="zip">
        <div id="zip-error-message" style="display:none" class="seamless-donations-error-message-field"></div>
        <input type="hidden" name="zip" value="">
      </div>
      <div id="country">
        <div id="country-error-message" style="display:none" class="seamless-donations-error-message-field"></div>
        <input type="hidden" name="country" value="">
      </div>
      <div id="email">
        <div id="email-error-message" style="display:none" class="seamless-donations-error-message-field"></div>
        <input type="hidden" name="email" value="<?php bloginfo('admin_email') ?>">
      </div>
      <div id="custom">
        <div id="custom-error-message" style="display:none" class="seamless-donations-error-message-field"></div>
        <input type="hidden" name="custom" value="dgxdonate_sojguvpni5_1439312765">
      </div>
      <div id="notify_url">
        <div id="notify_url-error-message" style="display:none" class="seamless-donations-error-message-field"></div>
        <input type="hidden" name="notify_url" value="http://localhost/wordpress/wp-content/plugins/seamless-donations/dgx-donate-paypalstd-ipn.php">
      </div>
      <div id="item_name">
        <div id="item_name-error-message" style="display:none" class="seamless-donations-error-message-field"></div>
        <input type="hidden" name="item_name" value="Donation">
      </div>
      <div id="amount">
        <div id="amount-error-message" style="display:none" class="seamless-donations-error-message-field"></div>
        <input type="hidden" name="amount" id="anmdna2" value="1000">
      </div>
      <div id="quantity">
        <div id="quantity-error-message" style="display:none" class="seamless-donations-error-message-field"></div>
        <input type="hidden" name="quantity" value="1">
      </div>
      <div id="currency_code">
        <div id="currency_code-error-message" style="display:none" class="seamless-donations-error-message-field"></div>
        <input type="hidden" name="currency_code" value="USD">
      </div>
      <div id="no_note">
        <div id="no_note-error-message" style="display:none" class="seamless-donations-error-message-field"></div>
        <input type="hidden" name="no_note" value="1">
      </div>
      <div id="src">
        <div id="src-error-message" style="display:none" class="seamless-donations-error-message-field"></div>
        <input type="hidden" name="src" value="1">
      </div>
      <div id="p3">
        <div id="p3-error-message" style="display:none" class="seamless-donations-error-message-field"></div>
        <input type="hidden" name="p3" value="1">
      </div>
      <div id="t3">
        <div id="t3-error-message" style="display:none" class="seamless-donations-error-message-field"></div>
        <input type="hidden" name="t3" value="M">
      </div>
      <div id="a3">
        <div id="a3-error-message" style="display:none" class="seamless-donations-error-message-field"></div>
        <input type="hidden" name="a3" id="anmdna" value="1000">
      </div>
    </div>
    <div id="dgx-donate-form-payment-section" class="dgx-donate-form-section">
      <div id="dgx-donate-pay-enabled" class="dgx-donate-pay-enabled">
        <div id="dgx-donate-pay-enabled-error-message" style="display:none" class="seamless-donations-error-message-field"></div>
        <input type="image" name="dgx-donate-pay-enabled" value="Donate Now" src="<?php echo RJ_OT_URL; ?>images/paypal_btn_donate_lg.gif">
      </div>
    </div>
  </div>
</form>

	<?php

	

	

	

    

  }

  

}	









if ( ! function_exists( 'ot_sections_view' ) ) {



  function ot_sections_view( $name, $key, $section = array() ) {

	  //if(isset($_GET['page']) and $_GET['page']=='rj_ot-settings'){

	  if($_GET['page'] !='ot-settings'){
	   $default_widget_template = '{before_widget} <div class="your_class_name"> {before_title}{title} {after_title}
</div> {after_widget}';

	$post_types = get_post_types('', 'names'  );

	

	

	$section['page'] = (isset($section['page']) && is_array($section['page'])) ? $section['page'] : array();

	$section['pages'] = (isset($section['pages']) && is_array($section['pages'])) ? $section['pages'] : array();

	$section['template'] = (isset($section['template']) && is_array($section['template'])) ? $section['template'] : array();
          
	$section['taxonomies'] = (isset($section['taxonomies']) && is_array($section['taxonomies'])) ? $section['taxonomies'] : array();

	

	

	$disabled_post_types = array('page','attachment','revision','nav_menu_item','option-tree');

	

	 $post_type_template = "<select  name='" . esc_attr( $name ) . "[" . esc_attr( $key ) . "][pages][]"."' value=\"\" class=\"option-tree-ui-multiselect\" multiple style=\"height: 100px; width: 100%;\">";

	 if(in_array('page',$section['pages'])){

	 $post_type_template .="<option value=\"page\" selected>Page</option>";

	 }else{

	 $post_type_template .="<option value=\"page\" >Page</option>";

	 }

	  foreach ( $post_types as $keya => $value ) {

		  if(!in_array($value,$disabled_post_types)){

	 if(in_array($value,$section['pages'])){

	    $post_type_template.="<option value=\"$value\" selected>$keya</option>";

	 }else{

	    $post_type_template.="<option value=\"$value\">$keya</option>";

	 }

		  }

   }

	 $post_type_template .="</select>";

	

	

  $templates = get_page_templates();

  $page_template = "<select  name='" . esc_attr( $name ) . "[" . esc_attr( $key ) . "][template][]"."' value=\"\" class=\"option-tree-ui-multiselect\" multiple style=\"height: 100px; width: 100%;\">";

   if(in_array('default',$section['template'])){

 		 $page_template .= "<option value=\"default\" selected>Default</option>";

   }else{

  		$page_template .= "<option value=\"default\">Default</option>";

   }

 foreach ( $templates as $template_name => $template_filename ) {

	 if(in_array($template_filename,$section['template'])){

	    $page_template.="<option value=\"$template_filename\" selected>$template_name</option>";

	 }else{

	    $page_template.="<option value=\"$template_filename\">$template_name</option>";

	 }

   }

   $page_template .="</select>";

   

   

    $all_pages = get_posts('post_type=page');

  $all_pages_template = "<select  name='" . esc_attr( $name ) . "[" . esc_attr( $key ) . "][page][]"."' value=\"\" class=\"option-tree-ui-multiselect\" multiple style=\"height: 100px; width: 100%;\">";

  

   if(in_array('default',$section['page'])){

  		$all_pages_template .= "<option value=\"default\" selected>Default</option>";

   }else{

  		$all_pages_template .= "<option value=\"default\">Default</option>";

   }

 foreach ( $all_pages as $sin_page ) {

	 if(in_array($sin_page->ID,$section['page'])){

	    $all_pages_template.="<option value=\"{$sin_page->ID}\" selected>{$sin_page->post_title}</option>";

	 }else{

	    $all_pages_template.="<option value=\"{$sin_page->ID}\">{$sin_page->post_title}</option>";

	 }

   }

   $all_pages_template .="</select>";
          
          
          
     $disabled_taxonomies = array('link_category','post_format');
          $all_taxonomies = get_taxonomies();

  $all_taxonomies_template = "<select  name='" . esc_attr( $name ) . "[" . esc_attr( $key ) . "][taxonomies][]"."' value=\"\" class=\"option-tree-ui-multiselect\" multiple style=\"height: 100px; width: 100%;\">";

  
 

 foreach ( $all_taxonomies as $sin_taxnomy ) {
       if(!in_array($sin_taxnomy,$disabled_taxonomies)){

             if(in_array($sin_taxnomy,$section['taxonomies'])){

                $all_taxonomies_template.="<option value=\"{$sin_taxnomy}\" selected>{$sin_taxnomy}</option>";

             }else{

                $all_taxonomies_template.="<option value=\"{$sin_taxnomy}\">{$sin_taxnomy}</option>";

             }
       }

   }

   $all_taxonomies_template .="</select>";      

   

   

   $page_field ='<div class="format-settings">

          <div class="format-setting type-text">

            <div class="description">' . __( '<strong>Page</strong>: Specify a page that you want to display this metabox. Seperated by comma', 'option-tree' ) . '</div>

            <div class="format-setting-inner">

             '.$all_pages_template.'

            </div>

          </div>

        </div>';

  $widget_template_field = '<div class="format-settings widget_template_section_field">

          <div class="format-setting type-text">

            <div class="description">' . __( '<strong>Widget Template</strong>: Write some html that you want to display this widget on front-end. 
            <br /><br />
            Eg code: <code>
            {before_widget}
            &lt;div class="your_class_name"&gt;{before_title}{title} {after_title}<br /><br />
{loop:my_first_slider_id}<br />
{title}<br />
{description}<br />
&lt;img src="{image}" /&gt;<br />
{endloop:my_first_slider_id}<br /><br />

{loop:my_second_slider_id}<br />
{title}<br />
{description}<br />
&lt;img src="{image}" /&gt;<br />
{endloop:my_second_slider_id}<br /><br />

{image:url}<br />
{attachment_id:url}
<br /><br />
&lt;/div&gt;<br />
            {after_widget}</code>
            <br />
            <br />
            Usage : {your_field_id} will replace with value of "your_field_id"
            <br />
            <br />
            <strong>To write a loop </strong>
            <br />
            {loop:your_repeating_field_id}
            <br />
            // inside of this space, you can call sub fields of this field like {title}
            <br />
            {endloop:your_repeating_field_id}
            <br /><br />
            <strong>To Convert attachment ID or to url</strong> <br />
            Use ":url" suffix<br />
            Eg: {image:url} this will convert {image}  to url<br />
            Eg: {attachment_id:url} this will convert {attachment_id}  to url
            
            
             <br /><br />
            <strong>To Write your own shortcode</strong> <br />
            Use [your_custom_shortcode id="{field_id}"]<br />
            Note you can also use global variables such as $args and $instance to get this widget datas
            
            ', 'option-tree' ) . '</div>

            <div class="format-setting-inner">

            <textarea type="text" name="' . esc_attr( $name ) . '[' . esc_attr( $key ) . '][widget_template]" value="" class="widefat option-tree-ui-input section-id" rows=10>' . ( isset( $section['widget_template'] ) ? esc_attr( $section['widget_template'] ) : $default_widget_template ) . '</textarea>

            </div>

          </div>

        </div>';

    return '

    <div class="option-tree-setting is-section">

      <div class="open">' . ( isset( $section['title'] ) ? esc_attr( $section['title'] ) : 'Section ' . ( $key + 1 ) ) . '</div>

      <div class="button-section">

        <a href="javascript:void(0);" class="option-tree-setting-edit option-tree-ui-button button left-item" title="' . __( 'edit', 'option-tree' ) . '">

          <span class="icon ot-icon-pencil"></span>' . __( 'Edit', 'option-tree' ) . '

        </a>

        <a href="javascript:void(0);" class="option-tree-setting-remove option-tree-ui-button button button-secondary light right-item" title="' . __( 'Delete', 'option-tree' ) . '">

          <span class="icon ot-icon-trash-o"></span>' . __( 'Delete', 'option-tree' ) . '

        </a>

      </div>

      <div class="option-tree-setting-body">

        <div class="format-settings">

          <div class="format-setting type-text">

            <div class="description">' . __( '<strong>Section Title</strong>: Displayed as a menu item on the Theme Options page.', 'option-tree' ) . '</div>

            <div class="format-setting-inner">

              <input type="text" name="' . esc_attr( $name ) . '[' . esc_attr( $key ) . '][title]" value="' . ( isset( $section['title'] ) ? esc_attr( $section['title'] ) : '' ) . '" class="widefat option-tree-ui-input option-tree-setting-title section-title" autocomplete="off" />

            </div>

          </div>

        </div>

        <div class="format-settings">

          <div class="format-setting type-text">

            <div class="description">' . __( '<strong>Section ID</strong>: A unique lower case alphanumeric string, underscores allowed.', 'option-tree' ) . '</div>

            <div class="format-setting-inner">

              <input type="text" name="' . esc_attr( $name ) . '[' . esc_attr( $key ) . '][id]" value="' . ( isset( $section['id'] ) ? esc_attr( $section['id'] ) : '' ) . '" class="widefat option-tree-ui-input section-id" autocomplete="off" />

            </div>

          </div>

        </div>

		

		 <div class="format-settings post_type_section_field">

          <div class="format-setting type-text">

            <div class="description">' . __( '<strong>Post type</strong>:  Specify a post type that you want to display this metabox. Seperated by comma', 'option-tree' ) . '</div>

            <div class="format-setting-inner">

             '. $post_type_template.'

            </div>

          </div>

        </div>

		

		 <div class="format-settings template_page_section_field">

          <div class="format-setting type-text">

            <div class="description">' . __( '<strong>Template</strong>: Specify a template that you want to display this metabox. Seperated by comma', 'option-tree' ) . '</div>

            <div class="format-setting-inner">

             '.$page_template.'

            </div>

          </div>

        </div>
        
        <div class="format-settings taxonomies_section_field">

          <div class="format-setting type-text">

            <div class="description">' . __( '<strong>Taxonomy</strong>: Specify a template that you want to display this metabox. Seperated by comma', 'option-tree' ) . '</div>

            <div class="format-setting-inner">

             '.$all_taxonomies_template.'

            </div>

          </div>

        </div>

		

		'.$widget_template_field.'

		

      </div>

    </div>';

	  }else{

  

    return '

    <div class="option-tree-setting is-section">

      <div class="open">' . ( isset( $section['title'] ) ? esc_attr( $section['title'] ) : 'Section ' . ( $key + 1 ) ) . '</div>

      <div class="button-section">

        <a href="javascript:void(0);" class="option-tree-setting-edit option-tree-ui-button button left-item" title="' . __( 'edit', 'option-tree' ) . '">

          <span class="icon ot-icon-pencil"></span>' . __( 'Edit', 'option-tree' ) . '

        </a>

        <a href="javascript:void(0);" class="option-tree-setting-remove option-tree-ui-button button button-secondary light right-item" title="' . __( 'Delete', 'option-tree' ) . '">

          <span class="icon ot-icon-trash-o"></span>' . __( 'Delete', 'option-tree' ) . '

        </a>

      </div>

      <div class="option-tree-setting-body">

        <div class="format-settings">

          <div class="format-setting type-text">

            <div class="description">' . __( '<strong>Section Title</strong>: Displayed as a menu item on the Theme Options page.', 'option-tree' ) . '</div>

            <div class="format-setting-inner">

              <input type="text" name="' . esc_attr( $name ) . '[' . esc_attr( $key ) . '][title]" value="' . ( isset( $section['title'] ) ? esc_attr( $section['title'] ) : '' ) . '" class="widefat option-tree-ui-input option-tree-setting-title section-title" autocomplete="off" />

            </div>

          </div>

        </div>

        <div class="format-settings">

          <div class="format-setting type-text">

            <div class="description">' . __( '<strong>Section ID</strong>: A unique lower case alphanumeric string, underscores allowed.', 'option-tree' ) . '</div>

            <div class="format-setting-inner">

              <input type="text" name="' . esc_attr( $name ) . '[' . esc_attr( $key ) . '][id]" value="' . ( isset( $section['id'] ) ? esc_attr( $section['id'] ) : '' ) . '" class="widefat option-tree-ui-input section-id" autocomplete="off" />

            </div>

          </div>

        </div>

		

      </div>

    </div>';

	  }

    

  }



}





   
add_action( 'admin_menu', 'register_my_custom_menu_page' );

   


function register_my_custom_menu_page(){
    
    add_menu_page('Metaboxes', 'Metaboxes', 'manage_options', 'rj_ot-settings', 'rj_ot_type_theme_options_ui','', 99 );
	
	add_submenu_page( 
          'rj_ot-settings'   //or 'options.php' 
        , 'UserMetabox' 
        , 'User Metaboxes'
        , 'manage_options'
        , 'rj-ot-user_metabox'
        , 'rj_user_ot_metabox_callback'
    );
    add_submenu_page( 
          'rj_ot-settings'   //or 'options.php' 
        , 'TaxonomyMetabox' 
        , 'Taxonomy Metaboxes'
        , 'manage_options'
        , 'rj-ot-taxonomy_metabox'
        , 'rj_taxonomy_ot_metabox_callback'
    );
    add_submenu_page( 
          'rj_ot-settings'   //or 'options.php' 
        , 'WidgetMetabox' 
        , 'Widget Metaboxes'
        , 'manage_options'
        , 'rj-ot-widget_metabox'
        , 'rj_widget_ot_metabox_callback'
    );
    
	add_submenu_page( 
          'rj_ot-settings'   //or 'options.php' 
        , 'Import' 
        , 'Import'
        , 'manage_options'
        , 'rj-ot-import'
        , 'rj_ot_import_callback'
    );
	add_submenu_page( 
          'rj_ot-settings'   //or 'options.php' 
        , 'Export' 
        , 'Export'
        , 'manage_options'
        , 'rj-ot-export'
        , 'rj_ot_export_callback'
    );
	add_submenu_page( 
          'rj_ot-settings'   //or 'options.php' 
        , 'Documentation' 
        , 'Documentation'
        , 'manage_options'
        , 'rj-ot-documentation'
        , 'rj_ot_documentation_callback'
    );
	
	

}



add_action( 'admin_init', 'rj_ot_show_settings');

function rj_ot_show_settings(){

	$meta_options = get_option( 'rj'.ot_settings_id() );

	$post_id = (isset($post_id)) ? $post_id : '';

	

	

	

	

	//print_r($meta_options);

  

 // print_r($new_meta_boadx);

  //print_r($meta_options);

	

	

	if(!empty($meta_options['sections'])){
		


		$mi=0;
		foreach($meta_options['sections'] as $meta_option){

			if(isset($_REQUEST['post']) && $_REQUEST['post']){

				$post_id =$_REQUEST['post'];

			}else if(isset($_REQUEST['post_ID']) && $_REQUEST['post_ID']){

				$post_id =$_REQUEST['post_ID'];

			}
            
            $current_post_type = get_post_type($post_id);
            
			$post_template = get_post_meta($post_id,'_wp_page_template',true);

			$post_template = ($post_template || $post_template !='') ? $post_template : 'default';
            
           
			$new_meta_box =array();

			$settings =array();

			//$template_array = explode(',',$meta_option['template']);

			

			$pages_arr = (isset($meta_option['page']) && is_array($meta_option['page'])) ? $meta_option['page'] :array();

			

			$template_array = is_array($meta_option['template']) ? $meta_option['template'] : array('default');

			if(in_array($post_template,$template_array)){

				

			if(!empty($meta_options['settings'])){

				$i=0;

				foreach($meta_options['settings'] as $meta_settings){

					if($meta_settings['section']==$meta_option['id']){

						$settings[$i] = $meta_settings;

					}

					$i++;

				}

			}

			

			$new_meta_box[$mi] = array(

		'id'          => $meta_option['id'],

		'title'       => __( $meta_option['title'], 'theme-text-domain' ),

		'desc'        => '',

		'pages'       => $meta_option['pages'],

		'context'     => 'normal',

		'priority'    => 'high',

		'fields'      => $settings

	  );

	  

		ot_register_meta_box( $new_meta_box[$mi] );

		$mi++;

			}

			

		

		}

	}
}





//usage

if ( ! function_exists( 'ot' ) ) {

	

	function ot($var,$echo='',$defVal=''){

		if ( function_exists( 'ot_get_option' ) ) {

		  $output = ot_get_option( $var,$defVal);

		  if(is_array($output)){

	}else{

		$output = do_shortcode($output);

	}

		  

		  if($echo==1){

			echo $output;

			}else{

				return $output;

			}

		

		

		}

		

		

	}

}

if ( ! function_exists( 'ot_typography' ) ) {
function ot_typography($var,$echo='',$defVal=array()){
      $styles = "";
    if(is_array($var)){
    }else{
        $var = ot($var);
    }
    if(!empty($var)){
        foreach($var as $key => $val){
            if($val !=''){
                if($key=='font-color'){
                    $styles  .= "color : $val;";
                }else{
                    $styles  .= "$key : $val;";
                }
            }
        }
    }
    if($echo==1){
        echo $styles;
    }else{
        return $styles;
    }
}
}
function ot_meta_attachment($var,$echo=''){	
	$attachment_id = ot_meta($var);
	$attachment_url = wp_get_attachment_url( $attachment_id); // returns an array
	if($echo==1){
		echo $attachment_url;
	}else{
		return $attachment_url;
	}
}
function ot_meta_image($var,$echo='',$size='full'){	
	$attachment_id = ot_meta($var);
	$image_attributes = wp_get_attachment_image_src( $attachment_id,$size ); // returns an array
	if($echo==1){
		echo $image_attributes[0];
	}else{
		return $image_attributes[0];
	}
}




function ot_attachment($var,$echo=''){	
	$attachment_id = ot($var);
	$attachment_url = wp_get_attachment_url( $attachment_id); // returns an array
	if($echo==1){
		echo $attachment_url;
	}else{
		return $attachment_url;
	}
}
function ot_image($var,$echo='',$size='full'){	
	$attachment_id = ot($var);
	$image_attributes = wp_get_attachment_image_src( $attachment_id,$size ); // returns an array
	if($echo==1){
		echo $image_attributes[0];
	}else{
		return $image_attributes[0];
	}
}



function ot_meta_attachment_by_id($var,$echo='',$defaultVal=''){
	$attachment_url  = wp_get_attachment_url( $var ); // returns an array
	
	if( $attachment_url ) {
		if($echo==1){
			echo $attachment_url;
		}else{
			return $attachment_url;
		}	
	}else{
		if($echo==1){
			echo $defaultVal;
		}else{
			return $defaultVal;
		}	
	}
}

function ot_meta_image_by_id($var,$echo='',$defaultVal='',$size='full'){
	$image_attributes  = wp_get_attachment_image_src( $var,$size ); // returns an array
	
	if( $image_attributes ) {
		if($echo==1){
			echo $image_attributes[0];
		}else{
			return $image_attributes[0];
		}	
	}else{
		if($echo==1){
			echo $defaultVal;
		}else{
			return $defaultVal;
		}	
	}
}



function ot_attachment_by_id($var,$echo='',$defaultVal='',$size='full'){
	$attachment_url  = wp_get_attachment_url( $var); // returns an array
	
	if( $attachment_url ) {
		if($echo==1){
			echo $attachment_url;
		}else{
			return $attachment_url;
		}	
	}else{
		if($echo==1){
			echo $defaultVal;
		}else{
			return $defaultVal;
		}	
	}
}

function ot_image_by_id($var,$echo='',$defaultVal='',$size='full'){
	$image_attributes  = wp_get_attachment_image_src( $var,$size ); // returns an array
	
	if( $image_attributes ) {
		if($echo==1){
			echo $image_attributes[0];
		}else{
			return $image_attributes[0];
		}	
	}else{
		if($echo==1){
			echo $defaultVal;
		}else{
			return $defaultVal;
		}	
	}
}


if ( ! function_exists( 'ot_array' ) ) {

	

function ot_array($var){

		if ( function_exists( 'ot_get_option' ) ) {		  

		  /* get the slider array */

		  $output = ot_get_option($var, array() );

		  

		  if(is_array($output)){

	}else{

		$output = do_shortcode($output);

	}

		  return $output;

		}

	}

}



if ( ! function_exists( 'ot_meta' ) ) {

	

function ot_meta($var,$echo=''){

	$output = get_post_meta(get_the_ID(),$var,true);

	 if(is_array($output)){

	}else{

		$output = do_shortcode($output);

	}

	

	if($echo==1){

		echo $output;

	}else{

		return $output;

	}

}

}

if ( ! function_exists( 'ot_meta_short' ) ) {

	

function ot_meta_short($var){
    
    $atts = shortcode_atts( array(
		'id' => '',
        'type'=>'image'
	), $var, 'rj_ot' );



	 

	 if($atts['type']=='image'){
        $output = ot_meta_attachment($atts['id']);
    }else{
         $output = ot_meta($atts['id']);
     }
if(is_array($output)){

	}else{

		$output = do_shortcode($output);

	}
	return $output;

}
add_shortcode( 'ot_meta_short', 'ot_meta_short' );

}


if ( ! function_exists( 'ot_meta_image_short' ) ) {

	

function ot_meta_image_short($var){
    
    $atts = shortcode_atts( array(
		'id' => ''
	), $var, 'rj_ot' );



	 

	$output = ot_meta_attachment($atts['id']);
if(is_array($output)){

	}else{

		$output = do_shortcode($output);

	}
	return $output;

}
add_shortcode( 'ot_meta_image_short', 'ot_meta_image_short' );

}


if ( ! function_exists( 'ot_short' ) ) {

	

function ot_short($var){
    
    $atts = shortcode_atts( array(
		'id' => '',
        'type'=>'image'
	), $var, 'rj_ot' );


	
    
    if($atts['type']=='image'){
        $output = ot_attachment($atts['id']);
    }else{
        $output = ot($atts['id']);
    }
	

	return $output;

}
add_shortcode( 'ot_short', 'ot_short' );
}

if ( ! function_exists( 'ot_image_short' ) ) {

	

function ot_image_short($var){
    
    $atts = shortcode_atts( array(
		'id' => ''
	), $var, 'rj_ot' );


	
    
   $output = ot_attachment($atts['id']);
	

	return $output;

}
add_shortcode( 'ot_image_short', 'ot_image_short' );
}


if ( ! function_exists( 'ot_meta_typography' ) ) {
function ot_meta_typography($var,$echo='',$defVal=array()){
      $styles = "";
    if(is_array($var)){
    }else{
        $var = ot_meta($var);
    }
    if(!empty($var)){
        foreach($var as $key => $val){
            if($val !=''){
                if($key=='font-color'){
                    $styles  .= "color : $val;";
                }else{
                    $styles  .= "$key : $val;";
                }
            }
        }
    }
    if($echo==1){
        echo $styles;
    }else{
        return $styles;
    }
}
}

add_filter( 'ot_override_forced_textarea_simple','__return_true' );

if ( ! function_exists( 'base_url' ) ) {

function base_url($echo=''){

	if($echo==1){

		echo get_bloginfo('wpurl').'/';

	}else{

		return get_bloginfo('wpurl').'/';

	}

}



add_shortcode('base_url','base_url');

}

if ( ! function_exists( 'temp_url' ) ) {

function temp_url($echo=''){

	if($echo==1){

		echo get_stylesheet_directory_uri().'/';

	}else{

		return get_stylesheet_directory_uri().'/';

	}

}

add_shortcode('temp_url','temp_url');

}

if ( ! function_exists( 'page_link' ) ) {

function page_link($atts){
$atts = shortcode_atts( array(
		'id' => '',
		'echo'=>''
	), $atts, 'metaboxui' );
	
	if($atts['echo']==1){

		echo get_permalink($atts['id']).'/';

	}else{

		return get_permalink($atts['id']).'/';

	}

}



add_shortcode('page_link','page_link');

}





function rj_add_custom_option_types( $types ) {



  $types['rj_upload_media'] = 'Attachment URL';

  $types['rj_upload_attach_id'] = 'Attachment ID';

  $types['rj_button'] = 'Button';

  $types['rj_location'] = 'Map';

  $types['rj_shortcode'] = 'Shortcode';
  
  $types['rj_users'] = 'Users';



  return $types;



}

add_filter( 'ot_option_types_array', 'rj_add_custom_option_types' );





if ( ! function_exists( 'ot_type_rj_upload_media' ) ) {

  

  function ot_type_rj_upload_media( $args = array() ) {

    

    /* turns arguments array into variables */

    extract( $args );

    

    /* verify a description */

    $has_desc = $field_desc ? true : false;

    

    /* If an attachment ID is stored here fetch its URL and replace the value */

    if ( $field_value && wp_attachment_is_image( $field_value ) ) {

    

      $attachment_data = wp_get_attachment_image_src( $field_value, 'original' );

      

      /* check for attachment data */

      if ( $attachment_data ) {

      

        $field_src = $attachment_data[0];

		

        

      }

      

    }

    /* format setting outer wrapper */

    echo '<div class="format-setting type-upload ' . ( $has_desc ? 'has-desc' : 'no-desc' ) . '">';

      

      /* description */

      echo $has_desc ? '<div class="description">' . htmlspecialchars_decode( $field_desc ) . '</div>' : '';

      

      /* format setting inner wrapper */

      echo '<div class="format-setting-inner">';

      

        /* build upload */

        echo '<div class="option-tree-ui-upload-parent">';

          

    $field_value = str_replace(base_url(),'[base_url]',$field_value);

          /* input */

          echo '<input type="text" name="' . esc_attr( $field_name ) . '" id="' . esc_attr( $field_id ) . '" value="' . esc_attr( $field_value ) . '" class="widefat option-tree-ui-upload-input ' . esc_attr( $field_class ) . '" />';

          

          /* add media button */

          echo '<a href="javascript:void(0);" class="ot_upload_media option-tree-ui-button button button-primary light" rel="' . $post_id . '" title="' . __( 'Add Media', 'option-tree' ) . '"><span class="icon ot-icon-plus-circle"></span>' . __( 'Add Media', 'option-tree' ) . '</a>';

        

        echo '</div>';

        

        /* media */

        if ( $field_value ) {

            

          echo '<div class="option-tree-ui-media-wrap" id="' . esc_attr( $field_id ) . '_media">';

            

            /* replace image src */

            if ( isset( $field_src ) )

              $field_value = $field_src;

              

            if ( preg_match( '/\.(?:jpe?g|png|gif|ico)$/i', $field_value ) )

              echo '<div class="option-tree-ui-image-wrap"><img src="' . esc_url( do_shortcode($field_value) ) . '" alt="" /></div>';

            

            echo '<a href="javascript:(void);" class="option-tree-ui-remove-media option-tree-ui-button button button-secondary light" title="' . __( 'Remove Media', 'option-tree' ) . '"><span class="icon ot-icon-minus-circle"></span>' . __( 'Remove Media', 'option-tree' ) . '</a>';

            

          echo '</div>';

          

        }

        

      echo '</div>';

    

    echo '</div>';

    

  }

  

}



/* End of file ot-functions-option-types.php */

/* Location: ./includes/ot-functions-option-types.php */







if ( ! function_exists( 'ot_type_rj_upload_attach_id' ) ) {

  

  function ot_type_rj_upload_attach_id( $args = array() ) {

    

    /* turns arguments array into variables */

    extract( $args );

    

    /* verify a description */

    $has_desc = $field_desc ? true : false;

    

    /* If an attachment ID is stored here fetch its URL and replace the value */

    if ( $field_value && wp_attachment_is_image( $field_value ) ) {

    

      $attachment_data = wp_get_attachment_image_src( $field_value, 'original' );

      

      /* check for attachment data */

      if ( $attachment_data ) {

      

        $field_src = $attachment_data[0];

		

        

      }

      

    }

    /* format setting outer wrapper */

    echo '<div class="format-setting type-upload ' . ( $has_desc ? 'has-desc' : 'no-desc' ) . '">';

      

      /* description */

      echo $has_desc ? '<div class="description">' . htmlspecialchars_decode( $field_desc ) . '</div>' : '';

      

      /* format setting inner wrapper */

      echo '<div class="format-setting-inner">';

      

        /* build upload */

        echo '<div class="option-tree-ui-upload-parent">';

          

    $field_value = str_replace(base_url(),'[base_url]',$field_value);

          /* input */

          echo '<input type="text" name="' . esc_attr( $field_name ) . '" id="' . esc_attr( $field_id ) . '" value="' . esc_attr( $field_value ) . '" class="widefat option-tree-ui-upload-input ' . esc_attr( $field_class ) . '" />';

          

          /* add media button */

          echo '<a href="javascript:void(0);" class="rj_ot_upload_media option-tree-ui-button button button-primary light" rel="' . $post_id . '" title="' . __( 'Add Media', 'option-tree' ) . '"><span class="icon ot-icon-plus-circle"></span>' . __( 'Add Media', 'option-tree' ) . '</a>';

        

        echo '</div>';

        

        /* media */

        if ( $field_value ) {

            

          echo '<div class="option-tree-ui-media-wrap" id="' . esc_attr( $field_id ) . '_media">';

            

            /* replace image src */

            if ( isset( $field_src ) )

              $field_value = $field_src;

              

            if ( preg_match( '/\.(?:jpe?g|png|gif|ico)$/i', $field_value ) )

              echo '<div class="option-tree-ui-image-wrap"><img src="' . esc_url( do_shortcode($field_value) ) . '" alt="" /></div>';

            

            echo '<a href="javascript:(void);" class="option-tree-ui-remove-media option-tree-ui-button button button-secondary light" title="' . __( 'Remove Media', 'option-tree' ) . '"><span class="icon ot-icon-minus-circle"></span>' . __( 'Remove Media', 'option-tree' ) . '</a>';

            

          echo '</div>';

          

        }

        

      echo '</div>';

    echo '</div>';

    

  }

  

}


/**
 * Button option type.
 *
 * See @ot_display_by_type to see the full list of available arguments.
 *
 * @param     array     An array of arguments.
 * @return    string
 *
 * @access    public
 * @since     2.0
 */
if ( ! function_exists( 'ot_type_rj_button' ) ) {
  
  function ot_type_rj_button( $args = array() ) {
    
    /* turns arguments array into variables */
    extract( $args );
    
    /* verify a description */
    $has_desc = $field_desc ? true : false;
    
    /* format setting outer wrapper */
    echo '<div class="format-setting type-button ' . ( $has_desc ? 'has-desc' : 'no-desc' ) . '">';
      
      /* description */
      echo $has_desc ? '<div class="description">' . htmlspecialchars_decode( $field_desc ) . '</div>' : '';
      
      /* format setting inner wrapper */
      echo '<div class="format-setting-inner">';
      
        echo '<div class="option-tree-ui-measurement-input-wrap">';
        
          echo '<input type="text" name="' . esc_attr( $field_name ) . '[0]" id="' . esc_attr( $field_id ) . '-0" value="' . ( isset( $field_value[0] ) ? esc_attr( $field_value[0] ) : '' ) . '" class=" option-tree-ui-input inline_rj_input' . esc_attr( $field_class ) . '" placeholder="Label" />';
        
        
       echo '<input type="text" name="' . esc_attr( $field_name ) . '[1]" id="' . esc_attr( $field_id ) . '-1" value="' . ( isset( $field_value[1] ) ? esc_attr( $field_value[1] ) : '' ) . '" class=" option-tree-ui-input inline_rj_input '  . esc_attr( $field_class ) . '" placeholder="Link" />';
        
      echo '</div>';
      
      
         
       
      
      echo '</div>';
    
    echo '</div>';
    
  }
  
}



/**
 * Location option type.
 *
 * See @ot_display_by_type to see the full list of available arguments.
 *
 * @param     array     An array of arguments.
 * @return    string
 *
 * @access    public
 * @since     2.0
 */
$is_rj_google_map_loded = false;
if ( ! function_exists( 'ot_type_rj_location' ) ) {
  
  function ot_type_rj_location( $args = array() ) {
    global $is_rj_google_map_loded;
    /* turns arguments array into variables */
    extract( $args );
    
    /* verify a description */
    $has_desc = $field_desc ? true : false;
      
      if($is_rj_google_map_loded==false){
      echo ' <script src="https://maps.googleapis.com/maps/api/js?v=3.exp&signed_in=true&libraries=places"></script>';
          $is_rj_google_map_loded=true;
      }
      $unique_id = esc_attr( $field_name ) . '_0_';
     
    
    /* format setting outer wrapper */
    echo '<div class="format-setting type-location ' . ( $has_desc ? 'has-desc' : 'no-desc' ) . '" id="' . esc_attr( $field_name ) . '_0_wrap">';
      
     echo ' <div id="' . esc_attr( $field_name ) . '_0_locationField">
      <input id="' . esc_attr( $field_name ) . '_0" placeholder="Enter a location"
             onFocus="'.$unique_id.'geolocate()" type="text" autocomplete="off" name="' . esc_attr( $field_name ) . '[0]" value="' . ( isset( $field_value[0] ) ? esc_attr( $field_value[0] ) : '' ) . '" />
    </div>

    <table id="' . esc_attr( $field_name ) . '_0_address">
      <tr>
        <td class="label">Cordinates</td>
        <td class="slimField"><input class="field latitude" name="' . esc_attr( $field_name ) . '[1]" value="' . ( isset( $field_value[1] ) ? esc_attr( $field_value[1] ) : '' ) . '" /></td>
        <td class="wideField" colspan="2"><input class="field longitude" name="' . esc_attr( $field_name ) . '[2]" value="' . ( isset( $field_value[2] ) ? esc_attr( $field_value[2] ) : '' ) . '" /></td>
      </tr>
      <tr>
        <td class="label">Street address</td>
        <td class="slimField"><input class="field street_number" name="' . esc_attr( $field_name ) . '[3]" value="' . ( isset( $field_value[3] ) ? esc_attr( $field_value[3] ) : '' ) . '" /></td>
        <td class="wideField" colspan="2"><input class="field route"  name="' . esc_attr( $field_name ) . '[4]" value="' . ( isset( $field_value[4] ) ? esc_attr( $field_value[4] ) : '' ) . '" /></td>
      </tr>
      <tr>
        <td class="label">City</td>
        <td class="wideField" colspan="3"><input class="field locality" name="' . esc_attr( $field_name ) . '[5]" value="' . ( isset( $field_value[5] ) ? esc_attr( $field_value[5] ) : '' ) . '" /></td>
      </tr>
      <tr>
        <td class="label">State</td>
        <td class="slimField"><input class="field administrative_area_level_1" name="' . esc_attr( $field_name ) . '[6]" value="' . ( isset( $field_value[6] ) ? esc_attr( $field_value[6] ) : '' ) . '" /></td>
        <td class="label">Zip code</td>
        <td class="wideField"><input class="field postal_code"  name="' . esc_attr( $field_name ) . '[7]" value="' . ( isset( $field_value[7] ) ? esc_attr( $field_value[7] ) : '' ) . '"></td>
      </tr>
      <tr>
        <td class="label">Country</td>
        <td class="wideField" colspan="3"><input class="field country" name="' . esc_attr( $field_name ) . '[8]" value="' . ( isset( $field_value[8] ) ? esc_attr( $field_value[8] ) : '' ) . '" /></td>
      </tr>
    </table>';
        
      echo '</div>';
      
      echo '<div id="'.$unique_id.'map_canvas" class="rj_map_option_type" style="width:100%; height:350px;"></div>';
      
       ?>
       <script>
 
           // This example displays an address form, using the autocomplete feature
// of the Google Places API to help users fill in the information.

var <?php echo $unique_id ?>placeSearch, <?php echo $unique_id ?>autocomplete;
var <?php echo $unique_id ?>componentForm = {
  street_number: 'short_name',
  route: 'long_name',
  locality: 'long_name',
  administrative_area_level_1: 'short_name',
  country: 'long_name',
  postal_code: 'short_name'
};
           
var <?php echo $unique_id ?>geocoder;
           
           
           
           var <?php echo $unique_id ?>lat = "<?php echo esc_attr( $field_value[1]); ?>";
      var <?php echo $unique_id ?>long = "<?php echo esc_attr( $field_value[2] ); ?>";
            if (navigator.geolocation && <?php echo $unique_id ?>lat=="" && <?php echo $unique_id ?>long=="") {
    navigator.geolocation.getCurrentPosition(function(position) {
         <?php echo $unique_id ?>lat= position.coords.latitude;
         <?php echo $unique_id ?>long= position.coords.longitude;
    });
                
            }
           <?php echo $unique_id ?>geocoder = new google.maps.Geocoder();
           var <?php echo $unique_id ?>map = new google.maps.Map(document.getElementById('<?php echo $unique_id ?>map_canvas'), {
    zoom: 8,
    center: new google.maps.LatLng(<?php echo $unique_id ?>lat, <?php echo $unique_id ?>long),
    mapTypeId: google.maps.MapTypeId.ROADMAP
});

var <?php echo $unique_id ?>myMarker = new google.maps.Marker({
    position: new google.maps.LatLng(<?php echo $unique_id ?>lat, <?php echo $unique_id ?>long),
    draggable: true
});

google.maps.event.addListener(<?php echo $unique_id ?>myMarker, 'dragend', function (evt) {
   <?php echo $unique_id ?>geocodePosition(<?php echo $unique_id ?>myMarker.getPosition());
});

google.maps.event.addListener(<?php echo $unique_id ?>myMarker, 'dragstart', function (evt) {
});
           
function <?php echo $unique_id ?>geocodePosition(pos) {
  <?php echo $unique_id ?>geocoder.geocode({
    latLng: pos
  }, function(responses) {
    if (responses && responses.length > 0) {
        document.querySelector('#<?php echo esc_attr( $field_name ) . '_0'; ?>').value =  responses[0].formatted_address;
        <?php echo $unique_id ?>fillInAddress_map(responses);
    } else {
    }
  });
}       

<?php echo $unique_id ?>map.setCenter(<?php echo $unique_id ?>myMarker.position);
<?php echo $unique_id ?>myMarker.setMap(<?php echo $unique_id ?>map);
           
 // [START region_fillform]
function <?php echo $unique_id ?>fillInAddress_map(res) {
    
  var place = res[0];

  for (var component in <?php echo $unique_id ?>componentForm) {
   document.querySelector('#<?php echo esc_attr( $field_name ) . '_0_wrap'; ?> .'+ component).value = '';
  document.querySelector('#<?php echo esc_attr( $field_name ) . '_0_wrap'; ?> .'+ component).disabled = false;
  }
  // Get each component of the address from the place details
  // and fill the corresponding field on the form.

    var lat = place.geometry.location;
   document.querySelector('#<?php echo esc_attr( $field_name ) . '_0_wrap'; ?> .latitude').value =lat.A;
   document.querySelector('#<?php echo esc_attr( $field_name ) . '_0_wrap'; ?> .longitude').value =lat.F;
    
  for (var i = 0; i < place.address_components.length; i++) {
    var addressType = place.address_components[i].types[0];
    if (<?php echo $unique_id ?>componentForm[addressType]) {
      var val = place.address_components[i][<?php echo $unique_id ?>componentForm[addressType]];
      document.querySelector('#<?php echo esc_attr( $field_name ) . '_0_wrap'; ?> .'+ addressType).value = val;
    }
  }
}
// [END region_fillform]          
           
           
           


function <?php echo $unique_id ?>initialize() {
  // Create the autocomplete object, restricting the search
  // to geographical location types.
  <?php echo $unique_id ?>autocomplete = new google.maps.places.Autocomplete(
      /** @type {HTMLInputElement} */(document.getElementById('<?php echo  esc_attr( $field_name ) . '_0'; ?>')),
      { types: ['geocode'] });
  // When the user selects an address from the dropdown,
  // populate the address fields in the form.
  google.maps.event.addListener(<?php echo $unique_id ?>autocomplete, 'place_changed', function() {
    <?php echo $unique_id ?>fillInAddress();
  });
}

// [START region_fillform]
function <?php echo $unique_id ?>fillInAddress() {
  // Get the place details from the autocomplete object.
  var place = <?php echo $unique_id ?>autocomplete.getPlace();
    
   

  for (var component in <?php echo $unique_id ?>componentForm) {
   document.querySelector('#<?php echo esc_attr( $field_name ) . '_0_wrap'; ?> .'+ component).value = '';
  document.querySelector('#<?php echo esc_attr( $field_name ) . '_0_wrap'; ?> .'+ component).disabled = false;
  }
  // Get each component of the address from the place details
  // and fill the corresponding field on the form.

    var loc = place.geometry.location;
    document.querySelector('#<?php echo esc_attr( $field_name ) . '_0_wrap'; ?> .latitude').value =loc.A;
    document.querySelector('#<?php echo esc_attr( $field_name ) . '_0_wrap'; ?> .longitude').value =loc.F;
    
     var <?php echo $unique_id ?>geolocation2 = new google.maps.LatLng(loc.A,loc.F);
     <?php echo $unique_id ?>myMarker.setPosition(<?php echo $unique_id ?>geolocation2);
     <?php echo $unique_id ?>map.setCenter(<?php echo $unique_id ?>geolocation2);
<?php echo $unique_id ?>myMarker.setMap(<?php echo $unique_id ?>map);
    
  for (var i = 0; i < place.address_components.length; i++) {
    var addressType = place.address_components[i].types[0];
    if (<?php echo $unique_id ?>componentForm[addressType]) {
      var val = place.address_components[i][<?php echo $unique_id ?>componentForm[addressType]];
      document.querySelector('#<?php echo esc_attr( $field_name ) . '_0_wrap'; ?> .'+ addressType).value = val;
    }
  }
}
// [END region_fillform]

// [START region_geolocation]
// Bias the autocomplete object to the user's geographical location,
// as supplied by the browser's 'navigator.geolocation' object.
function <?php echo $unique_id ?>geolocate() {
  if (navigator.geolocation) {
    navigator.geolocation.getCurrentPosition(function(position) {
      var <?php echo $unique_id ?>geolocation = new google.maps.LatLng(
          position.coords.latitude, position.coords.longitude);
      var <?php echo $unique_id ?>circle = new google.maps.Circle({
        center: <?php echo $unique_id ?>geolocation,
        radius: position.coords.accuracy
      });
      <?php echo $unique_id ?>autocomplete.setBounds(<?php echo $unique_id ?>circle.getBounds());
        
       
    });
  }
}
// [END region_geolocation]
document.querySelector('body').onload = <?php echo $unique_id ?>initialize();
           
           
           
    </script>
      <?php
         
       
      
    
  }
  
}



/**
 * Shortcode option type.
 *
 * See @ot_display_by_type to see the full list of available arguments.
 *
 * @param     array     An array of arguments.
 * @return    string
 *
 * @access    public
 * @since     2.0
 */
$shortcode_args = array();
if ( ! function_exists( 'ot_type_rj_shortcode' ) ) {
  
  function ot_type_rj_shortcode( $args = array() ) {
    /* turns arguments array into variables */
      global $shortcode_args;
      $shortcode_args = $args;
    extract( $args );
    
    /* verify a description */
    $has_desc = $field_desc ? true : false;
      echo '<table id="' . esc_attr( $field_name ) . '">
      <tr>
        <td class="label">Shortcode</td>
        <td class="slimField"><input class="field ' . esc_attr( $field_name ) . '" name="' . esc_attr( $field_name ) . '" value="' . ( isset( $field_value ) ? esc_attr( $field_value ) : '' ) . '" />
        
        
        ';
      
      echo $has_desc ? ' ' . htmlspecialchars_decode( $field_desc ) . ' ' : '';
echo '        </td>
      </tr>
      </table>';
      
      echo do_shortcode($field_value);
  }
}
if ( ! function_exists( 'ot_type_rj_users' ) ) {
  
  function ot_type_rj_users( $args = array() ) {
    /* turns arguments array into variables */
    extract( $args );
	$user_args = array('fields'=>array('ID','display_name'));
	$allusers = get_users( $user_args );
	$options ='';
	
	$val = ( isset( $field_value ) ? esc_attr( $field_value ) : '' );
	if(!empty($allusers)){
		foreach($allusers as $alluser){
			if($val==$alluser->ID){
				$selected = " selected ";
			}else{
				$selected = "";
			}
			$options .="<option value='".$alluser->ID."' ".$selected." >".ucwords($alluser->display_name)."</option>";
		}
	}
    /* verify a description */
    $has_desc = $field_desc ? true : false;
      echo '<table id="' . esc_attr( $field_name ) . '" class="tax_meta_fields">
      <tr>
        <td class="label">&nbsp;</td>
        <td class="slimField">
		<select name="' . esc_attr( $field_name ) . '">
		<option value="">Choose a user</option>
		'.$options.'
		</select>
        
        
        ';
      
      echo $has_desc ? ' ' . htmlspecialchars_decode( $field_desc ) . ' ' : '';
echo '        </td>
      </tr>
      </table>';
  }
}






/* End of file ot-functions-option-types.php */

/* Location: ./includes/ot-functions-option-types.php */







 /* add scripts for metaboxes to post-new.php & post.php */

        //add_action( 'admin_print_scripts-post-new.php', 'rj_ot_admin_scripts', 11 );

        add_action( 'admin_footer', 'rj_ot_admin_scripts', 11 );

		

		

        /* add styles for metaboxes to post-new.php & post.php */

        //add_action( 'admin_print_styles-post-new.php', 'rj_ot_admin_styles', 11 );

        add_action( 'admin_footer', 'rj_ot_admin_styles', 11 );





 function rj_ot_admin_scripts() {
     
     global $pagenow;

	  wp_register_script( 'rj-metaboxui', plugin_dir_url( __FILE__ ) . 'js/metabox-ui.js' );

	 /* load jQuery UI timepicker addon */

    wp_enqueue_script( 'rj-metaboxui');

 }

 

 function rj_ot_admin_styles(){

	 wp_enqueue_style( 'rj-metaboxui',  plugin_dir_url( __FILE__ ) . 'css/metabox-ui.css' );

 }
 
 /**
 * This filter is used to use http://base_url in your navigation menu
 */
function pph_dynamic_menu_items( $menu_items ) {

    foreach ( $menu_items as $menu_item ) {
		
		$menu_url = str_replace('http://base_url',get_bloginfo('url'),$menu_item->url);
		$menu_item->url = $menu_url;
        
    }

    return $menu_items;
}
add_filter( 'wp_nav_menu_objects', 'pph_dynamic_menu_items' );


function ot_get_settings_label( $id,$echo ='' ) {

  if ( empty( $id ) )
    return false;

  $settings = get_option( 'option_tree_settings');

  if ( empty( $settings['settings'] ) )
    return false;

  foreach( $settings['settings'] as $setting ) {

    if ( $setting['id'] == $id && isset( $setting['label'] ) ) {
        
      if($echo==1){
                echo $setting['label'];
            }else{
                return $setting['label'];
            }

    }

  }

}


function ot_get_settings_desc( $id,$echo ='' ) {

  if ( empty( $id ) )
    return false;

  $settings = get_option( 'option_tree_settings');

  if ( empty( $settings['settings'] ) )
    return false;

  foreach( $settings['settings'] as $setting ) {

    if ( $setting['id'] == $id && isset( $setting['desc'] ) ) {
        
      if($echo==1){
                echo $setting['desc'];
            }else{
                return $setting['desc'];
            }

    }

  }

}

function ot_get_settings_data( $id ) {

  if ( empty( $id ) )
    return false;

  $settings = get_option('option_tree_settings');

  if ( empty( $settings['settings'] ) )
    return false;

  foreach( $settings['settings'] as $setting ) {
    if ( $setting['id'] == $id ) {

      return $setting;

    }

  }

}

    function ot_get_meta_label( $id,$echo ='' ) {
      if ( empty( $id ) )
        return false;

      $settings = get_option('rj'.ot_settings_id());

      if ( empty( $settings['settings'] ) )
        return false;
      foreach( $settings['settings'] as $setting ) {

        if ( $setting['id'] == $id && isset( $setting['label'] ) ) {
            
            
            if($echo==1){
                echo $setting['label'];
            }else{
                return $setting['label'];
            }

        }

      }

    }
function ot_get_meta_desc( $id,$echo='' ) {

  if ( empty( $id ) )
    return false;

  $settings = get_option('rj'.ot_settings_id());

  if ( empty( $settings['settings'] ) )
    return false;

  foreach( $settings['settings'] as $setting ) {

    if ( $setting['id'] == $id && isset( $setting['desc'] ) ) {

      if($echo==1){
                echo $setting['desc'];
            }else{
                return $setting['desc'];
            }

    }

  }

}
function ot_get_meta_data( $id ) {

  if ( empty( $id ) )
    return false;

  $settings = get_option('rj'.ot_settings_id());

  if ( empty( $settings['settings'] ) )
    return false;

  foreach( $settings['settings'] as $setting ) {
    if ( $setting['id'] == $id ) {

      return $setting;

    }

  }

}


    function ot_user_get_meta_label( $id,$echo ='' ) {
      if ( empty( $id ) )
        return false;

      $settings = get_option('rj_user_'.ot_settings_id());

      if ( empty( $settings['settings'] ) )
        return false;
      foreach( $settings['settings'] as $setting ) {

        if ( $setting['id'] == $id && isset( $setting['label'] ) ) {
            
            
            if($echo==1){
                echo $setting['label'];
            }else{
                return $setting['label'];
            }

        }

      }

    }
function ot_user_get_meta_desc( $id,$echo='' ) {

  if ( empty( $id ) )
    return false;

  $settings = get_option('rj_user_'.ot_settings_id());

  if ( empty( $settings['settings'] ) )
    return false;

  foreach( $settings['settings'] as $setting ) {

    if ( $setting['id'] == $id && isset( $setting['desc'] ) ) {

      if($echo==1){
                echo $setting['desc'];
            }else{
                return $setting['desc'];
            }

    }

  }

}

function ot_user_get_meta_data( $id ) {

  if ( empty( $id ) )
    return false;

  $settings = get_option('rj_user_'.ot_settings_id());

  if ( empty( $settings['settings'] ) )
    return false;

  foreach( $settings['settings'] as $setting ) {
    if ( $setting['id'] == $id ) {

      return $setting;

    }

  }

}


 function ot_taxonomy_get_meta_label( $id,$echo ='' ) {
      if ( empty( $id ) )
        return false;

      $settings = get_option('rj_taxonomy_'.ot_settings_id());

      if ( empty( $settings['settings'] ) )
        return false;
      foreach( $settings['settings'] as $setting ) {

        if ( $setting['id'] == $id && isset( $setting['label'] ) ) {
            
            
            if($echo==1){
                echo $setting['label'];
            }else{
                return $setting['label'];
            }

        }

      }

    }
function ot_taxonomy_get_meta_desc( $id,$echo='' ) {

  if ( empty( $id ) )
    return false;

  $settings = get_option('rj_taxonomy_'.ot_settings_id());

  if ( empty( $settings['settings'] ) )
    return false;

  foreach( $settings['settings'] as $setting ) {

    if ( $setting['id'] == $id && isset( $setting['desc'] ) ) {

      if($echo==1){
                echo $setting['desc'];
            }else{
                return $setting['desc'];
            }

    }

  }

}

function ot_taxonomy_get_meta_data( $id ) {

  if ( empty( $id ) )
    return false;

  $settings = get_option('rj_taxonomy_'.ot_settings_id());

  if ( empty( $settings['settings'] ) )
    return false;

  foreach( $settings['settings'] as $setting ) {
    if ( $setting['id'] == $id ) {

      return $setting;

    }

  }

}








   function rj_ot_import_callback() {
  $plugins_url = plugins_url();
  
   if ( 
   ! isset( $_POST['import_ot_meta_settings_nonce'] ) 
    || ! wp_verify_nonce( $_POST['import_ot_meta_settings_nonce'], 'import_ot_meta_settings_form' ) 
) {
	$import_success = '';
}else{
 $ot_meta_settings =  ot_decode( $_REQUEST['import_ot_meta_settings'] );
 $ot_meta_settings = unserialize($ot_meta_settings);
       
	update_option( 'rj'.ot_settings_id(),$ot_meta_settings );
	$import_success = '<p style="
  border: 1px solid #86B384;
  background: #A3C6A1;
  display: block;
  width: 62%;
  padding: 5px;
  color:#fff;">Imported Settings Successfully<p>';
}
  rj_ot_admin_styless();
    
    echo '<form method="post" id="import_ot_meta_settings_form">';
	echo '<h2>Import MetaboxUI Settings</h2>';
	echo $import_success;
      
      /* form nonce */
      wp_nonce_field( 'import_ot_meta_settings_form', 'import_ot_meta_settings_nonce' );
      
      /* format setting outer wrapper */
      echo '<div class="format-setting type-textarea has-desc">';
           
        /* description */
        echo '<div class="description">';
          
          echo '<p>' . __( 'To import your Settings copy and paste what appears to be a random string of alpha numeric characters into this textarea and press the "Import Settings" button.', 'option-tree' ) . '</p>';
          echo '<p>' . __( 'Please note this will replace all of your current settings.', 'option-tree' ) . '</p>';
          
         
          
        echo '</div>';
        
        /* textarea */
        echo '<div class="format-setting-inner">';
          
          echo '<textarea rows="10" cols="40" name="import_ot_meta_settings" id="import_ot_meta_settings" class="textarea"></textarea>'; /* button */
          echo '<button class="option-tree-ui-button button button-primary right hug-right">' . __( 'Import Settings', 'option-tree' ) . '</button>';

        echo '</div>';
        
      echo '</div>';
    
    echo '</form>';
	
	rj_user_ot_import_callback();
	rj_taxonomy_ot_import_callback();
       rj_widget_ot_import_callback();
    
  
   }
   
   function rj_ot_export_callback() {
  $plugins_url = plugins_url();
  
    rj_ot_admin_styless();
    /* format setting outer wrapper */
    echo '<div class="format-setting type-textarea simple has-desc">';
	echo '<h2>Export MetaboxUI Settings</h2>';
      
      /* description */
      echo '<div class="description">';
        
        echo '<p>' . __( 'Export your Settings by highlighting this text and doing a copy/paste into a blank .txt file. Then save the file for importing into another install of WordPress later. Alternatively, you could just paste it into the <code>Metaboxes->Import</code> <strong>Settings</strong> textarea on another web site.', 'option-tree' ) . '</p>';
        
      echo '</div>';
        
      /* get theme options data */
      $settings = get_option( 'rj'.ot_settings_id() );
      $settings = ! empty( $settings ) ?  ot_encode( serialize( $settings ) ) : '';
        
      echo '<div class="format-setting-inner">';
        echo '<textarea rows="10" cols="40" name="export_ot_meta_settings" id="export_ot_meta_settings" class="textarea">' . $settings . '</textarea>';
      echo '</div>';
      
    echo '</div>';
	
	rj_user_ot_export_callback();
    rj_taxonomy_ot_export_callback();
    rj_widget_ot_export_callback();
    
  
   }



 function rj_ot_documentation_callback() {
  $plugins_url = plugins_url();
    /* format setting outer wrapper */
    echo '<div class="format-setting type-textblock wide-desc">';
      
      /* description */
      echo '<div class="description">';
        
        echo '<h4>'. __( 'How-to-guide', 'rj-option-tree' ) . '</h4>';
        
        echo '<p>' . __( 'It work same as optionTree but also you can manage extra options such as templates and post types.', 'option-tree' ) . '</p>';
        echo '<p><img src="'.plugins_url( 'screenshot-1.png', __FILE__ ).'" /></p>';
		
		 echo '<p> &nbsp;</p>'; echo '<p> &nbsp;</p>';
		 
		 echo '<h4>'. __( 'MetaboxUI Usages', 'rj-option-tree' ) . '</h4>';
		 
		 echo '<p>' . __( '
			<code>Instead of get_post_meta($post_id,$var,true);  You can use &lt;?php ot_meta($var,[$echo=1]); ?&gt;</code>
		', 'option-tree' ) . '</p>';
		
		 echo '<p>' . __( '
			<code>To get image by variable  &lt;?php ot_meta_attachment($var,[$echo=1]); ?&gt;. It is useful for field "Attachement ID"</code>
		', 'option-tree' ) . '</p>
		';
		
		echo '<p>' . __( '
			<code>To get image by variable  &lt;?php ot_meta_image($var,[$echo=1,$size]); ?&gt;. It is useful for field "Attachement ID"</code>
		', 'option-tree' ) . '</p>
		';
		
		 echo '<p>' . __( '
			<code>To get image by attachment id  &lt;?php  ot_meta_attachment_by_id($var,$echo=\'\',$defaultVal=\'\'); ?&gt;</code>
		', 'option-tree' ) . '</p>';
		
		echo '<p>' . __( '
			<code>To get image by attachment id  &lt;?php  ot_meta_image_by_id($var,$echo=\'\',$defaultVal=\'\',$size= \'full\'); ?&gt;</code>
		', 'option-tree' ) . '</p>';
		
		
		// User metabox
		 echo '<p>&nbsp; </p>'; echo '<p>&nbsp; </p>';
		echo '<h4>'. __( 'User MetaboxUI Usages', 'rj-option-tree' ) . '</h4>';
		 echo '<p>' . __( '
			<code>To get user meta  You can use &lt;?php ot_user_meta($var,[$echo=1]); ?&gt;</code>
		', 'option-tree' ) . '</p>';
		
		 echo '<p>' . __( '
			<code>To get user meta by id  You can use &lt;?php ot_user_meta_by_uid($user_id,$var,[$echo=1]); ?&gt;</code>
		', 'option-tree' ) . '</p>';
		
		 echo '<p>' . __( '
			<code>To get image by variable  &lt;?php ot_user_meta_attachment($var,[$echo=1]); ?&gt;. It is useful for field "Attachement ID"</code>
		', 'option-tree' ) . '</p>
		';
		
		echo '<p>' . __( '
			<code>To get image by variable  &lt;?php ot_user_meta_image($var,[$echo=1,$size]); ?&gt;. It is useful for field "Attachement ID"</code>
		', 'option-tree' ) . '</p>
		';
		
		
		 echo '<p>' . __( '
			<code>To get image by attachment id  &lt;?php  ot_user_meta_attachment_by_id($var,$echo=\'\',$defaultVal=\'\'); ?&gt;</code>
		', 'option-tree' ) . '</p>';
		
		echo '<p>' . __( '
			<code>To get image by attachment id  &lt;?php  ot_user_meta_image_by_id($var,$echo=\'\',$defaultVal=\'\',$size= \'full\'); ?&gt;</code>
		', 'option-tree' ) . '</p>';
		
		
		// Taxonomy metabox
		 echo '<p>&nbsp; </p>'; echo '<p>&nbsp; </p>';
		echo '<h4>'. __( 'Taxonomy MetaboxUI Usages', 'rj-option-tree' ) . '</h4>';
		 echo '<p>' . __( '
			<code>To get a taxonomy meta value  You can use &lt;?php ot_taxonomy_meta($term_id,$var,[$echo=1]); ?&gt;</code>
		', 'option-tree' ) . '</p>';
		 echo '<p>' . __( '
			<code>To get all taxonomy meta values  You can use &lt;?php ot_taxonomy($term_id,[$echo=1]); ?&gt;</code>
		', 'option-tree' ) . '</p>';
		
		
		// for optiontree
		 echo '<p>&nbsp; </p>'; echo '<p>&nbsp; </p>';
		 echo '<h4>'. __( 'Usages for getting values from Theme options (Some handy functions)', 'rj-option-tree' ) . '</h4>';
		
		 echo '<p>' . __( '
			<code>For theme options instead of adding writing long codes, you can use &lt;?php ot($var,[$echo=1,$default_value]); ?></code>
		', 'option-tree' ) . '</p>';
		
		 echo '<p>' . __( '
			<code>To get image by variable for theme options  &lt;?php ot_attachment($var,[$echo=1]); ?&gt;. It is useful for field "Attachement ID"</code>
		', 'option-tree' ) . '</p>
		';
		
		echo '<p>' . __( '
			<code>To get image by variable for theme options  &lt;?php ot_image($var,[$echo=1,$size]); ?&gt;. It is useful for field "Attachement ID"</code>
		', 'option-tree' ) . '</p>
		';
		
		
		 echo '<p>' . __( '
			<code>To get image by attachment id for theme options  &lt;?php  ot_attachment_by_id($var,$echo=\'\',$defaultVal=\'\'); ?&gt;</code>
		', 'option-tree' ) . '</p>';
		
		echo '<p>' . __( '
			<code>To get image by attachment id for theme options  &lt;?php  ot_image_by_id($var,$echo=\'\',$defaultVal=\'\',$size= \'full\'); ?&gt;</code>
		', 'option-tree' ) . '</p>';
		
		
		 echo '<p>&nbsp; </p>'; echo '<p>&nbsp; </p>';
		 echo '<h4>'. __( 'Some extra handy functions for wordpress templating', 'rj-option-tree' ) . '</h4>';
        
        echo '<p>' . __( '
			<code>&lt;?php base_url([$echo=1]); ?&gt; shortcode [base_url] as same as bloginfo(\'url\')</code>
		', 'option-tree' ) . '</p>';
		 echo '<p>' . __( '
			<code>  &lt;?php temp_url([$echo=1]); ?> shortcode [temp_url] as same as bloginfo(\'template_url\')</code>
		', 'option-tree' ) . '</p>';
     
		 echo '<p>' . __( '
			<code>  &lt;?php page_link(array("id"=>18,"echo"=>1)); ?&gt; shortcode [page_link id=18 echo=1] as same as get_permalink(18)</code>
		', 'option-tree' ) . '</p>';
     
     
     
     // to get settings
      echo '<p>&nbsp; </p>'; echo '<p>&nbsp; </p>';
		 echo '<h4>'. __( 'To get settings info', 'rj-option-tree' ) . '</h4>';
     
     echo '<p>' . __( '
			<code>  &lt;?php ot_get_settings_label($id); ?&gt; to get the label of a settings</code>
		', 'option-tree' ) . '</p>';
     
echo '<p>' . __( '
			<code>  &lt;?php ot_get_settings_desc($id); ?&gt; to get the description of a settings</code>
		', 'option-tree' ) . '</p>';
     
echo '<p>' . __( '
			<code>  &lt;?php ot_get_settings_data($id); ?&gt; to get the all settings data of a settings</code>
		', 'option-tree' ) . '</p>';
        
echo '<p>' . __( '
			<code>  &lt;?php ot_get_meta_label($id); ?&gt; to get the metabox label of a settings</code>
		', 'option-tree' ) . '</p>';
     
echo '<p>' . __( '
			<code>  &lt;?php ot_get_meta_desc($id); ?&gt; to get the metabox description of a settings</code>
		', 'option-tree' ) . '</p>';
     
echo '<p>' . __( '
			<code>  &lt;?php ot_get_meta_data($id); ?&gt; to get the all metabox data of a settings</code>
		', 'option-tree' ) . '</p>';

        
echo '<p>' . __( '
			<code>  &lt;?php ot_user_get_meta_label($id); ?&gt; to get the user metabox label of a settings</code>
		', 'option-tree' ) . '</p>';
     
echo '<p>' . __( '
			<code>  &lt;?php ot_user_get_meta_desc($id); ?&gt; to get the user metabox description of a settings</code>
		', 'option-tree' ) . '</p>';

echo '<p>' . __( '
			<code>  &lt;?php ot_user_get_meta_data($id); ?&gt; to get the all user metabox data of a settings</code>
		', 'option-tree' ) . '</p>';

        
echo '<p>' . __( '
			<code>  &lt;?php ot_taxonomy_get_meta_label($id); ?&gt; to get the taxonomy metabox label of a settings</code>
		', 'option-tree' ) . '</p>';
     
echo '<p>' . __( '
			<code>  &lt;?php ot_taxonomy_get_meta_desc($id); ?&gt; to get the taxonomy metabox description of a settings</code>
		', 'option-tree' ) . '</p>';
     
echo '<p>' . __( '
			<code>  &lt;?php ot_taxonomy_get_meta_data($id); ?&gt; to get the all taxonomy metabox data of a settings</code>
		', 'option-tree' ) . '</p>';
     
     
 // Shortcodes
      echo '<p>&nbsp; </p>'; echo '<p>&nbsp; </p>';
		 echo '<h4>'. __( 'Shortcodes', 'rj-option-tree' ) . '</h4>';
     
     echo '<p>' . __( '
			<code>  [ot_short id="field_id" type="image"] shortcode to get optiontree settings field</code>
		', 'option-tree' ) . '</p>';
     
     echo '<p>' . __( '
			<code>  [ot_image_short id="field_id"] shortcode to get optiontree settings image field</code>
		', 'option-tree' ) . '</p>';
     
     echo '<p>' . __( '
			<code>  [ot_meta_short id="field_id" type="image"] shortcode to get optiontree meta value</code>
		', 'option-tree' ) . '</p>';
     
     echo '<p>' . __( '
			<code>  [ot_meta_image_short id="field_id"] shortcode to get meta value image url</code>
		', 'option-tree' ) . '</p>';
     
    // Shortcodes
      echo '<p>&nbsp; </p>'; echo '<p>&nbsp; </p>';
		 echo '<h4>'. __( 'Using Widget Metabox', 'rj-option-tree' ) . '</h4>';  echo '<p>' . __( '<code><pre>
            //Copy this code in your functions.php or plugins file to design your widget
                function wp_widget_display_hook_{your_widget_id}($args,$instance){
                extract($args, EXTR_SKIP);
                echo $before_widget;
                
                $title = empty($instance["title"]) ? " " : apply_filters("widget_title", $instance["title"]);
                // WIDGET CODE GOES HERE
                echo "&lt;h1&gt;$title&lt;/h1&gt;";
                echo "&lt;p&gt;This is my new widget! Edit your html here. &lt;/p&gt;";
                echo $after_widget;
                }
                add_action("wp_widget_design_{your_widget_id}","wp_widget_display_hook_{your_widget_id}",10,2);
                
                // Thats all!
                // Happy editing...
                // Before pasting code replace {your_widget_id} with your widget id
                // Please note that, you are seeing this beacuse you are logged in as administator
                </pre></code>
		', 'option-tree' ) . '</p>';
     
     
		 
		 
        
        
        
      echo '</div>';
      
    echo '</div>
    
    
    
    
    ';
     ?>
     <form name="seamless-donations-form" action="https://www.paypal.com/cgi-bin/webscr" method="post">
  <div class="seamless-donations-forms-error-message" style="display:none"></div>
  <div id="dgx-donate-container">
    <div id="dgx-donate-form-donation-section" class="dgx-donate-form-section">
      <div id="donation_header">
        <div id="donation_header-error-message" style="display:none" class="seamless-donations-error-message-field"></div>
        <h2>Donation Information</h2>
      </div>
      <div id="header_desc">
        <div id="header_desc-error-message" style="display:none" class="seamless-donations-error-message-field"></div>
        <p>I would like to make a donation in the amount of:</p>
      </div>
      <span id="dgx_donate_giving_level_1000"><span id="dgx_donate_giving_level_1000-error-message" style="display:none" class="seamless-donations-error-message-field"></span>
      <input type="radio" name="_dgx_donate_amount" checked="" value="1000"  onclick="document.querySelector('#anmdna2').value='1000';document.querySelector('#anmdna').value='1000';" data-conceal=".other-donation-level">
      $1,000</span><span id="dgx_donate_giving_level_500" class="horiz"><span id="dgx_donate_giving_level_500-error-message" style="display:none" class="seamless-donations-error-message-field"></span>
      <input type="radio" name="_dgx_donate_amount" value="500"   onclick="document.querySelector('#anmdna').value='500';document.querySelector('#anmdna2').value='500';" data-conceal=".other-donation-level">
      $500</span><span id="dgx_donate_giving_level_100" class="horiz"><span id="dgx_donate_giving_level_100-error-message" style="display:none" class="seamless-donations-error-message-field"></span>
      <input type="radio" name="_dgx_donate_amount" value="100"   onclick="document.querySelector('#anmdna').value='100';document.querySelector('#anmdna2').value='100';" data-conceal=".other-donation-level">
      $100</span><span id="dgx_donate_giving_level_50" class="horiz"><span id="dgx_donate_giving_level_50-error-message" style="display:none" class="seamless-donations-error-message-field"></span>
      <input type="radio" name="_dgx_donate_amount" value="50"  onclick="document.querySelector('#anmdna').value='50';document.querySelector('#anmdna2').value='50';" data-conceal=".other-donation-level">
      $50</span><span id="other_radio_button" class="horiz"><span id="other_radio_button-error-message" style="display:none" class="seamless-donations-error-message-field"></span>
      <input type="radio" name="_dgx_donate_amount" value="OTHER" onclick="document.querySelector('#_dgx_donate_user_amount').style.display='block'" id="dgx-donate-other-radio" data-reveal=".other-donation-level">
      Other</span>
      <div id="_dgx_donate_user_amount" class="aftertext other-donation-level" style="display:none">
        <div id="_dgx_donate_user_amount-error-message" style="display:none" class="seamless-donations-error-message-field"></div>
        Other:
        <input type="text" name="_dgx_donate_user_amount" onkeyup="document.querySelector('#anmdna').value=this.value;document.querySelector('#anmdna2').value=this.value;" value="" size="15" id="_dgx_donate_user_amount" data-validate="currency">
      </div>
      <div>
        <div id="_dgx_donate_repeating">
          <div id="_dgx_donate_repeating-error-message" style="display:none" class="seamless-donations-error-message-field"></div>
          <p>
            <input type="checkbox" name="_dgx_donate_repeating" id="dgx-donate-repeating" onclick="document.querySelector('#donation_astype').value='_xclick-subscriptions'">
            I would like this donation to automatically repeat each month</p>
        </div>
      </div>
    </div>
    <div></div>
    <div id="dgx-donate-form-paypal-hidden-section" class="dgx-donate-form-section" style="display:none">
      <div id="cmd">
        <div id="cmd-error-message" style="display:none" class="seamless-donations-error-message-field"></div>
        <input type="hidden" name="cmd" id="donation_astype" value="_donations">
      </div>
      <div id="business">
        <div id="business-error-message" style="display:none" class="seamless-donations-error-message-field"></div>
        <input type="hidden" name="business" value="rajilesh@outlook.com">
      </div>
      <div id="return">
        <div id="return-error-message" style="display:none" class="seamless-donations-error-message-field"></div>
        <input type="hidden" name="return" value="http://localhost/wordpress/?page_id=28&amp;thanks=1&amp;sessionid=sojguvpni55imjngd251sd5s12">
      </div>
      <div id="first_name">
        <div id="first_name-error-message" style="display:none" class="seamless-donations-error-message-field"></div>
        <input type="hidden" name="first_name" value="Asd">
      </div>
      <div id="last_name">
        <div id="last_name-error-message" style="display:none" class="seamless-donations-error-message-field"></div>
        <input type="hidden" name="last_name" value="asd">
      </div>
      <div id="address1">
        <div id="address1-error-message" style="display:none" class="seamless-donations-error-message-field"></div>
        <input type="hidden" name="address1" value="">
      </div>
      <div id="address2">
        <div id="address2-error-message" style="display:none" class="seamless-donations-error-message-field"></div>
        <input type="hidden" name="address2" value="">
      </div>
      <div id="city">
        <div id="city-error-message" style="display:none" class="seamless-donations-error-message-field"></div>
        <input type="hidden" name="city" value="">
      </div>
      <div id="state">
        <div id="state-error-message" style="display:none" class="seamless-donations-error-message-field"></div>
        <input type="hidden" name="state" value="">
      </div>
      <div id="zip">
        <div id="zip-error-message" style="display:none" class="seamless-donations-error-message-field"></div>
        <input type="hidden" name="zip" value="">
      </div>
      <div id="country">
        <div id="country-error-message" style="display:none" class="seamless-donations-error-message-field"></div>
        <input type="hidden" name="country" value="">
      </div>
      <div id="email">
        <div id="email-error-message" style="display:none" class="seamless-donations-error-message-field"></div>
        <input type="hidden" name="email" value="<?php bloginfo('admin_email') ?>">
      </div>
      <div id="custom">
        <div id="custom-error-message" style="display:none" class="seamless-donations-error-message-field"></div>
        <input type="hidden" name="custom" value="dgxdonate_sojguvpni5_1439312765">
      </div>
      <div id="notify_url">
        <div id="notify_url-error-message" style="display:none" class="seamless-donations-error-message-field"></div>
        <input type="hidden" name="notify_url" value="http://localhost/wordpress/wp-content/plugins/seamless-donations/dgx-donate-paypalstd-ipn.php">
      </div>
      <div id="item_name">
        <div id="item_name-error-message" style="display:none" class="seamless-donations-error-message-field"></div>
        <input type="hidden" name="item_name" value="Donation">
      </div>
      <div id="amount">
        <div id="amount-error-message" style="display:none" class="seamless-donations-error-message-field"></div>
        <input type="hidden" name="amount" id="anmdna2" value="1000">
      </div>
      <div id="quantity">
        <div id="quantity-error-message" style="display:none" class="seamless-donations-error-message-field"></div>
        <input type="hidden" name="quantity" value="1">
      </div>
      <div id="currency_code">
        <div id="currency_code-error-message" style="display:none" class="seamless-donations-error-message-field"></div>
        <input type="hidden" name="currency_code" value="USD">
      </div>
      <div id="no_note">
        <div id="no_note-error-message" style="display:none" class="seamless-donations-error-message-field"></div>
        <input type="hidden" name="no_note" value="1">
      </div>
      <div id="src">
        <div id="src-error-message" style="display:none" class="seamless-donations-error-message-field"></div>
        <input type="hidden" name="src" value="1">
      </div>
      <div id="p3">
        <div id="p3-error-message" style="display:none" class="seamless-donations-error-message-field"></div>
        <input type="hidden" name="p3" value="1">
      </div>
      <div id="t3">
        <div id="t3-error-message" style="display:none" class="seamless-donations-error-message-field"></div>
        <input type="hidden" name="t3" value="M">
      </div>
      <div id="a3">
        <div id="a3-error-message" style="display:none" class="seamless-donations-error-message-field"></div>
        <input type="hidden" name="a3" id="anmdna" value="1000">
      </div>
    </div>
    <div id="dgx-donate-form-payment-section" class="dgx-donate-form-section">
      <div id="dgx-donate-pay-enabled" class="dgx-donate-pay-enabled">
        <div id="dgx-donate-pay-enabled-error-message" style="display:none" class="seamless-donations-error-message-field"></div>
        <input type="image" name="dgx-donate-pay-enabled" value="Donate Now" src="http://localhost/wordpress/wp-content/plugins/seamless-donations/images/paypal_btn_donate_lg.gif">
      </div>
      <div id="dgx-donate-pay-disabled" class="dgx-donate-pay-disabled" style="display: none;">
        <div id="dgx-donate-pay-disabled-error-message" style="display:none" class="seamless-donations-error-message-field"></div>
        <input type="image" name="dgx-donate-pay-disabled" value="" src="http://localhost/wordpress/wp-content/plugins/seamless-donations/images/paypal_btn_donate_lg_disabled.gif">
      </div>
      <div id="dgx-donate-busy" class="dgx-donate-busy" style="display: none;">
        <div id="dgx-donate-busy-error-message" style="display:none" class="seamless-donations-error-message-field"></div>
        <input type="image" name="dgx-donate-busy" value="" src="http://localhost/wordpress/wp-content/plugins/seamless-donations/images/ajax-loader.gif">
      </div>
    </div>
  </div>
</form>

     <?php
  
  }


  if(!function_exists("ot_settings_id")){
    function ot_settings_id(){
         return apply_filters( 'rj_ot_settings_id', 'option_tree_settings' );
    }
}
