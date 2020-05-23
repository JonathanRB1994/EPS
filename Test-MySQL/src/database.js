const mysql = require('mysql');
const {promisify} = require('util');
const {database} = require('./keys');

const pool = mysql.createPool(database);

pool.getConnection((err, connection)=>{
    if(err){
        if(err.code === 'PROTOCOLE_CONNECTION_LOST'){
            console.error('DATABASE CONNECTION WAS CLOSED');
        }else if(err.code === 'ER_CON_COUNT_ERROR'){
            Console.error('DATABASE HAS TO MANY CONNECTIONS');
        }else if(err.code == 'ECONNREFUSED'){
            console.error('DATABASE CONNECION WAS REFUSED');
        }
    }

    if (connection) connection.release();
    console.log('DB is Connected');
    return;
});

// Promisify Pool Querys
pool.query = promisify(pool.query);

module.exports = pool;