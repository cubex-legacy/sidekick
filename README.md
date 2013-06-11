sidekick
========

Cubex Sidekick


Monit Config

check process repositoryUpdate
  with pidfile /var/run/cubex/RepositoryUpdate.pid
    start program "/home/qbex/sidekick/bin/cubex --cubex-env=ENVIRONMENT Repository.Update:longRun -r all -v"
    stop program "/home/qbex/sidekick/vendor/bin/kill-cubex-script.sh Repository.Update:longRun"

check process buildQueue
  with pidfile /var/run/cubex/BuildQueue.pid
    start program "/home/qbex/sidekick/bin/cubex --cubex-env=ENVIRONMENT Fortify.BuildQueue"
    stop program "/home/qbex/sidekick/vendor/bin/kill-cubex-script.sh Fortify.BuildQueue"
