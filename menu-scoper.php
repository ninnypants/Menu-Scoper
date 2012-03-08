<?php
/*
Plugin Name: Menu Scoper
Plugin URI: http://ninnypants.com
Description: Limit menus on a per user bases regardless of role.
Version: 0.5
Author: ninnypants
Author URI: http://ninnypants.com
License: GPL2
*/


function mscope_add_menu(){

	add_submenu_page( 'options-general.php', 'Menu Scope', 'Menu Scope', 'add_users', 'menu-scope', 'mscope_page');

}

add_action('admin_menu', 'mscope_add_menu');



function mscope_page(){
	global $mscope_menu, $mscope_submenu;

	if(wp_verify_nonce($_POST['_wpnonce'], 'scope-menu')){
		update_user_meta($_POST['user'], 'menu-scope', $_POST['items']);
		update_user_meta($_POST['user'], 'menu-order', $_POST['pos']);
	}
	?>
	<style>
	#menu-scope{
		padding-top: 30px;
	}

	#menu-scope li ul{
		padding-left: 30px;
	}
	</style>
	<div class="wrap">
		<div id="icon-options-general" class="icon32"></div>
		<h2>Menu Scope</h2>
		<form method="post" action="" id="menu-scope">
			<?php wp_dropdown_users(array('include_selected' => true, )); ?>
			<p>Uncheck menu items to hide them.</p>
			<ul>
				<?php
				$pos = 1;
				foreach($mscope_menu as $key => $item):
					// if($item[4] == 'wp-menu-separator')
					// 	continue;
				?>
				<li><input type="text" name="pos[<?php echo $key; ?>]" value="<?php echo $pos; ?>" class="pos"><input type="checkbox" name="items[]" value="<?php echo $item[2]; ?>" checked /> <?php echo $item[4] == 'wp-menu-separator' ? 'Separator' : strip_tags($item[0]); ?>
					<?php
					if(isset($mscope_submenu[$item[2]])):
					?>
						<ul>
							<?php
							foreach($mscope_submenu[$item[2]] as $sub):
							?>
								<li><input type="checkbox" name="items[]" value="<?php echo $sub[2]; ?>" checked /> <?php echo strip_tags($sub[0]); ?></li>
							<?php
							endforeach;
							?>
						</ul>
					<?php
					endif;
					?>
				</li>
				<?php
				$pos++;
				endforeach;
				?>
			</ul>
			<input type="submit" value="Save" name="menu-scope">
			<?php wp_nonce_field('scope-menu'); ?>
		</form>
		<script type="text/javascript">
			mscope = Array();
			<?php
			$users = get_users();
			foreach($users as $usr):
			?>
			mscope[<?php echo $usr->ID; ?>] = <?php echo json_encode(array(get_user_meta($usr->ID, 'menu-scope', true), get_user_meta($usr->ID, 'menu-order', true))); ?>;
			<?php
			endforeach;
			?>
		</script>
	</div>

	<?php

}

add_action('admin_menu', 'mscope_scope_menu', 20);
function mscope_scope_menu(){
	global $menu, $submenu, $mscope_menu, $mscope_submenu;
	// make sure we have the full menu to work with later
	$mscope_menu = $menu; $mscope_submenu = $submenu;
	$usr = wp_get_current_user();
	$mscope = $usr->get('menu-scope');
	$order = $usr->get('menu-order');

	// re-order main menu
	if(!empty($order)){
		$newmen = array();
		foreach($menu as $key => $item){
			$newmen[$order[$key]] = $item;
		}
		$menu = $newmen;
	}
	// scope main menu
	if(is_array($mscope)){
		foreach($menu as $key => $item){
			if($item[4] == 'wp-menu-separator')
				continue;
			
			if(!in_array($item[2], $mscope)){
				if(isset($submenu[$item[2]])){
					unset($submenu[$item[2]]);
				}
				unset($menu[$key]);
			}
		}

		foreach($submenu as $skey => $subgrp){
			foreach($subgrp as $key => $item){
				if(!in_array($item[2], $mscope)){
					unset($submenu[$skey][$key]);
				}
			}
		}
	}
}

add_action('admin_enqueue_scripts', 'mscope_enqueue_scripts');
function mscope_enqueue_scripts(){
	wp_enqueue_script('mscope', plugins_url('mscope.js', __FILE__), array('jquery', 'jquery-ui-sortable'), '0.1', true);
}