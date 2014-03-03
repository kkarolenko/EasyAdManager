<div class="wrap">

	<?php $eam_b = new EasyAdManager(); ?>

	<h2>Easy Ad Manager</h2>

	<div id="eam_ads">

		<h3>Ad Manager</h3>
		<select name="ads">
		<!-- Query current ads -->
		<?php echo $eam_b->get_select_options(); ?>
		</select>

		<input type="button" name="create_new" value="+ New Ad Spot" class="button" />

		<input type="text" name="name" value="Ad Name" />

		<div id="eam_ad_options_container">

			<ul id="eam_options">
				<li name="Single" class="selected">Single</li>
				<li name="Rotate">Rotate</li>
				<li name="Alt">Alt</li>
				<li name="Disable">Disable</li>
			</ul>

			<div id="eam_edit">

				<div id="eam_single" class="eam_option selected">

					<div class="inline">
						<div class="row">
							<label for="img">IMG:</label>
							<input type="text" name="img" id="eam_img" />
							<input type="button" name="upload" value="Upload" class="button" />
						</div>

						<div class="row">
							<label for="img">LINK:</label>
							<input type="text" name="link" id="eam_link" />
						</div>
					</div>

					<div class="inline expires">
						<input type="checkbox" name="expires" value="<?php //get expires ?>" />
						<label for="expires" id="expires_label">EXPIRES</label>

						<input type="datetime-local" name="date_expires" value="<?php //get date_expires ?>" />
					</div>

				</div>

				<div id="eam_rotate" class="eam_option">
					<label for="src">LINK:</label>
				</div>

				<div id="eam_alt" class="eam_option">
					<label for="alt">ALT:</label>
					<textarea class="eam_alt" name="alt"><?php //get alt ?></textarea>
				</div>

			</div>

		</div>

		<input type="button" id="eam_save" name="save" value="Save" class="button" />
		<input type="button" id="eam_remove" name="remove" value="Remove" class="button" />

	</div>

	<div id="eam_settings">
		<h3>Settings</h3>
		<form method="post" action="options.php">

			<?php
			@settings_fields('eam_plugin');
			@do_settings_fields('eam_plugin');
			?>

			<label for="expire_to">After expire, default to:</label>
			<select name="expire_to">
				<option value="Alt" <?php if (get_option('expire_to') == 'Alt') { echo 'selected'; }?>>Alt</option>
				<option value="Disabled" <?php if (get_option('expire_to') == 'Disabled') { echo 'selected'; }?>>Disabled</option>
			</select>

			<?php @submit_button(); ?>
		</form>
	</div>

</div>
