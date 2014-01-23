VOORBEREIDING:

Pak het archief uit in de public folder van kortingscode. Je krijg een folder
genaamd "migration".

Pas het script ``migrate_kortingscode.php'' aan door:
- ``USER'' te vervangen door de te gebruiken gebruikersnaam van de db server;
- ``PASSWORD'' te vervangen door het te gebruiken wachtwoord voor de user.
- Eventueel naar het MySQL verbindingstype te kijken (TCP/IP poort of Unix
  Socket).


GEBRUIK:

Het script dient gebruikt te worden in de command line (NIET in de browser).

$ cd /path/to/kortingscode.nl/public/migration
$ php -f migrate_kortingscode.php

De *_site en *_user database worden aangevuld met data en tijdens dat
proces krijgen een paar tabellen een paar extra kolommen. Deze kolommen worden
na afloop van het proces weer verwijderd.
