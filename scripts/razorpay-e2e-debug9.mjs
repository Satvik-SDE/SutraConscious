import { chromium } from 'playwright';
import { pathToFileURL } from 'url';

const htmlPath = process.argv[2];
const pageUrl = pathToFileURL(htmlPath).href;

const browser = await chromium.launch({ headless: true });
const page = await browser.newPage();
await page.goto(pageUrl);
await page.click('#pay');
await page.waitForTimeout(15000);

const checkout = page.frameLocator('iframe[src*="api.razorpay.com/v1/checkout"]');
await checkout.getByTestId('Cards').click({ force: true });
await page.waitForTimeout(5000);

const innerSrc = await checkout.locator('iframe').first().getAttribute('src');
console.log('Inner iframe src:', innerSrc?.slice(0, 150));

const inner = checkout.frameLocator('iframe').first();
console.log('Inner inputs:', await inner.locator('input').count());
const meta = await inner.locator('input').evaluateAll((els) =>
    els.map((i) => ({ name: i.name, placeholder: i.placeholder, type: i.type, ac: i.autocomplete }))
);
console.log(meta);

if (meta.length) {
    const card = inner.locator('input[autocomplete="cc-number"], input').first();
    await card.fill('4111111111111111');
    const exp = inner.locator('input[autocomplete="cc-exp"], input[placeholder*="MM" i]').first();
    if (await exp.count()) await exp.fill('1230');
    const cvv = inner.locator('input[autocomplete="cc-csc"]').first();
    if (await cvv.count()) await cvv.fill('123');
    await checkout.getByRole('button', { name: /pay/i }).click({ timeout: 10000 }).catch(() => {});
    await page.waitForTimeout(20000);
    console.log('Result:', await page.evaluate(() => ({ r: window.__razorpayResult, e: window.__razorpayError })));
}

await browser.close();
