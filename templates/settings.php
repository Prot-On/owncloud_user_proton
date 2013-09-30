<form id="proton" action="#" method="post">
	<fieldset class="personalblock">
         <input type="hidden" name="requesttoken" value="<?php p($_['requesttoken']) ?>" id="requesttoken">
		<legend><strong><?php p($l->t('Prot-On User Integration'));?></strong></legend>
<?php
if (!OCP\App::isEnabled('files_proton')) {
?>		
		<p><label for="proton_api_url"><?php p($l->t('Prot-On API URL: '));?><input type="text" id="proton_api_url" name="proton_api_url" value="<?php p($_['proton_api_url']); ?>" style="width:20em;" title="<?php p($l->t('Url of the Prot-On rest API')); ?>"></label></p>
		
        <p><label for="proton_url"><?php p($l->t('Prot-On Server URL: '));?><input type="text" id="proton_url" name="proton_url" value="<?php p($_['proton_url']); ?>" style="width:20em;" title="<?php p($l->t('Url of the Prot-On server')); ?>"></label></p>
        <br />
        
        <p><label for="proton_oauth_client_id"><?php p($l->t('OAuth Client Id: '));?><input type="text" id="proton_oauth_client_id" name="proton_oauth_client_id" value="<?php p($_['proton_oauth_client_id']); ?>"></label>
        <label for="proton_oauth_secret"><?php p($l->t('OAuth Client Secret: '));?><input type="text" id="proton_oauth_secret" name="proton_oauth_secret" value="<?php p($_['proton_oauth_secret']); ?>"></label></p>
        <?php p($l->t('OAuth credentials used to connect with Prot-On')); ?>
        <br /><br />
<?php
}
?>      

        <p><label for="proton_hosting"><?php p($l->t('Organization name: '));?><input type="text" id="proton_hosting" name="proton_hosting" value="<?php p($_['proton_hosting']); ?>"></label>
        </p>
        <?php p($l->t('Name of your organization, it must be the same that is configured at Prot-On. Use this is to block all logins from users that are not in your Prot-On hosting.')); ?>
        <br /><br />
        
        <p><label for="proton_mysql_login"><?php p($l->t('ProtOn DB user: '));?><input type="text" id="proton_mysql_login" name="proton_mysql_login" value="<?php p($_['proton_mysql_login']); ?>"></label>
        <label for="proton_mysql_password"><?php p($l->t('ProtOn DB password: '));?><input type="password" id="proton_mysql_password" name="proton_mysql_password" value="<?php p($_['proton_mysql_password']); ?>"></label>
        <label for="proton_db_connection"><?php p($l->t('ProtOn DB connection string: '));?><input type="text" id="proton_db_connection" name="proton_db_connection" value="<?php p($_['proton_db_connection']); ?>"></label>
        <br /><?php p($l->t('Prot-On credentials from the hosting admin. This must be configured to be able to user Prot-On groups on ownCloud')); ?></p>
        
        <input type="submit" value="Save" />
	</fieldset>
</form>
