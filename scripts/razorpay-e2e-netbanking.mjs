import { chromium } from 'playwright';
import path from 'path';
import { pathToFileURL } from 'url';
import { writeFileSync } from 'fs';

const htmlPath = process.argv[2];
const pageUrl = pathToFileURL(htmlPath).href;

const browser = await chromium.launch({ headless: true });
const page = await browser.newPage();

try {
    await page.goto(pageUrl);
    await page.click('#pay');
    const checkout = page.frameLocator('iframe[src*="api.razorpay.com/v1/checkout"]');
    await page.waitForTimeout(12000);

    await checkout.getByTestId('Netbanking').click({ force: true });
    await page.waitForTimeout(3000);

    await checkout.getByText('HDFC', { exact: false }).first().click({ timeout: 15000 });
    await page.waitForTimeout(2000);

    await checkout.getByRole('button', { name: /pay|continue/i }).last().click({ timeout: 15000 });
    await page.waitForTimeout(5000);

    // Test mode success page / redirect
    await checkout.getByRole('button', { name: /success|simulate/i }).click({ timeout: 10000 }).catch(() => {});

    await page.waitForFunction(
        () => window.__razorpayResult || window.__razorpayError,
        null,
        { timeout: 120000 }
    );

    const result = await page.evaluate(() => ({
        result: window.__razorpayResult,
        error: window.__razorpayError,
    }));

    if (!result.result) {
        console.error('Failed:', result.error);
        process.exit(1);
    }

    writeFileSync(
        path.join(path.dirname(htmlPath), 'razorpay-e2e-result.json'),
        JSON.stringify(result.result, null, 2)
    );
    console.log('Payment:', result.result.razorpay_payment_id);
} finally {
    await browser.close();
}
