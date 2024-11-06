const puppeteer = require('puppeteer');

(async () => {
    const url = process.argv[2]; // URL de Bi1
    const browser = await puppeteer.launch({ headless: true });
    const page = await browser.newPage();

    // Définir un User-Agent pour imiter un navigateur réel
    await page.setUserAgent('Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/87.0.4280.88 Safari/537.36');

    // Aller à l'URL et attendre que la page se charge
    await page.goto(url, { waitUntil: 'networkidle2' });

    // Pause pour s'assurer que tout le contenu est chargé
    await new Promise(resolve => setTimeout(resolve, 3000)); // Attendre 3 secondes pour le chargement

    // Extraire les informations de produits
    const products = await page.evaluate(() => {
        // Utilisation de '.product-item' comme conteneur principal pour chaque produit
        const items = Array.from(document.querySelectorAll('.product-item'));
        return items.map(item => {
            const name = item.querySelector('a.name')?.innerText.trim() || 'Nom indisponible';
            const price = item.querySelector('.price')?.innerText.trim() || 'Prix indisponible';

            // Extraire le prix par kg (par exemple avec la classe "origin-price")
            const pricePerKg = item.querySelector('.origin-price')?.innerText.trim() || 'Prix par kg indisponible';

            return { name, price, pricePerKg };
        });
    });

    console.log("Données de produits extraites :", JSON.stringify(products, null, 2));

    await browser.close();
})();
