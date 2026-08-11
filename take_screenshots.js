import puppeteer from 'puppeteer';
import fs from 'fs';

const delay = ms => new Promise(res => setTimeout(res, ms));

(async () => {
    const dir = 'docs/images/manual_usuario';
    if (!fs.existsSync(dir)){
        fs.mkdirSync(dir, { recursive: true });
    }

    console.log('Lanzando navegador...');
    const browser = await puppeteer.launch({
        headless: "new",
        defaultViewport: { width: 1280, height: 800 }
    });
    const page = await browser.newPage();
    
    // 1. Inicio
    console.log('Capturando inicio...');
    await page.goto('http://guardianapp.test', { waitUntil: 'networkidle2' });
    await delay(1000);
    await page.screenshot({ path: `${dir}/1_inicio_dashboard.png` });

    // 2. Registro
    console.log('Capturando registro...');
    await page.goto('http://guardianapp.test/register', { waitUntil: 'networkidle2' });
    await delay(1000);
    await page.screenshot({ path: `${dir}/2_registro.png` });

    // 3. Login
    console.log('Capturando login...');
    await page.goto('http://guardianapp.test/login', { waitUntil: 'networkidle2' });
    await delay(1000);
    await page.screenshot({ path: `${dir}/3_login.png` });

    // 4. Hacer login
    console.log('Haciendo login...');
    await page.type('input[name="email"]', 'ciudadano@webgis.local');
    await page.type('input[name="password"]', 'SecurePass123!');
    await Promise.all([
        page.waitForNavigation({ waitUntil: 'networkidle2' }),
        page.click('button[type="submit"]')
    ]);

    // 5. Dashboard autenticado
    console.log('Capturando dashboard autenticado...');
    await delay(2000); // Wait for maps/charts
    await page.screenshot({ path: `${dir}/4_dashboard_autenticado.png` });

    // 6. Reportar incidente
    console.log('Capturando reporte...');
    await page.goto('http://guardianapp.test/report', { waitUntil: 'networkidle2' });
    await delay(1000);
    await page.screenshot({ path: `${dir}/5_reportar_incidente.png` });

    // 7. Perfil incidentes
    console.log('Capturando perfil...');
    await page.goto('http://guardianapp.test/profile/incidents', { waitUntil: 'networkidle2' });
    await delay(1000);
    await page.screenshot({ path: `${dir}/6_perfil_incidentes.png` });

    await browser.close();
    console.log('Capturas completadas.');
})();
