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
await checkout.getByTestId('Cards').click({ force: true });
await page.waitForTimeout(2000);

const meta = await checkout.locator('input').evaluateAll((inputs) =>
    inputs.map((i) => ({
        name: i.name,
        type: i.type,
        placeholder: i.placeholder,
        id: i.id,
        autocomplete: i.autocomplete,
        visible: i.offsetParent !== null,
    }))
);
console.log(JSON.stringify(meta, null, 2));

await browser.close();
