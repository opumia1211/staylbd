import { chromium } from 'playwright';
import fs from 'fs';
import path from 'path';
import { fileURLToPath } from 'url';

const __dirname = path.dirname(fileURLToPath(import.meta.url));
const baseUrl = process.argv[2] || 'http://localhost/sajaladminopu';
const outDir = path.join(__dirname, 'admin-layout-output');
const sessionFile = path.join(outDir, 'session.json');

async function measure(page) {
  return page.evaluate(() => {
    const r = (el) => {
      if (!el) return null;
      const b = el.getBoundingClientRect();
      const s = getComputedStyle(el);
      return { top: Math.round(b.top), bottom: Math.round(b.bottom), height: Math.round(b.height), paddingTop: s.paddingTop, marginTop: s.marginTop, position: s.position };
    };
    const menu = document.querySelector('#layout-menu');
    const navbar = document.querySelector('#layout-navbar');
    const container = document.querySelector('.layout-container');
    const pageEl = document.querySelector('.layout-page');
    const wrapper = document.querySelector('.layout-page > .content-wrapper');
    const header = document.querySelector('.admin-page-header') || document.querySelector('.layout-page h5.fw-bold');
    const welcome = document.querySelector('.card-title');
    return {
      url: location.href,
      navbar: r(navbar),
      menu: r(menu),
      menuOffsetHeight: menu?.offsetHeight ?? null,
      layoutContainer: r(container),
      layoutPage: r(pageEl),
      contentWrapper: r(wrapper),
      pageHeader: r(header),
      welcome: r(welcome),
      gapMenuToHeader: menu && header ? Math.round(header.getBoundingClientRect().top - menu.getBoundingClientRect().bottom) : null,
      gapMenuToWelcome: menu && welcome ? Math.round(welcome.getBoundingClientRect().top - menu.getBoundingClientRect().bottom) : null,
      layoutWrapperMax: getComputedStyle(document.querySelector('.layout-wrapper') || document.body).maxWidth,
      contentMax: wrapper ? getComputedStyle(wrapper).maxWidth : null,
    };
  });
}

fs.mkdirSync(outDir, { recursive: true });

const browser = await chromium.launch({ headless: true });
const contextOpts = { viewport: { width: 1440, height: 900 } };
if (fs.existsSync(sessionFile)) {
  contextOpts.storageState = sessionFile;
}
const context = await browser.newContext(contextOpts);
const page = await context.newPage();

try {
  await page.goto(`${baseUrl}/dashboard`, { waitUntil: 'networkidle', timeout: 30000 });
  await page.waitForTimeout(1500);
  const m = await measure(page);
  console.log('DESKTOP', JSON.stringify(m, null, 2));
  await page.screenshot({ path: path.join(outDir, 'desktop.png') });

  await page.setViewportSize({ width: 390, height: 844 });
  await page.waitForTimeout(800);
  const mobile = await measure(page);
  console.log('MOBILE', JSON.stringify(mobile, null, 2));
  await page.screenshot({ path: path.join(outDir, 'mobile.png') });

  const gap = m.gapMenuToHeader ?? m.gapMenuToWelcome;
  if (gap !== null && gap > 24) {
    console.error(`FAIL: gap below menu is ${gap}px (expected <= 24)`);
    process.exitCode = 1;
  } else if (m.menu) {
    console.log(`OK: gap below menu ~${gap ?? 'n/a'}px`);
  } else {
    console.error('FAIL: not logged in — run: php scripts/admin-browser-session.php');
    process.exitCode = 1;
  }
} catch (e) {
  console.error(e);
  process.exitCode = 1;
} finally {
  await browser.close();
}
