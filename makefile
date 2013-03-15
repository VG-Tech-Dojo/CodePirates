setupdb:
	cd migration;mysql -udemo -pdemo < ./CREATE_TABLE

deletedb:
	cd migration;mysql -udemo -pdemo < ./DELETE_TABLE

