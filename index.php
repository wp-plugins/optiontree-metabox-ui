<?php
/*
Plugin Name: MetaboxUI
Plugin URI: http://rajilesh.in/metaboxui-for-optiontree
Description: This is for developers who love optiontree
Author: Rajilesh Panoli
Version: 1.6
Author URI: http://rajilesh.in
*/
	
include_once( ABSPATH . 'wp-admin/includes/plugin.php' );	
if(!is_plugin_active('option-tree/ot-loader.php')){
	deactivate_plugins( plugin_basename( __FILE__ ) );
			wp_die( 'This plugin requires <a href="https://wordpress.org/plugins/option-tree/">Optiontree</a> plugin' );
			die();
}
add_action( 'admin_init', 'rj_ot_save_settings', 6 );

function rj_ot_save_settings(){
	if(isset($_REQUEST['page']) && $_REQUEST['page']=='rj_ot-settings'){
	  ot_admin_styles();
	  ot_admin_scripts();
	  
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
	
	
	
	
    
  }
  
}	




if ( ! function_exists( 'ot_sections_view' ) ) {

  function ot_sections_view( $name, $key, $section = array() ) {
	  //if(isset($_GET['page']) and $_GET['page']=='rj_ot-settings'){
	  if(1==1){
	$post_types = get_post_types('', 'names'  );
	
	
	$section['page'] = is_array($section['page']) ? $section['page'] : array();
	$section['pages'] = is_array($section['pages']) ? $section['pages'] : array();
	$section['template'] = is_array($section['template']) ? $section['template'] : array();
	
	
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
   
   
   $page_field ='<div class="format-settings">
          <div class="format-setting type-text">
            <div class="description">' . __( '<strong>Page</strong>: Specify a page that you want to display this metabox. Seperated by comma', 'option-tree' ) . '</div>
            <div class="format-setting-inner">
             '.$all_pages_template.'
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
		
		 <div class="format-settings">
          <div class="format-setting type-text">
            <div class="description">' . __( '<strong>Post type</strong>:  Specify a post type that you want to display this metabox. Seperated by comma', 'option-tree' ) . '</div>
            <div class="format-setting-inner">
             '. $post_type_template.'
            </div>
          </div>
        </div>
		
		 <div class="format-settings">
          <div class="format-setting type-text">
            <div class="description">' . __( '<strong>Template</strong>: Specify a template that you want to display this metabox. Seperated by comma', 'option-tree' ) . '</div>
            <div class="format-setting-inner">
             '.$page_template.'
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


add_action( 'admin_menu', 'register_my_custom_menu_page' );

function register_my_custom_menu_page(){
    add_menu_page('Metaboxes', 'Metaboxes', 'manage_options', 'rj_ot-settings', 'rj_ot_type_theme_options_ui','', 99 );
}

add_action( 'admin_init', 'rj_ot_show_settings');
function rj_ot_show_settings(){
	$meta_options = get_option( 'rj'.ot_settings_id(), $settings );
	
	
	
	
	
	//print_r($meta_options);
  
 // print_r($new_meta_boadx);
  //print_r($meta_options);
	
	
	if(!empty($meta_options['sections'])){
		$mi=0;
		foreach($meta_options['sections'] as $meta_option){
			$post_id = ($_REQUEST['post']) ?  $_REQUEST['post'] : $_REQUEST['post_ID'];
			
			$post_template = get_post_meta($post_id,'_wp_page_template',true);
			$post_template = ($post_template || $post_template !='') ? $post_template : 'default';
			$new_meta_box =array();
			$settings =array();
			//$template_array = explode(',',$meta_option['template']);
			
			$pages_arr = is_array($meta_option['page']) ? $meta_option['page'] :array();
			
			$template_array = is_array($meta_option['template']) ? $meta_option['template'] : array();
			
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
		echo get_bloginfo('template_url').'/';
	}else{
		return get_bloginfo('template_url').'/';
	}
}
add_shortcode('temp_url','temp_url');
}


function rj_add_custom_option_types( $types ) {

  $types['rj_upload_media'] = 'Upload Media';
  $types['rj_upload_attach_id'] = 'Inert Attachment';

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



/* End of file ot-functions-option-types.php */
/* Location: ./includes/ot-functions-option-types.php */



/* load the Meta Box assets */
      if ( OT_META_BOXES == true ) {
      
        /* add scripts for metaboxes to post-new.php & post.php */
        add_action( 'admin_print_scripts-post-new.php', 'rj_ot_admin_scripts', 11 );
        add_action( 'admin_print_scripts-post.php', 'rj_ot_admin_scripts', 11 );
		
		
        /* add styles for metaboxes to post-new.php & post.php */
        add_action( 'admin_print_styles-post-new.php', 'rj_ot_admin_styles', 11 );
        add_action( 'admin_print_styles-post.php', 'rj_ot_admin_styles', 11 );
      
      }


 function rj_ot_admin_scripts() {
	  wp_register_script( 'rj-metaboxui', plugin_dir_url( __FILE__ ) . 'js/metabox-ui.js' );
	 /* load jQuery UI timepicker addon */
    wp_enqueue_script( 'rj-metaboxui');
 }
 
 function rj_ot_admin_styles(){
	 wp_enqueue_style( 'rj-metaboxui',  plugin_dir_url( __FILE__ ) . 'css/metabox-ui.css' );
 }