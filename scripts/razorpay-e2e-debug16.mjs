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
await page.waitForTimeout(3000);
await checkout.locator('input[autocomplete="cc-number"]').first().fill('4111111111111111');
await checkout.locator('input[autocomplete="cc-exp"]').first().fill('12 / 30');
await checkout.locator('input[autocomplete="cc-csc"]').first().fill('123');
await checkout.getByRole('button', { name: 'Continue' }).click();
await page.waitForTimeout(8000);

console.log('Inputs:', await checkout.locator('input').evaluateAll((els) =>
    els.map((i) => ({ ph: i.placeholder, name: i.name, type: i.type }))
));
console.log('Buttons:', await checkout.getByRole('button').allTextContents());
console.log('Text:', (await checkout.locator('body').innerText()).slice(-500));
console.log('Page result:', await page.evaluate(() => ({ r: window.__razorpayResult, e: window.__razorpayError })));

await browser.close();
