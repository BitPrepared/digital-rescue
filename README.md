### Funzioni

 * Ricerca codice censimento a partire da Nome,Cognome,DataNascita [completato]
 * importazione dati da ASA [completato]
 * Visualizzazione dati profilo 
 * Creazione password per visione dati 
 

### Note

Per fare chiamate REST e' possibile usare il client https://github.com/guzzle/guzzle
Per cron (per parsare gli argomenti) https://github.com/jlogsdon/php-cli-tools

da https://github.com/jeremykendall/flaming-archer --> build per jenkins

Unit test: 
http://phpunit.de/manual/3.7/en/writing-tests-for-phpunit.html

### Tool utilizzati

 * http://minikomi.github.io/Bootstrap-Form-Builder/
 * https://github.com/HackedByChinese/ng-idle


### Todo 

manca anche la struttura del db...
"ext-mcrypt": "*"
$email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);


### Buone pratiche 

php-cs-fixer fix --level=all src

### Install

crontab -e 

20 * * * * /usr/bin/php /usr/bin/php /var/www/rescue.emiroagesci.it/script/cron.php

### Casini 
se fai casini puoi revertare fino ad un certo commit :

git reset --soft $$$$$$$ 

con $$$$ che è l'hash a cui revertare

e poi 

git push origin +master

per forzare il commit su github 
