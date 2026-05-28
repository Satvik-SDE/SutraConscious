import { chromium } from 'playwright';
import { pathToFileURL } from 'url';

const htmlPath = process.argv[2];
const pageUrl = pathToFileURL(htmlPath).href;

const browser = await chromium.launch({ headless: true });
const page = await browser.newPage();
await page.goto(pageUrl);
await page.click('#pay');
await page.waitForTimeout(15000);

const checkoutFrame = page.frame({ url: /api\.razorpay\.com\/v1\/checkout\/public/ });
if (!checkoutFrame) {
    console.log('No checkout frame');
    process.exit(1);
}

await checkoutFrame.getByTestId('Cards').click({ force: true });
await page.waitForTimeout(8000);

console.log('Child frames:', checkoutFrame.childFrames().map((f) => f.url().slice(0, 100)));
console.log('Iframe count in checkout:', await checkoutFrame.locator('iframe').count());

for (const child of checkoutFrame.childFrames()) {
    const inputs = await child.locator('input').count().catch(() => 0);
    if (inputs) {
        console.log('Child frame inputs', inputs, child.url().slice(0, 80));
        const meta = await child.locator('input').evaluateAll((els) =>
            els.map((i) => ({ name: i.name, placeholder: i.placeholder, type: i.type }))
        );
        console.log(meta);
    }
}

// Try nested frameLocator
const nested = page.frameLocator('iframe[src*="api.razorpay.com/v1/checkout"]').frameLocator('iframe');
console.log('Nested iframe count:', await nested.locator('*').count().catch(() => 'err'));

await browser.close();
