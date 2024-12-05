const puppeteer = require('puppeteer');

const baseUrl = process.argv[2]; // URL de recherche
const productType = process.argv[3]; // Type de produit (riz, lait, etc.)

(async () => {
    const browser = await puppeteer.launch({
        executablePath: process.env.PUPPETEER_EXECUTABLE_PATH || '/usr/bin/chromium',
        headless: true, // Utilisez headless pour éviter d'ouvrir une fenêtre
        args: ['--no-sandbox', '--disable-setuid-sandbox'], // Nécessaire pour Puppeteer dans Docker
    });
    const page = await browser.newPage();

    let allProducts = [];
    let pageIndex = 1;

    while (true) {
        const url = `${baseUrl}&p=${pageIndex}`;
        await page.goto(url, { waitUntil: 'networkidle2' });

        // Ajout d'un délai pour permettre à la page de charger complètement
        await new Promise(resolve => setTimeout(resolve, 3000));

        try {
            await page.waitForSelector('.vignette-grille-produit-component', { timeout: 10000 });
        } catch (error) {
            console.error(`Page ${pageIndex} ne contient pas les produits ou n'est pas accessible.`);
            break;
        }

        const products = await page.evaluate((productType) => {
            const items = Array.from(document.querySelectorAll('.vignette-grille-produit-component'));
            return items.map(item => {
                const name = item.querySelector('.label-container.link a')?.getAttribute('alt') || item.querySelector('.label-container.link a')?.innerText || 'Nom indisponible';

                const priceEntier = item.querySelector('.prix-block .prix-unitaire .entier')?.innerText.trim() || '';
                const priceDecimal = item.querySelector('.prix-block .prix-unitaire .decimal')?.innerText.trim() || '';
                const priceDevise = item.querySelector('.prix-block .prix-unitaire .devise')?.innerText.trim() || '';
                const price = priceEntier ? `${priceEntier}${priceDecimal}` : 'Prix indisponible';

                const pricePerKgEntier = item.querySelector('.prixKg .prix .entier')?.innerText.trim() || '';
                const pricePerKgDecimal = item.querySelector('.prixKg .prix .decimal')?.innerText.trim() || '';
                const pricePerKg = pricePerKgEntier ? `${pricePerKgEntier}${pricePerKgDecimal}` : 'Prix par kg indisponible';

                return { name, price, pricePerKg, type: productType };
            });
        }, productType);

        if (products.length === 0) {
            console.log(`Aucun produit trouvé pour ${productType} sur la page ${pageIndex}.`);
            break;
        }

        allProducts = allProducts.concat(products);
        pageIndex++;
    }

    console.log(JSON.stringify(allProducts, null, 2));
    await browser.close();
})();
