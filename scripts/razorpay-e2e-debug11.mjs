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
await page.waitForTimeout(8000);

console.log(await checkout.locator('body').innerText());

await browser.close();
