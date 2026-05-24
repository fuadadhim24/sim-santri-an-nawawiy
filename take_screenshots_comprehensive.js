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

    const launchBrowser = async () => {
        return await puppeteer.launch({
            executablePath: executablePath,
            headless: "new",
            defaultViewport: { width: 1280, height: 800 }
        });
    };

    const takeScreenshot = async (page, url, filename, action = null) => {
        try {
            await page.goto(url, { waitUntil: 'networkidle0' });
            await new Promise(resolve => setTimeout(resolve, 1500)); // wait for livewire

            if (action) {
                await action(page);
                await new Promise(resolve => setTimeout(resolve, 1500)); // wait for action (modal/alert)
            }

            await page.screenshot({ path: `docs/screenshots/${filename}.png`, fullPage: true });
            console.log(`Saved ${filename}.png`);
        } catch (e) {
            console.error(`Error taking screenshot for ${filename} at ${url}:`, e);
        }
    };

    // --- SUPERADMIN ---
    console.log("Processing Superadmin (Full Flow)...");
    let browser = await launchBrowser();
    let page = await browser.newPage();
    await page.goto('http://127.0.0.1:8000/login');
    await page.waitForSelector('input[name="identifier"]');
    await page.type('input[name="identifier"]', 'admin@annawawiy.ac.id');
    await page.type('input[name="password"]', 'password');
    await Promise.all([ page.waitForNavigation({ waitUntil: 'networkidle0' }), page.click('button[type="submit"]') ]);
    
    await takeScreenshot(page, 'http://127.0.0.1:8000/admin/dashboard', 'superadmin_dashboard');
    
    // Users
    await takeScreenshot(page, 'http://127.0.0.1:8000/admin/users', 'superadmin_users_index');
    await takeScreenshot(page, 'http://127.0.0.1:8000/admin/users/create', 'superadmin_users_create');
    await takeScreenshot(page, 'http://127.0.0.1:8000/admin/users/2/edit', 'superadmin_users_edit'); // 2 is Admin TU
    // Modal Hapus User
    await takeScreenshot(page, 'http://127.0.0.1:8000/admin/users', 'superadmin_users_delete_modal', async (p) => {
        const deleteBtn = await p.$('button[wire\\:click*="confirmDelete"]');
        if (deleteBtn) await deleteBtn.click();
    });

    // Fee Categories
    await takeScreenshot(page, 'http://127.0.0.1:8000/admin/fee-categories', 'superadmin_fee_categories_index');
    await takeScreenshot(page, 'http://127.0.0.1:8000/admin/fee-categories/create', 'superadmin_fee_categories_create');
    await takeScreenshot(page, 'http://127.0.0.1:8000/admin/fee-categories/1/edit', 'superadmin_fee_categories_edit');

    // Fee Masters
    await takeScreenshot(page, 'http://127.0.0.1:8000/admin/fee-masters', 'superadmin_fee_masters_index');
    await takeScreenshot(page, 'http://127.0.0.1:8000/admin/fee-masters/create', 'superadmin_fee_masters_create');
    await takeScreenshot(page, 'http://127.0.0.1:8000/admin/fee-masters/1/edit', 'superadmin_fee_masters_edit');
    await takeScreenshot(page, 'http://127.0.0.1:8000/admin/fee-masters/archive', 'superadmin_fee_masters_archive');

    // Discounts
    await takeScreenshot(page, 'http://127.0.0.1:8000/admin/discounts', 'superadmin_discounts_index');
    await takeScreenshot(page, 'http://127.0.0.1:8000/admin/discounts/create', 'superadmin_discounts_create');
    await takeScreenshot(page, 'http://127.0.0.1:8000/admin/discounts/1/edit', 'superadmin_discounts_edit');

    // Financial Reports
    await takeScreenshot(page, 'http://127.0.0.1:8000/admin/reports/financial', 'superadmin_financial_reports');

    // FAQs
    await takeScreenshot(page, 'http://127.0.0.1:8000/admin/faqs', 'superadmin_faqs_index');
    await takeScreenshot(page, 'http://127.0.0.1:8000/admin/faqs/create', 'superadmin_faqs_create');
    await takeScreenshot(page, 'http://127.0.0.1:8000/admin/faqs/1/edit', 'superadmin_faqs_edit');

    await browser.close();

    // --- ADMIN TU ---
    console.log("Processing Admin TU (Full Flow)...");
    browser = await launchBrowser();
    page = await browser.newPage();
    await page.goto('http://127.0.0.1:8000/login');
    await page.waitForSelector('input[name="identifier"]');
    await page.type('input[name="identifier"]', 'tu@annawawiy.ac.id');
    await page.type('input[name="password"]', 'password');
    await Promise.all([ page.waitForNavigation({ waitUntil: 'networkidle0' }), page.click('button[type="submit"]') ]);
    
    await takeScreenshot(page, 'http://127.0.0.1:8000/admin/dashboard', 'admintu_dashboard');
    
    // Guardians
    await takeScreenshot(page, 'http://127.0.0.1:8000/admin/guardians', 'admintu_guardians_index');
    await takeScreenshot(page, 'http://127.0.0.1:8000/admin/guardians/create', 'admintu_guardians_create');
    await takeScreenshot(page, 'http://127.0.0.1:8000/admin/guardians/1/edit', 'admintu_guardians_edit');

    // Students
    await takeScreenshot(page, 'http://127.0.0.1:8000/admin/students', 'admintu_students_index');
    await takeScreenshot(page, 'http://127.0.0.1:8000/admin/students/create', 'admintu_students_create');
    await takeScreenshot(page, 'http://127.0.0.1:8000/admin/students/1/edit', 'admintu_students_edit');
    await takeScreenshot(page, 'http://127.0.0.1:8000/admin/students/1', 'admintu_students_detail'); // Show detail

    // SPMB Schedules
    await takeScreenshot(page, 'http://127.0.0.1:8000/admin/spmb-schedules', 'admintu_spmb_schedules_index');
    await takeScreenshot(page, 'http://127.0.0.1:8000/admin/spmb-schedules/create', 'admintu_spmb_schedules_create');
    await takeScreenshot(page, 'http://127.0.0.1:8000/admin/spmb-schedules/1/edit', 'admintu_spmb_schedules_edit');

    // Student Acceptance
    await takeScreenshot(page, 'http://127.0.0.1:8000/admin/student-acceptance', 'admintu_student_acceptance_index');
    // We assume ID 3 (Fatima Binti Usman) is pending
    await takeScreenshot(page, 'http://127.0.0.1:8000/admin/student-acceptance/3/confirm', 'admintu_student_acceptance_confirm');
    
    // Rombels
    await takeScreenshot(page, 'http://127.0.0.1:8000/admin/rombels', 'admintu_rombels_index');

    // Billings
    await takeScreenshot(page, 'http://127.0.0.1:8000/admin/billings', 'admintu_billings_index');
    await takeScreenshot(page, 'http://127.0.0.1:8000/admin/billings/create', 'admintu_billings_create');
    await takeScreenshot(page, 'http://127.0.0.1:8000/admin/billings/archive', 'admintu_billings_archive');

    // Trying to get a receipt view if billing ID 1 exists
    await takeScreenshot(page, 'http://127.0.0.1:8000/receipts/1', 'admintu_receipt_view');

    await browser.close();

    // --- WALI SANTRI ---
    console.log("Processing Wali Santri (Full Flow)...");
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
