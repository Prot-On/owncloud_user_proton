<form id="proton" action="#" method="post">
	<fieldset class="personalblock">
         <input type="hidden" name="requesttoken" value="<?php p($_['requesttoken']) ?>" id="requesttoken">
		<legend><strong><?php p($l->t('Prot-On Integration'));?></strong></legend>
		
		<p><label for="proton_api_url"><?php p($l->t('Prot-On API URL: '));?><input type="text" id="proton_api_url" name="proton_api_url" value="<?php p($_['proton_api_url']); ?>" style="width:20em;"></label>
		<br /><?php p($l->t('Url of the Prot-On rest API')); ?></p>
		
        <p><label for="proton_url"><?php p($l->t('Prot-On Server URL: '));?><input type="text" id="proton_url" name="proton_url" value="<?php p($_['proton_url']); ?>" style="width:20em;"></label>
        <br /><?php p($l->t('Url of the Prot-On server')); ?></p>
        
        <p><label for="proton_oauth_client_id"><?php p($l->t('OAuth Client Id: '));?><input type="text" id="proton_oauth_client_id" name="proton_oauth_client_id" value="<?php p($_['proton_oauth_client_id']); ?>"></label>
        <label for="proton_oauth_secret"><?php p($l->t('OAuth Client Secret: '));?><input type="text" id="proton_oauth_secret" name="proton_oauth_secret" value="<?php p($_['proton_oauth_secret']); ?>"></label></p>
        <br /><?php p($l->t('OAuth credentials used to connect with Prot-On')); ?>

        <p><label for="proton_hosting"><?php p($l->t('Hosting name: '));?><input type="text" id="proton_hosting" name="proton_hosting" value="<?php p($_['proton_hosting']); ?>"></label>
        <br /><?php p($l->t('Name of your organization, it must be the same that is configured at Prot-On. Use this is to block all logins from users that are not in your Prot-On hosting.')); ?></p>
        
        <p><label for="proton_hosting_admin_login"><?php p($l->t('Hosting admin login: '));?><input type="text" id="proton_hosting_admin_login" name="proton_hosting_admin_login" value="<?php p($_['proton_hosting_admin_login']); ?>"></label>
        <label for="proton_hosting_admin_password"><?php p($l->t('Hosting admin password: '));?><input type="password" id="proton_hosting_admin_password" name="proton_hosting_admin_password" value="<?php p($_['proton_hosting_admin_password']); ?>"></label>
        <br /><?php p($l->t('Prot-On credentials from the hosting admin. This must be configured to be able to user Prot-On groups on ownCloud')); ?></p>
        
        <input type="submit" value="Save" />
	</fieldset>
</form>
