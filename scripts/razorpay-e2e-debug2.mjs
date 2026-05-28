import { chromium } from 'playwright';
import { pathToFileURL } from 'url';

const htmlPath = process.argv[2];
const pageUrl = pathToFileURL(htmlPath).href;

const browser = await chromium.launch({ headless: true });
const page = await browser.newPage();
await page.goto(pageUrl);
await page.click('#pay');
await page.waitForTimeout(8000);

const checkout = page.frameLocator('iframe[src*="api.razorpay.com/v1/checkout"]');
const texts = await checkout.locator('button, a, [role="button"], label').allTextContents();
console.log('Buttons/labels:', texts.slice(0, 40));

const inputs = await checkout.locator('input').count();
console.log('Input count:', inputs);

try {
    const cardTab = checkout.getByText('Card', { exact: false }).first();
    await cardTab.click({ timeout: 5000 });
    await page.waitForTimeout(2000);
    console.log('After Card click, inputs:', await checkout.locator('input').count());
} catch (e) {
    console.log('Card tab:', e.message);
}

await browser.close();
