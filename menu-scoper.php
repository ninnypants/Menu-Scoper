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
	global $menu, $submenu;

	if(isset($_POST['menu-scope'])){
		update_user_meta($_POST['user'], 'menu-scope', $_POST['items']);
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
			<ul>
				<?php
				foreach($menu as $item):
					if($item[4] == 'wp-menu-separator')
						continue;
				?>
				<li><input type="checkbox" name="items[]" value="<?php echo $item[2]; ?>" checked /> <?php echo strip_tags($item[0]); ?>
					<?php
					if(isset($submenu[$item[2]])):
					?>
						<ul>
							<?php
							foreach($submenu[$item[2]] as $sub):
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
				endforeach;
				?>
			</ul>
			<input type="submit" value="Save" name="menu-scope">

		
		</form>
		<script type="text/javascript">
			mscope = Array();
			<?php
			$users = get_users();
			foreach($users as $usr):
			?>
			mscope[<?php echo $usr->ID; ?>] = <?php echo json_encode(get_user_meta($usr->ID, 'menu-scope', true)); ?>;
			<?php
			endforeach;
			?>
		</script>
	</div>

	<?php

}

add_action('admin_menu', 'mscope_scope_menu', 20);
function mscope_scope_menu(){
	global $menu, $submenu;
	//var_dump($menu, $submenu);
	$usr = wp_get_current_user();
	$mscope = $usr->get('menu-scope');
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

		foreach($submenu as &$subgrp){
			foreach($subgrp as $key => $item){
				if(!in_array($item[2], $mscope)){
					$subgrp[$key];
				}
			}
		}
	}
}

add_action('admin_enqueue_scripts', 'mscope_enqueue_scripts');
function mscope_enqueue_scripts(){
	wp_enqueue_script('mscope', plugins_url('mscope.js', __FILE__), array('jquery'), '0.1', true);
}