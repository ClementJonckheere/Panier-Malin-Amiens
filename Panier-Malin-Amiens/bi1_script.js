const puppeteer = require('puppeteer');

(async () => {
    const url = process.argv[2];
    const browser = await puppeteer.launch({
        executablePath: process.env.PUPPETEER_EXECUTABLE_PATH || '/usr/bin/chromium',
        headless: true, // Utilisez headless pour éviter d'ouvrir une fenêtre
        args: ['--no-sandbox', '--disable-setuid-sandbox'], // Nécessaire pour Puppeteer dans Docker
    });
    const page = await browser.newPage();

    await page.setUserAgent('Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/87.0.4280.88 Safari/537.36');

    await page.goto(url, { waitUntil: 'networkidle2' });

    await new Promise(resolve => setTimeout(resolve, 3000));

    const products = await page.evaluate(() => {
        const items = Array.from(document.querySelectorAll('.product-item'));
        return items.map(item => {
            const name = item.querySelector('a.name')?.innerText.trim() || 'Nom indisponible';
            const price = item.querySelector('.price')?.innerText.trim() || 'Prix indisponible';

            const pricePerKg = item.querySelector('.origin-price')?.innerText.trim() || 'Prix par kg indisponible';

            return { name, price, pricePerKg };
        });
    });

    console.log(JSON.stringify(products));

    await browser.close();
})();
