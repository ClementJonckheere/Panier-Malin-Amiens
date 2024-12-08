const sequelize = require('../config/config');
const Product = require('../models/products');

(async () => {
    try {
        await sequelize.authenticate();
        console.log('Connexion à la base de données réussie.');

        await Product.sync({ alter: true });
        console.log('La table "products" a été créée ou mise à jour avec succès.');

        process.exit();
    } catch (error) {
        console.error('Erreur lors de la connexion ou de la synchronisation :', error);
        process.exit(1);
    }
})();
