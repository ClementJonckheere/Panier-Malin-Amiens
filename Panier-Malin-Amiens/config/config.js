const { Sequelize } = require('sequelize');

const sequelize = new Sequelize('panier_malin', 'user', 'password', {
    host: 'localhost',
    dialect: 'mysql',
    port: 3306
});


module.exports = sequelize;
