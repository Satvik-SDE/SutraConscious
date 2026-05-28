import { chromium } from 'playwright';
import { pathToFileURL } from 'url';

const htmlPath = process.argv[2];
const pageUrl = pathToFileURL(htmlPath).href;

const browser = await chromium.launch({ headless: true });
const page = await browser.newPage();
await page.goto(pageUrl);
await page.click('#pay');

const checkout = page.frameLocator('iframe[src*="api.razorpay.com/v1/checkout"]');
await page.waitForTimeout(12000);

await checkout.locator('input[type="tel"]').fill('9876543210');
await checkout.getByTestId('Cards').click({ force: true });
await page.waitForTimeout(3000);

const html = await checkout.locator('body').innerHTML();
console.log('Contains card:', html.includes('card') || html.includes('Card'));
console.log('Snippet:', html.slice(0, 2000));

await browser.close();
