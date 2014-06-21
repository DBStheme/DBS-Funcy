<?php 

class DBSfuncy {

	public function __construct() {
		add_action('save_post', array( $this, 'MetaBoxSave' ));
	}

	public function PrintOption( $id, $echo = true ) {
		if ( get_option('DBSfuncy') ) {
			$DBSfuncyOption = get_option("DBSfuncy");
			if( isset($DBSfuncyOption[$id]) ) {
				if( $echo == true ){
					echo $DBSfuncyOption[$id];
				} else {
					return $DBSfuncyOption[$id];
				}
			}
			unset($DBSfuncyOption);
		}
	}

	public function HTMLCharset( $charset = "utf-8", $HTML5 = true ) {
		if( $HTML5 ) {
			echo '<meta charset="' . $charset . '" />' . "\n";
		} else {
			echo '<meta http-equiv="Content-Type" content="text/html; charset=' . $charset . '" />' . "\n";
		}
	}

	public function ThemeAddress() {
		return get_template_directory_uri();
	}

	public function FastMenu( $id = null, $depth = null ) {
		if( !$depth ){
			wp_nav_menu(array('theme_location' => $id));
		} else {
			wp_nav_menu(array('theme_location' => $id, "depth" => $depth));
		}
	}

	public function FastMenuRegister( $id = null, $des=null ) {
		register_nav_menu($id, $des);
	}

	public function DirectCSS( $file = null ) {
		if($file) {
			if( strpos( $file, '//' ) !== false ) {
				echo '<link rel="stylesheet" type="text/css" media="all" href="' . $file . '" />' . "\n";
			} else {
				echo '<link rel="stylesheet" type="text/css" media="all" href="' . $this -> ThemeAddress() . '/' . $file . '" />' . "\n";
			}
		} else {
			echo '<link rel="stylesheet" type="text/css" media="all" href="' . $this -> ThemeAddress() . '/style.css" />' . "\n";
		}
	}

	public function DirectJS( $file = null ) {
		if( strpos( $file, '//' ) !== false ) {
			echo '<script type="text/javascript" src="' . $file . '"></script>' . "\n";
		} else {
			echo '<script type="text/javascript" src="' . $this -> ThemeAddress() . '/' . $file . '"></script>' . "\n";
		}
	}

	public function AttachJS( $name = null, $file = null, $footer = false ) {
		if(!$file) {
			wp_enqueue_script( $name );
		} else {
			if(!$footer) {
				wp_enqueue_script( $name, $this -> ThemeAddress() . '/' . $file . '.js' );
			} else {
				wp_enqueue_script( $name, $this -> ThemeAddress() . '/' . $file . '.js', $deps = array(), $ver = false, $in_footer = true );
			}
		}
	}

	public function AddAdminMenuItem( $name = null ) {
	}

	public function ThumbnailAddress( $id = false, $echo = true, $size = "fullsize" ) {
		global $post;
		if( !$id ){
			$thumb = wp_get_attachment_image_src( get_post_thumbnail_id($post->ID), $size );
		} else {
			$thumb = wp_get_attachment_image_src( get_post_thumbnail_id($id), $size );
		}
		if( $echo = true ){
			echo $thumb['0'];
		} else {
			return $thumb['0'];
		}
	}

	public function OptionsPanel( $items = array() ) {
		# Save Part
		if( isset($_POST['optionspost']) && $_POST['optionspost'] <> '' ){
			update_option( 'DBSfuncy', $_POST);
			$saved = true;
		}
		# Form Part
		echo '
			<form method="post">
			<input type="hidden" name="optionspost" value="true" />
		';
		if( isset($saved) && $saved === true ) {
			echo '<div id="message" class="updated" style="margin: 30px 0;"><p>Settings Updated!</p></div>';
		}
		foreach( $items as $item => $array ) {

			if( isset($array['type']) && $array['type'] == 'headline' ){
			} else {
				echo '<label for="' . $item . '" style="margin: 10px 0;display: block;">' . $array['name'] . '</label>';
			}

			if( isset($array['type']) && $array['type'] == 'catselect' ) {
				echo '<div style="display: block;clear:both;width: 100%;">';
				wp_dropdown_categories(array('name'=>$item));
				echo '</div>';
			} elseif( isset($array['type']) && $array['type'] == 'textarea' ) {
				echo '<textarea name="' . $item . '" style="margin: 10px 0;display: block;width: 25em;height: 15em;">' . $this -> PrintOption($item, false) . '</textarea>';
			} elseif (isset($array['type']) && $array['type'] == 'headline' ) {
				echo '<h3 style="margin: 50px 0;padding-bottom:15px;border-bottom: 2px solid #333;">' . $array['name'] . '</h4>';
			} else {
				echo '<input type="text" name="' . $item . '" value="' . $this -> PrintOption($item, false) . '" style="margin: 10px 0;display: block;" class="regular-text" />';
			}

		}
		echo '
			<input name="save" type="submit" class="button button-primary button-large" style="margin-top: 30px;clear: both;float:none;display: block;" value="Save it!">
			</form>
		';
		unset($saved);
	}

	public function SpannyTitle($echo = true){
		$title = the_title('', '', false);
		$title = explode(' ', $title);
		foreach( $title as $titlenum => $titletext ){
			$titlenew .= '<span class="title-num-' . ($titlenum + 1) . '">' . $titletext . ' </span>'; 
		}
		echo $titlenew;
	}

	public function LimitedContent($until = 30, $start = 0) {
		$content = mb_substr(strip_tags(strip_shortcodes(get_the_content())), $strat, $until);
		echo $content . '...';
	}

	public function MetaBox( $items = array() ) {
		foreach( $items as $itemID => $itemName ){
			$value = get_post_meta( get_the_ID(), $itemID, true );
			echo '<label style="clear: both;width: 100%;margin: 5px 0;"><small>' . $itemName . '</small></label>';
			echo '<input type="text" name="funcy_' . $itemID . '" style="width: 100%;margin: 5px 0;" value="' . $value . '" placeholder="' . $itemName . '...">';
		}
	}

	public function MetaBoxPostsCheckbox( $ID, $type, $title ) {
		echo '<h4 style="margin: 15px 0;">' . $title . '</h4>';
		$posts = get_posts( 'post_type=' . $type );
		$checked = get_post_meta( get_the_ID(), $ID, true );
		if(!is_array($checked)){
			$checked = array($checked);
		}
		foreach ($posts as $post) {
			if( in_array($post->ID, $checked) ){
				$ischecked = 'checked';
			} else {
				$ischecked = '';
			}
			echo '<input type="checkbox" name="funcy_' . $ID . '[]" value="' . $post->ID . '" ' . $ischecked . '>';
			echo '<label style="margin-left: 10px;" for="' . $post->ID . '">' . $post->post_title . '</label>';
		}
	}

	public function MetaBoxSave() {
	    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE)
	    return get_the_ID();

	    if (!current_user_can('edit_post', get_the_ID()))
	    return get_the_ID();

		foreach( $_POST as $itemName => $itemValue ){
			if( strpos( $itemName, 'funcy_') !== false ){
				update_post_meta( get_the_ID(), str_replace('funcy_', '', $itemName), $itemValue );
			}
		}
	}



}



?>