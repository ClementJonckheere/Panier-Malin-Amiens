const puppeteer = require('puppeteer');

// Obtenir l'URL de base et le nombre de pages à partir des arguments de la ligne de commande
const baseUrl = process.argv[2];
const maxPages = parseInt(process.argv[3]) || 1; // Nombre de pages à scraper (par défaut 1)

(async () => {
    const browser = await puppeteer.launch();
    const page = await browser.newPage();

    let allProducts = [];

    for (let i = 1; i <= maxPages; i++) {
        const url = `${baseUrl}&p=${i}`;
        await page.goto(url, { waitUntil: 'networkidle2' });
        await page.waitForSelector('.vignette-grille-produit-component', { timeout: 60000 });

        // Extraire les informations des produits
        const products = await page.evaluate(() => {
            const items = Array.from(document.querySelectorAll('.vignette-grille-produit-component'));
            return items.map(item => {
                const name = item.querySelector('.label-container.link a')?.getAttribute('alt') || item.querySelector('.label-container.link a')?.innerText || 'Nom indisponible';
                const price = item.querySelector('.prix-block')?.innerText || 'Prix indisponible';
                return { name, price };
            });
        });

        // Ajouter les produits de la page courante à la liste générale
        allProducts = allProducts.concat(products);
    }
    console.log(JSON.stringify(allProducts, null, 2));

    await browser.close();
})();
