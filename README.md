Setting Up Sidekick
===

Assumptions made:
- You have PHP installed
- You have composer available (Some commands may need to be updated to point to your composer location)
- You are running a web server, and able to configure the site yourself
- You have a cassandra cluster available to connect to
- You have a MySQL server to connect to

Firstly, you are going to need a directory for sidekick to live in.  To keep things simple, we are going to create a sidekick directory in the base "/sidekick".
Following commands will assume the /sidekick base path, however, you could just as easily work within /var/www/ or /home/username/

cd /sidekick
git clone https://github.com/qbex/sidekick.git sidekick
cd sidekick
composer install

You now need to create your production config to define your connections, an example can be found below.  This will need to be placed in conf/production.ini  (production should be changed to match your CUBEX_ENV defined in your web server configuration)

You should set devtools > creations to true within your config to allow sidekick to generate any tables required.

Hopefully, when you visit sidekick.yourdomain.tld, you will be presented with a login screen.

To create your user, you simply need to run the following command, changing the username to whatever you want.  * I would recommend using "password" for your password at this point, and changing it through the sidekick interface at a later point.

You should now have a complete sidekick install :)

Example conf/production.ini
===
	[project]
	environment=production
	base_uri = sidekick.yourdomain.tld

	[database\db]
	hostname = localhost
	username = sidekick
	password = "yourpassword"

	[devtools]
	creations=true

	[cassandra\sidekick]
	nodes[] = localhost

A sample nginx configuration
===

    server {
        listen   80;
        index index.php;
        server_name sidekick.yourdomain.tld;
        root /sidekick/sidekick/public;

        location / {
            index   index.php;
             if ( !-f $request_filename )
            {
                rewrite ^/(.*)$ /index.php?__path__=/$1 last;
                break;
            }
        }

        location ~ \.php$ {
              fastcgi_pass unix:/var/run/php5-fpm.sock;
              fastcgi_index index.php;
              fastcgi_param CUBEX_ENV production;
              include fastcgi_params;
        }

    }




Other setup requirements
========

MySQL Setup
==

  CREATE DATABASE sidekick
  CREATE USER 'sidekick'@'localhost' IDENTIFIED BY 'EnterPasswordHere';
  GRANT ALL PRIVILEGES ON sidekick.* TO 'sidekick'@'localhost';
  FLUSH PRIVILEGES;


Monit Config
==

check process repositoryUpdate
  with pidfile "/var/run/cubex/Repository.Update:longRun.pid"
    start program "/bin/bash -c '/sidekick/sidekick/bin/repeat /sidekick/sidekick/bin/cubex --cubex-env=production Repository.Update:longRun -r all -v'" as uid sidekick
    stop program "/sidekick/sidekick/vendor/bin/kill-cubex-script.sh Repository.Update:longRun" as uid sidekick

check process buildQueue
  with pidfile "/var/run/cubex/Fortify.BuildQueue.pid"
    start program "/bin/bash -c '/sidekick/sidekick/bin/repeat  /sidekick/sidekick/bin/cubex --cubex-env=production Fortify.BuildQueue'" as uid sidekick
    stop program "/sidekick/sidekick/vendor/bin/kill-cubex-script.sh Fortify.BuildQueue" as uid sidekick

check process deployQueue
  with pidfile "/var/run/cubex/Diffuse.DeployQueue.pid"
    start program "/bin/bash -c '/sidekick/sidekick/bin/repeat  /sidekick/sidekick/bin/cubex --cubex-env=production Diffuse.DeployQueue'" as uid sidekick
    stop program "/sidekick/sidekick/vendor/bin/kill-cubex-script.sh Diffuse.DeployQueue" as uid sidekick


Recommended Tools
==
pear config-set auto_discover 1
pear install pear.phpqatools.org/phpqatools


Import the Cubex Code Standards onto your own system
==
git clone https://github.com/qbex/CubexCodeStandards.git /usr/share/php/PHP/CodeSniffer/Standards/CubexCodeStandards
