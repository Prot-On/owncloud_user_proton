user_proton
============

[Prot-On](http://Prot-On.com) is an application that allows you to protect, manage and track the use of all kinds of files that are shared on the Internet, by e-mail or through a cloud service.



This app ofers integration of [Prot-On](http://Prot-On.com) users and group in Owncloud.


## Installation


As this repository contains submodules install this app with the following commands:

```
cd xxxx\owncloud\apps
git clone https://github.com/Prot-On/owncloud_user_proton.git user_proton
cd user_proton
git submodule update --init --recursive
```

Then activate the app in Owncloud app manager.

**Note:** If you are using user_proton and files_proton the second application you try to activate will fail, this is due to a shared database, before installing the second one (or after trying and failing) just delete the file `xxx_proton/appinfo/database.xml` of the application not activated.

## Configuration


There are several configurations depending of your use case.

* If you configure a restAPI url it will allow you to log in with your Prot-On credentials in the web or Owncloud clients, also if you provide oAuth data and Prot-On url this will add a new button on login page to login via oAuth providing a simple SSO.

* If you configure a Enterprise name this will block any login attempt from users that are not inside that organization, this is usefull when working with Prot-On main server instead of a Prot-On on Premise Server.

The last thing that can be configured is database credentials, those should be a credentials valid to connect to Prot-On database if this is setted you will be able to search users and groups from Prot-On to share with them files inside Owncloud.
Indeed if restApi is also configured and the file shared is a Prot-On file then the app will automatically give the shared with user or group permissions to that file (the same premissions you give them in Owncloud).

**Note:** If your company uses LDAP and you don't want to automatically manage permissions or groups integration then you can avoid this app and just use Owncloud LDAP application, otherwise configure LDAP on Prot-On and use this app.

For a company with their own on Premise Prot-On server it is recomended to set everything except the Organization name (including custom oAuth).
For a setup against the public Prot-On server it is recomended to set Enterprises and API to enable login with Prot-On credentials. If you want to use oAuth and/or user/group sharing then please contact us so we can provide you with the needed credentials.

### Creation of user for database integration with on Premise server

Use this script to allow remote access to your Prot-On database (only gives read access to 4 tables), use this with your Prot-On database (not Owncloud).
Change own_user and own_pass with your desired user and password.

```sql
CREATE USER 'own_user'@'%' IDENTIFIED BY 'own_pass';
GRANT SELECT ON protonks.user TO 'own_user'@'%';
GRANT SELECT ON protonks.group_of_users TO 'own_user'@'%';
GRANT SELECT ON protonks.proton_domain TO 'own_user'@'%';
GRANT SELECT ON protonks.group_membership TO 'own_user'@'%';
```


### Configure oAuth as the default login method ###

If you have configured the oAuth details for login you can redirect the main page to the oAuth login while enabling another alias for the default login.
To do this just add this to your rewrite rules in the .htaccess under your owncloud dir (in this example the standard login will be at "/adminLogin").

```
RewriteRule ^$ /index.php/apps/user_proton/init [R]

RewriteCond %{QUERY_STRING} redirect_url=
RewriteRule ^index.php$ /index.php/apps/user_proton/init [R]

RewriteRule ^adminLogin$ /index.php [PT]
```

## Support


If you need help with the configuration or integration of this app please open a ticket or send an email to support@prot-on.com
