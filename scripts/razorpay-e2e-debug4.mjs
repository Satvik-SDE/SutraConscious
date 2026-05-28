import { chromium } from 'playwright';
import { pathToFileURL } from 'url';

const htmlPath = process.argv[2];
const pageUrl = pathToFileURL(htmlPath).href;

const browser = await chromium.launch({ headless: true });
const page = await browser.newPage();
await page.goto(pageUrl);
await page.click('#pay');

const checkout = page.frameLocator('iframe[src*="api.razorpay.com/v1/checkout"]');
await page.waitForTimeout(10000);

// Wait for overlay gone
await checkout.locator('[data-testid^="overlay-"]').waitFor({ state: 'hidden', timeout: 30000 }).catch(() => {});

const phone = checkout.locator('input[type="tel"], input[name*="phone" i]').first();
if (await phone.count()) {
    await phone.fill('9999999999');
}

await checkout.getByTestId('Cards').click({ force: true, timeout: 20000 });
await page.waitForTimeout(3000);

console.log('Frames after Cards:', page.frames().length);
for (const f of page.frames()) {
    if (f.url().includes('razorpay')) console.log('  ', f.url().slice(0, 120));
}

console.log('Inputs in checkout:', await checkout.locator('input').count());
for (let i = 0; i < 10; i++) {
    const frames = page.frames().filter((f) => f.url().includes('razorpay') && !f.url().includes('checkout/public'));
    for (const f of frames) {
        const c = await f.locator('input').count().catch(() => 0);
        if (c) console.log('Frame inputs', f.url().slice(0, 80), c);
    }
}

await page.screenshot({ path: htmlPath.replace('.html', '-cards.png'), fullPage: true });
await browser.close();
