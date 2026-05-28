import { chromium } from 'playwright';
import path from 'path';
import { pathToFileURL } from 'url';
import { writeFileSync } from 'fs';

const htmlPath = process.argv[2];
const pageUrl = pathToFileURL(htmlPath).href;

const browser = await chromium.launch({ headless: true });
const page = await browser.newPage();
await page.goto(pageUrl);
await page.click('#pay');
await page.waitForTimeout(12000);

const checkout = page.frameLocator('iframe[src*="api.razorpay.com/v1/checkout"]');
const phone = checkout.locator('input[type="tel"]');
await phone.click();
await phone.fill('');
await phone.pressSequentially('9123456789', { delay: 50 });
await checkout.getByRole('button', { name: 'Continue' }).click();
await page.waitForTimeout(5000);

await checkout.getByTestId('Cards').click({ force: true });
await page.waitForTimeout(5000);

const cardNumber = checkout.locator('input[autocomplete="cc-number"], input[placeholder*="card" i]').first();
if (await cardNumber.count()) {
    await cardNumber.fill('4111111111111111');
    const expiry = checkout.locator('input[autocomplete="cc-exp"]').first();
    if (await expiry.count()) await expiry.fill('12 / 30');
    const cvv = checkout.locator('input[autocomplete="cc-csc"]').first();
    if (await cvv.count()) await cvv.fill('123');
    console.log('Card fields filled');
} else {
    console.log('No card fields');
}

console.log('Buttons:', await checkout.getByRole('button').allTextContents());
console.log('Text tail:', (await checkout.locator('body').innerText()).slice(-800));

await browser.close();
