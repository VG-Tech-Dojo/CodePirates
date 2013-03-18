setupdb:
	cd migration;mysql -udemo -pdemo < ./CREATE_TABLE

deletedb:
	cd migration;mysql -uroot  < ./DELETE_TABLE
	

deletemasterdb:
	cd migration;mysql -uroot -psarumon  < ./DELETE_TABLE

