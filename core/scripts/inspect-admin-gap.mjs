import { chromium } from 'playwright';

const url = process.argv[2] || 'http://localhost/sajaladminopu/dashboard';

try {
  const browser = await chromium.launch({ headless: true });
  const page = await browser.newPage({ viewport: { width: 1400, height: 900 } });
  await page.goto(url, { waitUntil: 'domcontentloaded', timeout: 15000 });
  const data = await page.evaluate(() => {
    const pick = (sel) => {
      const el = document.querySelector(sel);
      if (!el) return null;
      const r = el.getBoundingClientRect();
      const s = getComputedStyle(el);
      return {
        sel,
        top: Math.round(r.top),
        height: Math.round(r.height),
        paddingTop: s.paddingTop,
        marginTop: s.marginTop,
        position: s.position,
      };
    };
    return {
      menu: pick('#layout-menu'),
      page: pick('.layout-page'),
      wrapper: pick('.content-wrapper'),
      container: pick('.content-wrapper .container-p-y'),
      breadcrumb: pick('.breadcrumb') || pick('.layout-page h5'),
      welcome: pick('.card-title'),
    };
  });
  console.log(JSON.stringify(data, null, 2));
  await browser.close();
} catch (e) {
  console.error('SKIP:', e.message);
  process.exit(0);
}
