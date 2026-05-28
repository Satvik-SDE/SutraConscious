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
const phone = checkout.locator('input[type="tel"]');
await phone.click({ clickCount: 3 });
await phone.press('Backspace');
await phone.type('9123456789', { delay: 80 });
await page.waitForTimeout(500);
const val = await phone.inputValue();
console.log('Phone value:', val);
await checkout.getByRole('button', { name: 'Continue' }).click();
await page.waitForTimeout(6000);
const text = await checkout.locator('body').innerText();
console.log(text.includes('valid mobile') ? 'STILL INVALID' : 'OK or other');
console.log(text.slice(400, 1200));

await browser.close();
