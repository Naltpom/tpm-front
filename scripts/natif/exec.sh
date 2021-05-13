#!/bin/bash

ACTION=$1

CONSOLE='php bin/console'
PATH_VHOST='/Users/thibaut/Sites/vhosts'
HOSTS_FILE="/etc/hosts"
HOSTS_TMP_FILE="/etc/hosts.tmp"

hosts=(
    "opti.mobads.localhost"
)

execute() {
    $ACTION
}

no-implemented() {
    echo "Command not implemented"
}

#############
# DOCKER
#############

up() {
    sudo apachectl start
}

remove() {
    no-implemented
}

info() {
    echo -e "\n\033[35m==========  Infos  ==========\n\033[37m"

    echo -e "\033[33m Hosts:\033[37m"
    echo -e "\033[37m    - Monolitic:     \033[34m http://${hosts[0]}\033[37m"
}

exec() {
    no-implemented
}

envs() {
    cp behat.yml.dist behat.yml
    brew services start elasticsearch
    brew services start httpd
    brew services start kibana
    brew services start php
    brew services start memcached
}

envs-remove() {
    brew services stop elasticsearch
    brew services stop httpd
    brew services stop kibana
    brew services stop php
    brew services stop memcached
    sudo apachectl stop
    rm -rf public/build public/bundles
}

images-build() {
    echo "Add vhost. There is an example in scripts/natif/http/vhost.conf";
}

images-remove() {
    echo "Don't forget to remove vhost"
}

networks-create() {
    no-implemented
}

networks-remove() {
    no-implemented
}

hosts-add() {
    for i in ${hosts[*]}; do
        HOST="127.0.0.1       ${i}"
        grep -q -F "${HOST}" "${HOSTS_FILE}" || echo "${HOST}" >> "${HOSTS_FILE}"
    done
}

hosts-remove() {
    for i in ${hosts[*]}; do
        PATTERN="/${i}/d"
        sed "${PATTERN}" "${HOSTS_FILE}" > "${HOSTS_TMP_FILE}" && mv "${HOSTS_TMP_FILE}" "${HOSTS_FILE}"
    done
}

swarm-init() {
    no-implemented
}

volumes-create() {
    no-implemented
}

#############
# SYMFONY
#############

install() {
    cmd 'composer install'
}

#############
# TEST
#############

tests() {
    tu
    tf
}

tu() {
    cmd "vendor/bin/simple-phpunit"
}

tu_coverage() {
    cmd "vendor/bin/simple-phpunit --coverage-html=coverage/unit --coverage-xml=coverage/unit/coverage-xml --log-junit=coverage/unit/junit.xml"
    cmd "vendor/bin/infection --threads=4 --coverage=coverage/unit --only-covered"
}

tf() {
    cmd "php -d memory_limit=-1 vendor/behat/behat/bin/behat --no-coverage --format progress --stop-on-failure"
}

tf_coverage() {
    cmd "php -d memory_limit=-1 vendor/behat/behat/bin/behat --format progress"
}

#############
# Assets
#############

assets() {
    cmd 'yarn install'
    yarn
}

yarn() {
    cmd 'bin/console bazinga:js-translation:dump public/js --format=json --merge-domains'
    cmd 'bin/console fos:js-routing:dump --format=json --target=public/js/fos_js_routes.json\
     && yarn run dev'
}

yarn_watch() {
    cmd 'yarn run watch'
}

#############
# AUDIT
#############

phpcs() {
    cmd "./vendor/bin/phpcs --standard=.phpcs.xml src"
}

phpcpd() {
    cmd "./vendor/sebastian/phpcpd/phpcpd src"
}

phpmd() {
    cmd "./vendor/phpmd/phpmd/src/bin/phpmd src text .phpmd.xml --exclude src/DataFixtures"
}

php_cs_fixer() {
    cmd "./vendor/friendsofphp/php-cs-fixer/php-cs-fixer fix --dry-run --using-cache=no src/"
}

php_cs_fixer_apply() {
    cmd "./vendor/friendsofphp/php-cs-fixer/php-cs-fixer fix --using-cache=no --verbose src"
    cmd "./vendor/friendsofphp/php-cs-fixer/php-cs-fixer fix --using-cache=no --verbose tests"
    cmd "./vendor/friendsofphp/php-cs-fixer/php-cs-fixer fix --using-cache=no --verbose features/bootstrap"
}

phpmetrics() {
    cmd "phpdbg -qrr ./vendor/bin/phpmetrics --report-html=reports --exclude=vendor,bin,reports,tests,var,features,src/Kernel.php,src/DataFixtures,src/Migrations ./"
}

cmd() {
    bash -c "$1"
}

execute
