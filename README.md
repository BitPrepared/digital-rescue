### Funzioni

 * Ricerca codice censimento a partire da Nome,Cognome,DataNascita [completato]
 * importazione dati da ASA da formato ODS [completato]
 * Visualizzazione dati profilo 
 * Creazione password per visione dati
 * importazione dati da ASA da formato SQLITE
 

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



### Pratiche 
	
Improved Persistent Login Cookie Best Practice (http://jaspan.com/improved_persistent_login_cookie_best_practice)

You could use this strategy described here as best practice:

    When the user successfully logs in with Remember Me checked, a login cookie is issued in addition to the standard session management cookie.
    The login cookie contains the user's username, a series identifier, and a token. The series and token are unguessable random numbers from a suitably large space. All three are stored together in a database table.
    When a non-logged-in user visits the site and presents a login cookie, the username, series, and token are looked up in the database.
        If the triplet is present, the user is considered authenticated. The used token is removed from the database. A new token is generated, stored in database with the username and the same series identifier, and a new login cookie containing all three is issued to the user.
        If the username and series are present but the token does not match, a theft is assumed. The user receives a strongly worded warning and all of the user's remembered sessions are deleted.
        If the username and series are not present, the login cookie is ignored.


### TABELLE


select s.asa_csocio, s.asa_creg, r.asa_nome, s.asa_cognome, s.asa_nome, cel.asa_numero as cellulare, s.asa_indirizzo, s.asa_cap,
s.asa_datan, s.asa_nascita, s.asa_foca, rec.asa_numero as email  from asa_soci s join asa_regionisys r on r.asa_creg = s.asa_creg
join asa_recapiti rec on rec.asa_csocio = s.asa_csocio join asa_cellulari cel on cel.asa_csocio = s.asa_csocio
where rec.asa_tipo = 'E'



-- CREATE VIEW asa_cellulari AS select * from asa_recapiti rcell where rcell.asa_tipo = 'B'


ASA_ORD è il gruppo


php rescue.php -i /Users/yoghi/Documents/Workspace/digital-rescue/tests/resources/manysqlite/


## Installazione

composer.phar install --no-dev --optimize-autoloader
