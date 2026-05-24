import puppeteer from 'puppeteer-core';
import fs from 'fs';

(async () => {
    const edgePaths = [
        'C:\\Program Files (x86)\\Microsoft\\Edge\\Application\\msedge.exe',
        'C:\\Program Files\\Microsoft\\Edge\\Application\\msedge.exe'
    ];
    let executablePath = null;
    for (const p of edgePaths) {
        if (fs.existsSync(p)) {
            executablePath = p;
            break;
        }
    }

    if (!executablePath) {
        console.error("Could not find Edge executable.");
        process.exit(1);
    }

    const takeScreenshot = async (page, url, filename) => {
        try {
            await page.goto(url, { waitUntil: 'networkidle0' });
            await new Promise(resolve => setTimeout(resolve, 1500));
            await page.screenshot({ path: `docs/screenshots/${filename}.png`, fullPage: true });
            console.log(`Saved ${filename}.png`);
        } catch (e) {
            console.error(`Error taking screenshot for ${filename}:`, e);
        }
    };

    const launchBrowser = async () => {
        return await puppeteer.launch({
            executablePath: executablePath,
            headless: "new",
            defaultViewport: { width: 1280, height: 800 }
        });
    };

    // --- SUPERADMIN ---
    console.log("Processing Superadmin...");
    let browser = await launchBrowser();
    let page = await browser.newPage();
    await page.goto('http://127.0.0.1:8000/login');
    await page.waitForSelector('input[name="identifier"]');
    await page.type('input[name="identifier"]', 'admin@annawawiy.ac.id');
    await page.type('input[name="password"]', 'password');
    await Promise.all([ page.waitForNavigation({ waitUntil: 'networkidle0' }), page.click('button[type="submit"]') ]);
    
    await takeScreenshot(page, 'http://127.0.0.1:8000/admin/dashboard', 'superadmin_dashboard');
    await takeScreenshot(page, 'http://127.0.0.1:8000/admin/users', 'superadmin_users');
    await takeScreenshot(page, 'http://127.0.0.1:8000/admin/fee-categories', 'superadmin_fee_categories');
    await takeScreenshot(page, 'http://127.0.0.1:8000/admin/fee-masters', 'superadmin_fee_masters');
    await takeScreenshot(page, 'http://127.0.0.1:8000/admin/discounts', 'superadmin_discounts');
    await takeScreenshot(page, 'http://127.0.0.1:8000/admin/reports/financial', 'superadmin_financial_reports');
    await takeScreenshot(page, 'http://127.0.0.1:8000/admin/faqs', 'superadmin_faqs');
    await browser.close();

    // --- ADMIN TU ---
    console.log("Processing Admin TU...");
    browser = await launchBrowser();
    page = await browser.newPage();
    await page.goto('http://127.0.0.1:8000/login');
    await page.waitForSelector('input[name="identifier"]');
    await page.type('input[name="identifier"]', 'tu@annawawiy.ac.id');
    await page.type('input[name="password"]', 'password');
    await Promise.all([ page.waitForNavigation({ waitUntil: 'networkidle0' }), page.click('button[type="submit"]') ]);
    
    await takeScreenshot(page, 'http://127.0.0.1:8000/admin/dashboard', 'admintu_dashboard');
    await takeScreenshot(page, 'http://127.0.0.1:8000/admin/guardians', 'admintu_guardians');
    await takeScreenshot(page, 'http://127.0.0.1:8000/admin/students', 'admintu_students');
    await takeScreenshot(page, 'http://127.0.0.1:8000/admin/spmb-schedules', 'admintu_spmb_schedules');
    await takeScreenshot(page, 'http://127.0.0.1:8000/admin/student-acceptance', 'admintu_student_acceptance');
    await takeScreenshot(page, 'http://127.0.0.1:8000/admin/rombels', 'admintu_rombels');
    await takeScreenshot(page, 'http://127.0.0.1:8000/admin/billings', 'admintu_billings');
    await browser.close();

    // --- WALI SANTRI ---
    console.log("Processing Wali Santri...");
    browser = await launchBrowser();
    page = await browser.newPage();
    await page.goto('http://127.0.0.1:8000/login');
    await page.waitForSelector('input[name="identifier"]');
    await page.type('input[name="identifier"]', '081234567890');
    await page.type('input[name="password"]', 'password');
    await Promise.all([ page.waitForNavigation({ waitUntil: 'networkidle0' }), page.click('button[type="submit"]') ]);
    
    await takeScreenshot(page, 'http://127.0.0.1:8000/my-dashboard', 'walisantri_dashboard');
    await takeScreenshot(page, 'http://127.0.0.1:8000/spmb-schedules', 'walisantri_spmb_schedules');
    await takeScreenshot(page, 'http://127.0.0.1:8000/spmb/register', 'walisantri_spmb_register');
    await takeScreenshot(page, 'http://127.0.0.1:8000/faq', 'walisantri_faq');

    try {
        await page.goto('http://127.0.0.1:8000/my-dashboard', { waitUntil: 'networkidle0' });
        const studentLink = await page.$('a[href^="http://127.0.0.1:8000/students/"]');
        if (studentLink) {
            const href = await page.evaluate(el => el.href, studentLink);
            await takeScreenshot(page, href, 'walisantri_student_detail');
        }
    } catch(e) {
        console.error("Could not navigate to student detail", e);
    }

    await browser.close();
})();
