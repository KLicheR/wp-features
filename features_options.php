<?php
class Features_options extends Features {
	public $php_version;

	private $options = array();

	public function __construct() {
		$this->set_php_version();
		add_action('admin_menu', array($this, 'add_page'));
		add_action('wp_ajax_features_revert_option', array($this, 'revert_option'));
		$this->get_options_data();
	}

	private function set_php_version() {
		$version = explode('.', PHP_VERSION);
		$this->php_version = $version[0] * 10000 + $version[1] * 100 + $version[2];
	}

	public function get_options_data() {
		if (file_exists(dirname(__FILE__) . '/features_options_data.php')) {
			include_once(dirname(__FILE__) . '/features_options_data.php');
			$this->options = $options;
		}
	}

	public function add_page() {
		add_settings_section( 'wp_options', 'Fields from "wp_options" table', array($this, 'render_section'), 'features-options' );
		add_management_page(__('Features', 'features'), __('Features', 'features'), 'manage_options', 'features-options', array($this, 'render_page'));
	}

	public function render_section() {
		echo __('Options can be reverted all at once by pressing the <strong>Revert all</strong> or individually by pressing the <strong>Revert</strong> buttons of each option.', 'features');
	}

	public function render_page() {
?>
		<div class="wrap">
			<?php screen_icon(); ?>
			<h2><?php echo __('Features', 'features'); ?></h2>
			<form method="post" action="options.php">
				<?php do_settings_sections('features-options'); ?>
				<table class="form-table">
					<tbody>
						<?php
							foreach ($this->options as $key => $value) {
								$this->add_settings_field($key, $value);
							}
						?>
					</tbody>
				</table>
				<p class="submit">
					<button type="button" name="revert-all" id="revert-all" class="button button-primary"><?php echo __('Revert all', 'features'); ?></button>
				</p>
			</form>
		</div>
		<script type="text/javascript">
			jQuery(document).ready(function(){
				set_revert_all_btn_state();

				jQuery('.revert').click(function(){
					var key = jQuery(this).data('key');
					jQuery('#loading-'+key).show();
					jQuery('#message-'+key).hide();
					jQuery(this).attr('disabled', 'disabled');
					jQuery.ajax({
						url:ajaxurl,
						type:'POST',
						dataType:'json',
						data:{
							action:'features_revert_option',
							key:key
						},
						context:this,
						success:function(data, textStatus, jqXHR) {
							jQuery('#loading-'+key).hide();
							jQuery('#message-'+key).show();
							if (data.status == 'error') {
								jQuery('#message-'+key).text(data.message);
								jQuery(this).removeAttr('disabled');
							}
							else{
								jQuery('#message-'+key).hide();
								jQuery('#db-value-'+key).hide();
								jQuery('#db-value-label-'+key).hide();
								jQuery('#label-'+key).css('color', 'green').css('font-weight', 'normal');
							}
							set_revert_all_btn_state();
						},
						error:function(jqXHR, textStatus, errorThrown) {
							jQuery('#loading-'+key).hide();
							jQuery('#message-'+key).show();
							jQuery('#message-'+key).text('A problem occured with request. Update failed.').css('color', 'red');
							jQuery(this).removeAttr('disabled');
						}
					});
				});
				jQuery('#revert-all').click(function(){
					jQuery('.revert').trigger('click');
				});
			});
			function set_revert_all_btn_state() {
				if (jQuery('.revert[disabled!="disabled"]').length == 0) {
					jQuery('#revert-all').attr('disabled', 'disabled');
				}
				else{
					jQuery('#revert-all').removeAttr('disabled');
				}
			}
		</script>
<?php
	}

	public function revert_option() {
		header("Content-Type: application/json; charset=UTF-8");

		if (!isset($_POST['key'])) {
			echo json_encode(array(
				'status' => 'error',
				'message' => 'No key defined.',
			));
		}
		else{
			$key = $_POST['key'];
			if (!isset($this->options[$key])) {
				echo json_encode(array(
					'status' => 'error',
					'message' => 'There is no data available to update this option.'.$key,
				));
			}
			else{
				if (false === get_option($key)) {
					echo json_encode(array(
						'status' => 'error',
						'message' => 'The option key is invalid.',
					));
				}
				else{
					$value = $this->options[$key];
					if (is_serialized($value)) {
						$value = unserialize($value);
					}
					if (!update_option($key, $value)) {
						echo json_encode(array(
							'status' => 'error',
							'message' => 'Update failed.',
						));
					}
					else{
						echo json_encode(array(
							'status' => 'success',
						));
					}
				}
			}
		}
		die();
	}

	private function add_settings_field($key, $raw_value) {
		$db_value = get_option($key);
		if (is_serialized($raw_value)) {
			$value = unserialize($raw_value);
			$db_raw_value = serialize($db_value);
		}
		else{
			$value = $raw_value;
			$db_raw_value = $db_value;
		}
		$sync = ($value == $db_value);
?>
		<tr valign="top">
			<th style="width:300px;">
				<label id="label-<?php echo $key; ?>" style="<?php echo ($sync?'color:green;font-weight:normal;':'color:red;font-weight:bold;'); ?>"><?php echo $key; ?></label>
			</th>
			<td>
				<?php if (!$sync): ?>
					<button type="button" class="button revert" data-key="<?php echo $key; ?>"><?php echo __('Revert', 'features'); ?></button>
				<?php else: ?>
					<button type="button" class="button revert" disabled="disabled"><?php echo __('Revert', 'features'); ?></button>
				<?php endif; ?>
				<img id="loading-<?php echo $key; ?>" src="/wp-admin/images/wpspin_light.gif" style="display:none;">
				<span id="message-<?php echo $key; ?>" style="display:none;color:red;"></span>
			</td>
			<td>
				<?php
					if (!$sync):
						// If PHP version in >= 5.4.0.
						if ($this->php_version >= 50400) {
							$db_raw_value_with_htmlentities = htmlentities($db_raw_value);
						}
						else {
							$db_raw_value_with_htmlentities = htmlentities($db_raw_value, ENT_COMPAT, 'UTF-8');
						}
				?>
					<label id="db-value-label-<?php echo $key; ?>" for="db-value-<?php echo $key; ?>">Raw value from DB, ready for the &quot;features_options_data.php&quot; file:</label>
					<input type="text" id="db-value-<?php echo $key; ?>" value="<?php echo str_replace("'", "\'", $db_raw_value_with_htmlentities); ?>" title="Raw value from DB, ready for the &quot;features_options_data.php&quot; file.">
				<?php
					endif;
				?>
			</td>
		</tr>
<?php
	}
}
?>