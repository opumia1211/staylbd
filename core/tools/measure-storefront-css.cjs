/**
 * Report raw / gzip / brotli sizes for key storefront CSS (run after `npm run production`).
 * Usage: node tools/measure-storefront-css.cjs
 */
const fs = require('fs');
const path = require('path');
const zlib = require('zlib');
const { promisify } = require('util');

const gzip = promisify(zlib.gzip);
const brotliCompress = zlib.brotliCompress ? promisify(zlib.brotliCompress) : null;

const root = path.join(__dirname, '../public/css');
const files = [
    'tailwind-homepage.css',
    'tailwind-product.css',
    'critical-storefront.css',
    'tailwind-storefront-deferred.css',
    'tailwind-storefront-deferred-home.css',
    'tailwind-storefront-deferred-cart.css',
    'tailwind-storefront-deferred-account.css',
];

async function measure(rel) {
    const p = path.join(root, rel);
    if (!fs.existsSync(p)) {
        return { rel, error: 'missing' };
    }
    const raw = fs.readFileSync(p);
    const gz = await gzip(raw, { level: 9 });
    let br = null;
    if (brotliCompress) {
        br = await brotliCompress(raw, {
            params: { [zlib.constants.BROTLI_PARAM_QUALITY]: 6 },
        });
    }
    return {
        rel,
        raw: raw.length,
        gzip: gz.length,
        brotli: br ? br.length : null,
    };
}

(async () => {
    const rows = [];
    for (const f of files) {
        rows.push(await measure(f));
    }
    console.log('\nStorefront CSS sizes (bytes)\n');
    console.log('file\t\traw\tgzip\tbrotli');
    for (const r of rows) {
        if (r.error) {
            console.log(r.rel, r.error);
            continue;
        }
        console.log(
            `${r.rel}\t${r.raw}\t${r.gzip}\t${r.brotli != null ? r.brotli : 'n/a'}`
        );
    }
    const homeBlocking = rows.find((r) => r.rel === 'tailwind-homepage.css');
    const homeDeferred = rows.find((r) => r.rel === 'tailwind-storefront-deferred-home.css');
    const pdpBlocking = rows.find((r) => r.rel === 'tailwind-product.css');
    const pdpDeferred = rows.find((r) => r.rel === 'tailwind-storefront-deferred.css');
    const crit = rows.find((r) => r.rel === 'critical-storefront.css');

    function sumBrotli(a, b, c) {
        if (!a || !b || a.brotli == null || b.brotli == null) return null;
        let t = a.brotli + b.brotli;
        if (c && c.brotli != null) t += c.brotli;
        return t;
    }

    console.log('\n--- Typical compressed totals (Brotli, separate responses) ---');
    const homeTotal = sumBrotli(homeBlocking, crit, homeDeferred);
    const pdpTotal = sumBrotli(pdpBlocking, crit, pdpDeferred);
    if (homeTotal != null) {
        console.log(`Home (blocking+critical+deferred): ${homeTotal} bytes Brotli`);
    }
    if (pdpTotal != null) {
        console.log(`PDP/catalog (blocking+critical+deferred): ${pdpTotal} bytes Brotli`);
    }
    console.log('');
})();
