setupdb:
	cd migration;mysql -udemo -pdemo < ./CREATE_TABLE

deletedb:
<<<<<<< HEAD
	cd migration;mysql -uroot  < ./DELETE_TABLE
	
insertdb:
	cd migration;mysql -uroot  < ./INSERT_TABLE

deletemasterdb:
	cd migration;mysql -uroot -psarumon  < ./DELETE_TABLE

setupmasterdb:
	cd migration;mysql -uroot -psarumon < ./CREATE_TABLE
=======
	cd migration;mysql -udemo -pdemo < ./DELETE_TABLE
>>>>>>> 2_makeQuestionModelAndDao

