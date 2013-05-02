setupdb:
	cd migration;mysql -udemo -pdemo < ./CREATE_TABLE

deletedb:
	cd migration;mysql -uroot  < ./DELETE_TABLE
	
insertdb:
	cd migration;mysql -uroot  < ./INSERT_TABLE

versionupdb101:
	cd migration;mysql -uroot  < ./VER101

changelikeTable:
	cd migration;mysql -uroot  < ./changelikeTable

versionupdb201:
	cd migration;mysql -uroot  < ./VER201

deletemasterdb:
	cd migration;mysql -uroot -psarumon < ./DELETE_TABLE

setupmasterdb:
	cd migration;mysql -uroot -psarumon < ./CREATE_TABLE

insertmasterdb:
	cd migration;mysql -uroot -psarumon < ./INSERT_TABLE

versionupmasterdb101:
	cd migration;mysql -uroot -psarumon < ./VER101

changemasterlikeTable:
	cd migration;mysql -uroot -psarumon < ./changelikeTable

versionupmasterdb201:
	cd migration;mysql -uroot -psarumon < ./VER201
