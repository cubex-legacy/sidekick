Cubex Sidekick
========

MySQL Setup

  CREATE DATABASE sidekick
  CREATE USER 'sidekick'@'localhost' IDENTIFIED BY 'EnterPasswordHere';
  GRANT ALL PRIVILEGES ON sidekick.* TO 'sidekick'@'localhost';
  FLUSH PRIVILEGES;

Monit Config

check process repositoryUpdate
  with pidfile "/var/run/cubex/Repository.Update:longRun.pid"
    start program "/home/qbex/sidekick/bin/cubex --cubex-env=devbuild Repository.Update:longRun -r all -v"
    stop program "/home/qbex/sidekick/vendor/bin/kill-cubex-script.sh Repository.Update:longRun"

check process buildQueue
  with pidfile "/var/run/cubex/Fortify.BuildQueue.pid"
    start program "/home/qbex/sidekick/bin/cubex --cubex-env=devbuild Fortify.BuildQueue"
    stop program "/home/qbex/sidekick/vendor/bin/kill-cubex-script.sh Fortify.BuildQueue"


# Recommended Tools
pear config-set auto_discover 1
pear install pear.phpqatools.org/phpqatools
