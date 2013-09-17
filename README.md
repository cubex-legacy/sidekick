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
    start program "/bin/bash -c '/sidekick/sidekick/bin/repeat /sidekick/sidekick/bin/cubex --cubex-env=production Repository.Update:longRun -r all -v'" as uid sidekick
    stop program "/sidekick/sidekick/vendor/bin/kill-cubex-script.sh Repository.Update:longRun" as uid sidekick

check process buildQueue
  with pidfile "/var/run/cubex/Fortify.BuildQueue.pid"
    start program "/bin/bash -c '/sidekick/sidekick/bin/repeat  /sidekick/sidekick/bin/cubex --cubex-env=production Fortify.BuildQueue'" as uid sidekick
    stop program "/sidekick/sidekick/vendor/bin/kill-cubex-script.sh Fortify.BuildQueue" as uid sidekick


# Recommended Tools
pear config-set auto_discover 1
pear install pear.phpqatools.org/phpqatools


git clone https://github.com/qbex/CubexCodeStandards.git /usr/share/php/PHP/CodeSniffer/Standards/CubexCodeStandards
