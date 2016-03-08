<?php

define('PUN_ROOT', dirname(__FILE__).'/');
require PUN_ROOT.'include/common.php';
require PUN_ROOT.'header.php';

if ($pun_user['g_read_board'] == '0')
	message($lang_common['No view'], false, '403 Forbidden');

?>



<div id="postform" class="blockform">
	<h2><span><?php echo $action ?></span></h2>
	<div class="box">
		<?php echo $form."\n" ?>
			<div class="inform">
				<fieldset>
					<legend><?php echo $lang_common['Write message legend'] ?></legend>
					<div class="infldset txtarea">
						<label class="conl required"><span><b>Titre</b></span><br />
							<input type="text" name="title" value="" size="25" maxlength="25" tabindex="" /><br />
						</label>
						<label class="conl required"><span><b>Date de démarrage</b></span><br />
							<input type="text" id="start" name="start" value="" size="25" maxlength="25" tabindex="" /><br />
						</label>
						<label class="conl required"><span><b>Date de fin</b></span><br />
							<input type="text" name="end" value="" size="25" maxlength="25" tabindex="" /><br />
						</label>
						<label class="conl required"><span><b>Description</b></span><br />
							<textarea name="desc" rows="20" cols="95" tabindex=""></textarea><br />
						</label>
					</div>
				</fieldset>
			</div>
	</div>
</div>


<script src="portal/js/jquery-2.2.1.min.js"></script>
<script src="portal/js/moment.js"></script>
<script>

	moment().locale('fr');
	$('#start').val(moment().format('dd MM yyyy'));
</script
<?php


require PUN_ROOT.'footer.php';
?>