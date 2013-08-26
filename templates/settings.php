<form id="proton" action="#" method="post">
	<fieldset class="personalblock">
		<legend><strong><?php p($l->t('Prot-On Integration'));?></strong></legend>
		<p><label for="proton_url"><?php p($l->t('URL: '));?><input type="text" id="proton_url" name="proton_url" value="<?php p($_['proton_url']); ?>" style="width:20em;"></label>
		 <input type="hidden" name="requesttoken" value="<?php p($_['requesttoken']) ?>" id="requesttoken">
		<input type="submit" value="Save" />
		<br /><?php p($l->t('Url of the Prot-On rest API')); ?>
	</fieldset>
</form>
