<VirtualHost *:80>
	 RewriteEngine on
     	 RewriteCond %{REQUEST_METHOD} ^(TRACE|TRACK)
    	 RewriteRule .* - [F]

	ServerAdmin webmaster@appstack.io
	ServerName dev.appstack.io
	ServerAlias dev.appstack.io
	DocumentRoot /var/www/dev.appstack.io/trunk/public/
	<Directory /var/www/dev.appstack.io/trunk/public/>
		Options FollowSymLinks MultiViews -Indexes
		AllowOverride All
		Order allow,deny
		allow from all
	</Directory>

	ScriptAlias /cgi-bin/ /usr/lib/cgi-bin/
	<Directory "/usr/lib/cgi-bin">
		AllowOverride None 
		Options +ExecCGI -MultiViews +SymLinksIfOwnerMatch
		Order allow,deny
		Allow from all
	</Directory>

	ErrorLog /var/log/apache2/dev.appstack.io_error.log

	# Possible values include: debug, info, notice, warn, error, crit,
	# alert, emerg.
	LogLevel warn

	CustomLog /var/log/apache2/dev.appstack.io_access.log combined


</VirtualHost>
