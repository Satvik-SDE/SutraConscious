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
const allText = await checkout.locator('body').innerText();
console.log(allText.slice(0, 3000));

const links = await checkout.locator('a, button, span').filter({ hasText: /UPI|VPA|card|Card/i }).allTextContents();
console.log('UPI/Card related:', links.filter(Boolean).slice(0, 30));

await browser.close();
