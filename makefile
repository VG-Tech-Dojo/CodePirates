setupdb:
	cd migration;mysql -utreasure -ptreasure_pass < ./CREATE_TABLE

deletedb:
	cd migration;mysql -utreasure  < ./DELETE_TABLE
	
insertdb:
	cd migration;mysql -utreasure  < ./INSERT_TABLE

versionupdb101:
	cd migration;mysql -utreasure  < ./VER101

changelikeTable:
	cd migration;mysql -utreasure  < ./changelikeTable

versionupdb201:
	cd migration;mysql -utreasure  < ./VER201

deletemasterdb:
	cd migration;mysql -utreasure -ptreasure_pass < ./DELETE_TABLE

setupmasterdb:
	cd migration;mysql -utreasure -ptreasure_pass < ./CREATE_TABLE

insertmasterdb:
	cd migration;mysql -utreasure -ptreasure_pass < ./INSERT_TABLE

versionupmasterdb101:
	cd migration;mysql -utreasure -ptreasure_pass < ./VER101

changemasterlikeTable:
	cd migration;mysql -utreasure -ptreasure_pass < ./changelikeTable

versionupmasterdb201:
	cd migration;mysql -utreasure -ptreasure_pass < ./VER201
