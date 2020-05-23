const express = require('express');
const morgan = require('morgan');
const app = express();

// requiriendo rutas
const routes = require('./routes');
const routesApi = require('./routes-api');

// Settings
app.set('appName', 'My frist server');

// Middlewares
app.use( morgan('dev') );
app.set('views', __dirname + '/views' );
app.set('view engine', 'ejs');

// rutas
app.use(routes);
app.use('/api', routesApi);

app.get('*', (req, res)=>{
    res.end('Archivo no encontrado!');
});

app.listen(3000, ()=>{
    console.log('Server funcionando');
    console.log('Nombre de la app', app.get('appName'));
});