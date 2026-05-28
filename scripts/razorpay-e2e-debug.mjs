import { chromium } from 'playwright';
import { pathToFileURL } from 'url';

const htmlPath = process.argv[2];
const pageUrl = pathToFileURL(htmlPath).href;

const browser = await chromium.launch({ headless: true });
const page = await browser.newPage();
await page.goto(pageUrl);
await page.click('#pay');
await page.waitForTimeout(8000);

console.log('Frames:', page.frames().map((f) => ({ url: f.url(), name: f.name() })));
console.log('Iframes in DOM:', await page.locator('iframe').evaluateAll((els) =>
    els.map((e) => ({ src: e.src, title: e.title, name: e.name, id: e.id }))
));

await page.screenshot({ path: htmlPath.replace('.html', '-debug.png'), fullPage: true });
await browser.close();
