import { chromium } from 'playwright';
import { pathToFileURL } from 'url';

const htmlPath = process.argv[2];
const pageUrl = pathToFileURL(htmlPath).href;

const browser = await chromium.launch({ headless: true });
const page = await browser.newPage();
await page.goto(pageUrl);
await page.click('#pay');
await page.waitForTimeout(12000);

const checkout = page.frameLocator('iframe[src*="api.razorpay.com/v1/checkout"]');
await checkout.getByTestId('Cards').click({ force: true });
await page.waitForTimeout(4000);

const cardNumber = checkout.locator('input[autocomplete="cc-number"], input[placeholder*="card" i]').first();
console.log('Card count:', await cardNumber.count());
if (await cardNumber.count()) {
    await cardNumber.fill('4111111111111111');
    await checkout.locator('input[autocomplete="cc-exp"]').first().fill('12 / 30').catch(() => {});
    await checkout.locator('input[autocomplete="cc-csc"]').first().fill('123').catch(() => {});
}

const buttons = await checkout.locator('button').allTextContents();
console.log('All buttons:', buttons);
console.log('Visible text:', (await checkout.locator('body').innerText()).slice(-600));

await browser.close();
