import { chromium } from 'playwright';
import { pathToFileURL } from 'url';

const htmlPath = process.argv[2];
const pageUrl = pathToFileURL(htmlPath).href;

const browser = await chromium.launch({ headless: true });
const page = await browser.newPage();
await page.goto(pageUrl);
await page.click('#pay');
await page.waitForTimeout(5000);

const checkout = page.frameLocator('iframe[src*="api.razorpay.com/v1/checkout"]');
await page.waitForTimeout(3000);

await checkout.getByTestId('Netbanking').click({ force: true });
await page.waitForTimeout(3000);

console.log('After Netbanking:', await checkout.locator('button, [role="button"]').allTextContents());

const bank = checkout.getByText('HDFC', { exact: false }).first();
if (await bank.count()) {
    await bank.click({ force: true });
    await page.waitForTimeout(2000);
}

const pay = checkout.getByRole('button', { name: /pay/i }).first();
console.log('Pay buttons:', await checkout.getByRole('button').allTextContents());
if (await pay.count()) {
    await pay.click({ force: true });
}

await page.waitForTimeout(15000);
console.log('Result:', await page.evaluate(() => ({ r: window.__razorpayResult, e: window.__razorpayError })));

await browser.close();
