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
await checkout.locator('[data-testid^="overlay-"]').waitFor({ state: 'hidden', timeout: 30000 }).catch(() => {});

await checkout.locator('input[type="tel"]').fill('9876543210');
await checkout.getByRole('button', { name: 'Continue' }).click();
await page.waitForTimeout(5000);

console.log('Inputs after continue:', await checkout.locator('input').evaluateAll((inputs) =>
    inputs.map((i) => ({ name: i.name, type: i.type, placeholder: i.placeholder, visible: i.offsetParent !== null }))
));

console.log('Buttons:', await checkout.getByRole('button').allTextContents());
console.log('Result:', await page.evaluate(() => ({ r: window.__razorpayResult, e: window.__razorpayError })));

await browser.close();
