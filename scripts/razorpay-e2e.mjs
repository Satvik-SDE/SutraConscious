import { chromium } from 'playwright';
import path from 'path';
import { pathToFileURL } from 'url';
import { writeFileSync } from 'fs';

const htmlPath = process.argv[2];
if (!htmlPath) {
    console.error('Usage: node razorpay-e2e.mjs <path-to-html>');
    process.exit(1);
}

const pageUrl = pathToFileURL(htmlPath).href;

const browser = await chromium.launch({ headless: true });
const page = await browser.newPage();

try {
    await page.goto(pageUrl);
    await page.click('#pay');

    const checkout = page.frameLocator('iframe[src*="api.razorpay.com/v1/checkout"]');
    await page.waitForTimeout(12000);

    await checkout.getByTestId('Cards').click({ force: true });
    await page.waitForTimeout(4000);

    const cardNumber = checkout.locator(
        'input[autocomplete="cc-number"], input[placeholder*="card" i]'
    ).first();
    await cardNumber.waitFor({ state: 'visible', timeout: 30000 });
    await cardNumber.fill('4111111111111111');

    const expiry = checkout.locator('input[autocomplete="cc-exp"]').first();
    if (await expiry.count()) {
        await expiry.fill('12 / 30');
    }

    const cvv = checkout.locator('input[autocomplete="cc-csc"]').first();
    if (await cvv.count()) {
        await cvv.fill('123');
    }

    await checkout.getByRole('button', { name: 'Continue' }).click({ timeout: 20000 });
    await page.waitForTimeout(3000);

    const otp = checkout.locator('input[name*="otp" i], input[placeholder*="OTP" i], input[inputmode="numeric"]').first();
    if (await otp.count()) {
        await otp.fill('1111');
        await page.waitForTimeout(1000);
    }

    const payButton = checkout.locator('button').filter({ hasText: /pay|submit|continue|₹/i }).last();
    if (await payButton.count()) {
        await payButton.click({ timeout: 20000 }).catch(() => {});
    }

    await page.waitForFunction(
        () => window.__razorpayResult || window.__razorpayError,
        { timeout: 120000 }
    );

    const result = await page.evaluate(() => ({
        result: window.__razorpayResult,
        error: window.__razorpayError,
    }));

    if (result.error || !result.result) {
        console.error('Payment did not succeed:', result.error || 'unknown');
        process.exit(1);
    }

    const outPath = path.join(path.dirname(htmlPath), 'razorpay-e2e-result.json');
    writeFileSync(outPath, JSON.stringify(result.result, null, 2));
    console.log('Captured payment:', result.result.razorpay_payment_id);
} finally {
    await browser.close();
}
