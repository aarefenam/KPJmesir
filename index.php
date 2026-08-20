<?php

//print_r($_SERVER);die();

$sitepad['db_name'] = 'kpjmesir_spd4571';
$sitepad['db_user'] = 'kpjmesir_spd4571';
$sitepad['db_pass'] = 'tK[2XMd-y.';
$sitepad['db_host'] = 'localhost';
$sitepad['db_table_prefix'] = 'Kesz0ck_';
$sitepad['charset'] = 'utf8mb4';
$sitepad['collate'] = 'utf8mb4_unicode_ci';
$sitepad['serving_url'] = 'kpjmesir.org';// URL without protocol but with directory as well
$sitepad['url'] = 'https://kpjmesir.org';
$sitepad['relativeurl'] = '';
$sitepad['.sitepad'] = '/home/kpjmesir';
$sitepad['sitepad_plugin_path'] = '/usr/local/sitepad';
$sitepad['editor_path'] = '/usr/local/sitepad/editor';
$sitepad['path'] = dirname(__FILE__);
$sitepad['AUTH_KEY'] = 'CUbmSCRme53kr9X8PM6CkQpKCfghjlsA6CphtX3S4OIqOKAHsxuXN6Hp7CbtUHGE';
$sitepad['SECURE_AUTH_KEY'] = 'CYo0pX4RE95gsiho0PK1s829z0ataQabtMvhjcKfQT8eSAWUmoQDzGElc6mqjLRJ';
$sitepad['LOGGED_IN_KEY'] = '0zd10vBReFMJmSg5LGm60qmVSeqgbAps3GKlB5kgknzt56YQmCu7gnHiv8SOMJJg';
$sitepad['NONCE_KEY'] = 'kvtSxZF2adZmtVIDBeIXG4bpUQlWhyokFEfzXwTld2WTT81X0eWwhwUSqJC4obBm';
$sitepad['AUTH_SALT'] = 'nC2PIU3LZtMy6s9qBLeOMwEHq8T3b2kNmHOtSp9qdHJjwD5Ytgq8YNmE773ucMyd';
$sitepad['SECURE_AUTH_SALT'] = 'lwNZ5ZeeEaqi8NmsbqcrTZAVUssQJEQ1QjO8EsjY8aUh0dhDKItKKSZFJ5z9sTwm';
$sitepad['LOGGED_IN_SALT'] = 'ZMHSOgXBnYJadqWufrAlSEzDqUPvQnxABTDp8KIU1mQdkqV2Dy4E7nN1YEpHl1vR';
$sitepad['NONCE_SALT'] = 'XpvJ0mwyRjFzn7p5za5h9MtaJcrGIBYYJkzpJWwGS2jyeZOO4l1lFgEJBxhhZPxp';

if(!include_once($sitepad['editor_path'].'/site-inc/bootstrap.php')){
	die('Could not include the bootstrap.php. One of the reasons could be open_basedir restriction !');
}

