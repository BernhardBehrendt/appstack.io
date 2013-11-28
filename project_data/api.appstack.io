<VirtualHost *:80>
        ServerName api.appstack.io 
        DocumentRoot /var/www/dev.appstack.io/trunk/api/www/

        UseCanonicalName Off
        ServerSignature On

        <Directory "/var/www/dev.appstack.io/trunk/api/www/">
                Options Indexes FollowSymLinks MultiViews
                AllowOverride None
                Order allow,deny
                Allow from all

                <IfModule mod_php5.c>
                  php_value magic_quotes_gpc                0
                  php_value register_globals                0
                  php_value session.auto_start              0
                  php_value session.cookie_domain ".appstack.io"
                </IfModule>

        </Directory>
</VirtualHost>
