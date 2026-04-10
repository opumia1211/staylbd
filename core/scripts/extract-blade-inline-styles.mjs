/**
 * Extract static <style>...</style> blocks from Blade files into critical CSS files.
 * Skips blocks that contain Blade/PHP ({{ }}, @if, etc.).
 * Usage: node scripts/extract-blade-inline-styles.mjs [--apply]
 */
import fs from 'fs';
import path from 'path';
import { fileURLToPath } from 'url';

const __dirname = path.dirname(fileURLToPath(import.meta.url));
const viewsRoot = path.join(__dirname, '..', 'resources', 'views');
const coreRoot = path.join(__dirname, '..');
const apply = process.argv.includes('--apply');

const SKIP_FILES = new Set([
    path.normalize('resources/views/templates/basic/layouts/app.blade.php'),
    path.normalize('resources/views/admin/auth/two_factor_recovery_codes.blade.php'),
]);

const DYNAMIC_RE = /\{\{|\}\}|@(if|unless|foreach|forelse|isset|empty|auth|guest|php|section|yield|extends|push|endpush|stack|csrf|method)\b/;
const STYLE_RE = /<style\b[^>]*>([\s\S]*?)<\/style>/gi;

function normRel(p) {
    return path.normalize(path.relative(coreRoot, p)).replace(/\\/g, '/');
}

function skipPath(rel) {
    return SKIP_FILES.has(rel) || SKIP_FILES.has(path.normalize(rel));
}

function isAdminView(rel) {
    return (
        rel.startsWith('resources/views/admin/') ||
        rel.startsWith('resources/views/components/')
    );
}

function classify(rel) {
    if (isAdminView(rel)) return 'admin';
    return 'storefront';
}

function walkBlades(dir) {
    const out = [];
    for (const ent of fs.readdirSync(dir, { withFileTypes: true })) {
        const p = path.join(dir, ent.name);
        if (ent.isDirectory()) out.push(...walkBlades(p));
        else if (ent.name.endsWith('.blade.php')) out.push(p);
    }
    return out;
}

function stripStyleBlocks(content, filePath, bucket) {
    const rel = normRel(filePath);
    if (skipPath(rel)) return { content, extracted: [] };

    let extracted = [];
    let next = content;
    let m;
    const re = new RegExp(STYLE_RE.source, STYLE_RE.flags);
    const matches = [...content.matchAll(re)];
    if (matches.length === 0) return { content, extracted: [] };

    for (const match of matches) {
        const full = match[0];
        const css = match[1].trim();
        if (!css) {
            next = next.replace(full, '');
            continue;
        }
        if (DYNAMIC_RE.test(css)) {
            continue;
        }

        const section = `\n/* --- ${rel} --- */\n${css}\n`;
        extracted.push(section);
        next = next.replace(full, `\n{{-- inline style moved to ${classify(rel) === 'admin' ? 'critical-admin.css' : 'critical-storefront.css'} --}}\n`);
    }

    return { content: next, extracted };
}

function main() {
    const blades = walkBlades(viewsRoot);
    const storefront = ['/* Auto-extracted from Blade <style> blocks — Inter only in source; use Inter, system-ui, sans-serif */'];
    const admin = ['/* Auto-extracted from Blade <style> blocks — admin + shared components */'];

    let touched = 0;
    for (const filePath of blades) {
        const rel = normRel(filePath);
        if (skipPath(rel)) continue;

        const raw = fs.readFileSync(filePath, 'utf8');
        const { content, extracted } = stripStyleBlocks(raw, filePath, null);
        if (extracted.length === 0) continue;

        const bucket = classify(rel);
        if (bucket === 'admin') {
            admin.push(...extracted);
        } else {
            storefront.push(...extracted);
        }

        if (content !== raw) {
            touched++;
            if (apply) {
                fs.writeFileSync(filePath, content, 'utf8');
            }
        }
    }

    const outSf = path.join(coreRoot, 'resources', 'css', 'critical-storefront.css');
    const outAd = path.join(coreRoot, 'resources', 'css', 'critical-admin.css');

    if (!apply) {
        console.log('Dry run. Pass --apply to write blades + critical CSS.');
        console.log('Would write:', outSf, '(sections:', storefront.length, ')');
        console.log('Would write:', outAd, '(sections:', admin.length, ')');
        console.log('Blade files with changes:', touched);
        return;
    }

    fs.writeFileSync(outSf, storefront.join('\n') + '\n', 'utf8');
    fs.writeFileSync(outAd, admin.join('\n') + '\n', 'utf8');
    console.log('Wrote', outSf, storefront.length, 'sections');
    console.log('Wrote', outAd, admin.length, 'sections');
    console.log('Updated blades:', touched);
}

main();
