const puppeteer = require('puppeteer');

const baseUrl = process.argv[2];

(async () => {
    const browser = await puppeteer.launch();
    const page = await browser.newPage();

    let allProducts = [];
    let pageIndex = 1;

    while (true) {
        const url = `${baseUrl}&p=${pageIndex}`;
        await page.goto(url, { waitUntil: 'networkidle2' });
        try {
            await page.waitForSelector('.vignette-grille-produit-component', { timeout: 10000 });
        } catch (error) {
            break;
        }

        // Extraire les informations des produits
        const products = await page.evaluate(() => {
            const items = Array.from(document.querySelectorAll('.vignette-grille-produit-component'));
            return items.map(item => {
                const name = item.querySelector('.label-container.link a')?.getAttribute('alt') || item.querySelector('.label-container.link a')?.innerText || 'Nom indisponible';
                const price = item.querySelector('.prix-block')?.innerText.trim() || 'Prix indisponible';

                // Extraire le prix par kilogramme (ou par unité) si disponible
                const pricePerKgEntier = item.querySelector('.prix .entier')?.innerText.trim() || '';
                const pricePerKgDecimal = item.querySelector('.prix .decimal')?.innerText.trim() || '';
                const pricePerKgDevise = item.querySelector('.prix .devise')?.innerText.trim() || '';
                const pricePerKgUnit = item.querySelector('.prix .parUnite')?.innerText.trim() || '';

                // Construire le prix par kg complet sans double virgule
                let pricePerKg = 'Prix par kg indisponible';
                if (pricePerKgEntier && pricePerKgDevise && pricePerKgUnit) {
                    pricePerKg = pricePerKgEntier;
                    if (pricePerKgDecimal) {
                        pricePerKg += `,${pricePerKgDecimal}`;
                    }
                    pricePerKg += ` ${pricePerKgDevise} ${pricePerKgUnit}`;
                }

                return { name, price, pricePerKg };
            });
        });

        if (products.length === 0) {
            break;
        }
        allProducts = allProducts.concat(products);
        pageIndex++;
    }

    console.log(JSON.stringify(allProducts, null, 2));

    await browser.close();
})();
