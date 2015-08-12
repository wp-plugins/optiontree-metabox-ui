<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
	
include_once( 'rj-ot-widget-meta-box-api.php' );	

add_action( 'admin_init', 'rj_widget_ot_save_settings', 6 );




function rj_widget_ot_save_settings(){

	if(isset($_REQUEST['page']) && $_REQUEST['page']=='rj-ot-widget_metabox'){

	  rj_ot_admin_scriptss();

	  rj_ot_admin_styless();

	  

    wp_enqueue_script( 'rj_ot_widget_script', plugin_dir_url(dirname(__FILE__) ) . 'js/widget-metabox-ui-settings.js' );
}

	  




    /* check and verify import settings nonce */

    if ( isset( $_POST['option_tree_settings_nonce'] ) && wp_verify_nonce( $_POST['option_tree_settings_nonce'], 'rj_option_tree_settings_form' ) && isset($_GET['page']) && $_GET['page']=='rj-ot-widget_metabox' ) {



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

		

       update_option( 'rj_widget_'.ot_settings_id(), $settings );

        $message = 'success';

        

      }

      

      /* redirect */

      wp_redirect( add_query_arg( array( 'action' => 'save-settings', 'message' => $message ), $_POST['_wp_http_referer'] ) );

      exit;

      

    }

    

    return false;



  

}






	




















if ( ! function_exists( 'rj_widget_ot_metabox_callback' ) ) {

  

  function rj_widget_ot_metabox_callback() {

    global $blog_id;

    

    echo '

	<h2>Widget Metaboxes UI</h2>

	<form method="post" id="option-tree-settings-form">';

      

      /* form nonce */

      wp_nonce_field( 'rj_option_tree_settings_form', 'option_tree_settings_nonce' );

      

      /* format setting outer wrapper */

      echo '<div class="format-setting type-textblock has-desc">';

        

       

        

        /* get the saved settings */

        $settings = get_option(  'rj_widget_'.ot_settings_id() );



        /* wrap settings array */

        echo '<div class="format-setting-inner">';

          

          /* set count to zero */

          $count = 0;

  

          /* loop through each section and its settings */

          echo '<ul class="option-tree-setting-wrap option-tree-sortable" id="option_tree_settings_list" data-name="' . ot_settings_id() . '[settings]">';

          

          if ( isset( $settings['sections'] ) ) {

          

            foreach( $settings['sections'] as $section ) {

              

              /* section */

              echo '<li class="' . ( $count == 0 ? 'ui-state-disabled' : 'ui-state-default' ) . ' list-section">' . ot_widget_sections_view( ot_settings_id() . '[sections]', $count, $section ) . '

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

  

}


if ( ! function_exists( 'ot_widget_sections_view' ) ) {



  function ot_widget_sections_view( $name, $key, $section = array() ) {
      
    
	  //if(isset($_GET['page']) and $_GET['page']=='rj_ot-settings'){

	  if(1==1){

	

          $default_widget_template = '{before_widget} <div class="your_class_name"> {before_title}{title} {after_title}
</div> {after_widget}';

          
          
	$section['taxonomies'] = (isset($section['taxonomies']) && is_array($section['taxonomies'])) ? $section['taxonomies'] : array();

	

	

	

	

	



   

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

		 <div class="format-settings widget_template_section_field">

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

{image_to_url}
<br /><br />
&lt;/div&gt;<br />
            {after_widget}</code>
            <br />
            <br />
            Usage : {your_field_id} will replace with value of "your_field_id"
            <br />
            <br />
            To write a loop 
            <br />
            {loop:your_repeating_field_id}
            <br />
            // inside of this space, you can call sub fields of this field like {title}
            <br />
            {endloop:your_repeating_field_id}
            <br /><br />
            To Convert attachment ID or to url <br />
            Use "_to_url" suffix<br />
            Eg: {image_to_url} this will convert {image}  to url
            
            
            ', 'option-tree' ) . '</div>

            <div class="format-setting-inner">

            <textarea type="text" name="' . esc_attr( $name ) . '[' . esc_attr( $key ) . '][widget_template]" value="" class="widefat option-tree-ui-input section-id" rows=10>' . ( isset( $section['widget_template'] ) ? esc_attr( $section['widget_template'] ) : $default_widget_template ) . '</textarea>

            </div>

          </div>

        </div>

		

		

		

		

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





add_action( 'widgets_init', 'rj_widget_ot_show_settings');
add_action( 'wp', 'rj_widget_ot_show_settings');
function rj_widget_ot_show_settings(){
	
	$meta_options = get_option( 'rj_widget_'.ot_settings_id() );

	$post_id = (isset($post_id)) ? $post_id : '';

	

	

	

	

	//print_r($meta_options);

  

 // print_r($new_meta_boadx);

  //print_r($meta_options);

	

	

	if(!empty($meta_options['sections'])){
		


		$mi=0;
		foreach($meta_options['sections'] as $meta_option){

			
            $taxonomies = (isset( $meta_option['taxonomies'])) ?  $meta_option['taxonomies'] : array();
            $widget_template = (isset( $meta_option['widget_template'])) ?  $meta_option['widget_template'] : "";

			$new_meta_box =array();

			$settings =array();

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

		'pages'       => "",

		'context'     => 'normal',

		'priority'    => 'high',
        'taxonomies'  =>$taxonomies,
        'widget_template'  =>$widget_template,

		'fields'      => $settings

	  );


		rj_ot_widget_register_meta_box( $new_meta_box[$mi] );

		$mi++;


			

		

		}
        
        
   

	}
}





//usage












 




   function rj_widget_ot_import_callback() {
  $plugins_url = plugins_url();
  
   if ( 
   ! isset( $_POST['import_widget_ot_meta_settings_nonce'] ) 
    || ! wp_verify_nonce( $_POST['import_widget_ot_meta_settings_nonce'], 'import_widget_ot_meta_settings_form' ) 
) {
	$import_success = '';
}else{
	 $ot_meta_settings =  ot_decode( $_REQUEST['import_widget_ot_meta_settings'] );
	 $ot_meta_settings = unserialize($ot_meta_settings);
     
    
        
          
		update_option(  'rj_widget_'.ot_settings_id(),$ot_meta_settings );
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
	echo '<h2>Import Widget MetaboxUI Settings</h2>';
	echo $import_success;
      
      /* form nonce */
      wp_nonce_field( 'import_widget_ot_meta_settings_form', 'import_widget_ot_meta_settings_nonce' );
      
      /* format setting outer wrapper */
      echo '<div class="format-setting type-textarea has-desc">';
           
        /* description */
        echo '<div class="description">';
          
          echo '<p>' . __( 'To import your Settings copy and paste what appears to be a random string of alpha numeric characters into this textarea and press the "Import Settings" button.', 'option-tree' ) . '</p>';
          echo '<p>' . __( 'Please note this will replace all of your current settings.', 'option-tree' ) . '</p>';
          
         
          
        echo '</div>';
        
        /* textarea */
        echo '<div class="format-setting-inner">';
          
          echo '<textarea rows="10" cols="40" name="import_widget_ot_meta_settings" id="import_widget_ot_meta_settings" class="textarea"></textarea>'; /* button */
          echo '<button class="option-tree-ui-button button button-primary right hug-right">' . __( 'Import Settings', 'option-tree' ) . '</button>';

        echo '</div>';
        
      echo '</div>';
    
    echo '</form>';
    
  
   }
   
   function rj_widget_ot_export_callback() {
  $plugins_url = plugins_url();
  
    rj_ot_admin_styless();
    /* format setting outer wrapper */
    echo '<div class="format-setting type-textarea simple has-desc">';
	echo '<h2>Export Widget MetaboxUI Settings</h2>';
      
      /* description */
      echo '<div class="description">';
        
        echo '<p>' . __( 'Export your Settings by highlighting this text and doing a copy/paste into a blank .txt file. Then save the file for importing into another install of WordPress later. Alternatively, you could just paste it into the <code>Metaboxes->Import</code> <strong>Settings</strong> textarea on another web site.', 'option-tree' ) . '</p>';
        
      echo '</div>';
        
      /* get theme options data */
      $settings = get_option( 'rj_widget_'.ot_settings_id() );
      $settings = ! empty( $settings ) ?  ot_encode( serialize( $settings ) ) : '';
        
      echo '<div class="format-setting-inner">';
        echo '<textarea rows="10" cols="40" name="export_user_ot_meta_settings" id="export_user_ot_meta_settings" class="textarea">' . $settings . '</textarea>';
      echo '</div>';
      
    echo '</div>';
    
  
   }

