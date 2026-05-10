<?php
?><!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
<meta charset="<?php bloginfo('charset'); ?>">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title><?php _e('Legal Notice', 'nonla'); ?> — Nonla Cultural Escape</title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:ital,wght@0,400;0,600;1,400;1,600&family=Inter:wght@300;400;500;600&family=Bebas+Neue&display=swap" rel="stylesheet">
<?php wp_head(); ?>
<style>
*, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
:root {
  --cream:   #FAF5EC;
  --parch:   #F2E8D4;
  --white:   #FFFDF7;
  --terra:   #B85520;
  --terra-l: #D4703A;
  --gold-l:  #D4A030;
  --text:    #1A0E06;
  --text2:   #4A3318;
  --muted:   #7A6040;
  --border:  #DDD0B0;
  --card:    #FFF8EE;
  --head:    'Cormorant Garamond', serif;
  --body:    'Inter', sans-serif;
  --display: 'Bebas Neue', sans-serif;
}
html { scroll-behavior: smooth; }
body { background: var(--cream); color: var(--text); font-family: var(--body); }
a { color: var(--terra); text-decoration: none; }
a:hover { text-decoration: underline; }
img { display: block; max-width: 100%; }

/* NAV */
.nav {
  position: sticky; top: 0; z-index: 200;
  display: flex; align-items: center; justify-content: space-between;
  padding: 20px 52px;
  background: rgba(250,245,236,.96); backdrop-filter: blur(12px);
  border-bottom: 1px solid var(--border);
}
.logo { font-family: var(--display); font-size: 24px; letter-spacing: .1em; color: var(--text); text-decoration: none; }
.logo span { color: var(--gold-l); }
.nav-links { display: flex; gap: 32px; list-style: none; }
.nav-links a { font-size: 12px; letter-spacing: .12em; text-transform: uppercase; color: var(--muted); text-decoration: none; transition: color .2s; }
.nav-links a:hover { color: var(--text); }
.nav-right { display: flex; align-items: center; gap: 14px; }
.lang-toggle { display: flex; gap: 3px; }
.lang-btn {
  font-size: 11px; font-weight: 600; letter-spacing: .1em; text-transform: uppercase;
  padding: 5px 10px; border-radius: 2px; cursor: pointer; border: none;
  background: transparent; color: var(--muted); transition: all .2s;
}
.lang-btn.active { background: var(--terra); color: #fff; }
.lang-btn:hover:not(.active) { color: var(--text); }

/* PAGE LAYOUT */
.page-header { padding: 64px 52px 48px; background: var(--parch); border-bottom: 1px solid var(--border); }
.page-header-inner { max-width: 760px; margin: 0 auto; }
.breadcrumb { font-size: 12px; color: var(--muted); letter-spacing: .06em; margin-bottom: 20px; }
.breadcrumb a { color: var(--terra); text-decoration: none; }
.page-title { font-family: var(--head); font-size: clamp(40px, 6vw, 72px); font-weight: 600; line-height: .95; color: var(--text); margin-bottom: 16px; }
.page-title em { font-style: italic; color: var(--terra); }
.page-subtitle { font-size: 15px; color: var(--text2); line-height: 1.7; font-weight: 300; }

.content-wrap { max-width: 760px; margin: 0 auto; padding: 64px 52px 100px; }

/* TOC */
.toc { background: var(--card); border: 1px solid var(--border); border-radius: 6px; padding: 28px 32px; margin-bottom: 56px; }
.toc-title { font-size: 11px; font-weight: 700; letter-spacing: .2em; text-transform: uppercase; color: var(--muted); margin-bottom: 16px; }
.toc-list { list-style: none; display: flex; flex-direction: column; gap: 8px; }
.toc-list a { font-size: 14px; color: var(--terra); text-decoration: none; }
.toc-list a:hover { text-decoration: underline; }
.toc-list .toc-num { color: var(--muted); margin-right: 8px; font-size: 12px; }

/* SECTIONS */
.legal-section { margin-bottom: 56px; padding-bottom: 56px; border-bottom: 1px solid var(--border); }
.legal-section:last-child { border-bottom: none; }
.section-num { font-size: 11px; font-weight: 700; letter-spacing: .2em; text-transform: uppercase; color: var(--terra); margin-bottom: 10px; }
.section-heading { font-family: var(--head); font-size: 32px; font-weight: 600; color: var(--text); margin-bottom: 20px; line-height: 1.1; }
.legal-section p { font-size: 15px; color: var(--text2); line-height: 1.8; font-weight: 300; margin-bottom: 14px; }
.legal-section p:last-child { margin-bottom: 0; }
.legal-section strong { color: var(--text); font-weight: 600; }
.legal-section ul { list-style: none; margin: 16px 0; display: flex; flex-direction: column; gap: 10px; }
.legal-section li { font-size: 15px; color: var(--text2); line-height: 1.7; font-weight: 300; padding-left: 20px; position: relative; }
.legal-section li::before { content: '—'; position: absolute; left: 0; color: var(--terra); }

/* INFO BOX */
.info-box { background: var(--parch); border: 1px solid var(--border); border-radius: 6px; padding: 24px 28px; margin: 24px 0; }
.info-box p { font-size: 14px; color: var(--text2); line-height: 1.7; margin-bottom: 8px; }
.info-box p:last-child { margin-bottom: 0; }
.info-box strong { color: var(--text); }

/* FOOTER */
.footer { background: var(--text); color: var(--parch); }
.footer-inner { max-width: 1200px; margin: 0 auto; padding: 48px 52px 32px; }
.footer-bottom { display: flex; justify-content: space-between; font-size: 12px; color: rgba(242,232,212,.35); padding-top: 24px; border-top: 1px solid rgba(242,232,212,.1); }
.footer-logo { font-family: var(--display); font-size: 22px; letter-spacing: .1em; margin-bottom: 12px; }
.footer-logo span { color: var(--gold-l); }
.footer-tagline { font-size: 13px; color: rgba(242,232,212,.5); line-height: 1.7; font-weight: 300; margin-bottom: 32px; }

@media (max-width: 760px) {
  .nav { padding: 16px 24px; }
  .nav-links { display: none; }
  .page-header { padding: 48px 24px 36px; }
  .content-wrap { padding: 48px 24px 80px; }
  .footer-inner { padding: 40px 24px 28px; }
}
</style>
</head>
<body>

<nav class="nav">
  <a href="<?php echo esc_url(home_url('/')); ?>" class="logo">NONLA<span>.</span></a>
  <ul class="nav-links">
    <li><a href="<?php echo esc_url(home_url('/#about')); ?>"><span class="t" data-en="About" data-ro="Despre">About</span></a></li>
    <li><a href="<?php echo esc_url(home_url('/projects')); ?>"><span class="t" data-en="Projects" data-ro="Proiecte">Projects</span></a></li>
    <li><a href="<?php echo esc_url(home_url('/#contact')); ?>"><span class="t" data-en="Contact" data-ro="Contact">Contact</span></a></li>
  </ul>
  <div class="nav-right">
    <div class="lang-toggle">
      <button class="lang-btn active" data-lang="en" onclick="applyLang('en')">EN</button>
      <button class="lang-btn" data-lang="ro" onclick="applyLang('ro')">RO</button>
    </div>
  </div>
</nav>

<div class="page-header">
  <div class="page-header-inner">
    <div class="breadcrumb">
      <a href="<?php echo esc_url(home_url('/')); ?>">Nonla</a> / <span class="t" data-en="Legal Notice" data-ro="Mențiuni Legale">Legal Notice</span>
    </div>
    <h1 class="page-title">
      <span class="t" data-en="Legal" data-ro="Mențiuni">Legal</span><br>
      <em><span class="t" data-en="Notice." data-ro="Legale.">Notice.</span></em>
    </h1>
    <p class="page-subtitle t"
       data-en="Legal information, privacy policy, and terms for Asociația Nonla Cultural Escape."
       data-ro="Informații legale, politică de confidențialitate și termeni pentru Asociația Nonla Cultural Escape.">
      Legal information, privacy policy, and terms for Asociația Nonla Cultural Escape.
    </p>
  </div>
</div>

<div class="content-wrap">

  <!-- TOC -->
  <div class="toc">
    <div class="toc-title t" data-en="Contents" data-ro="Cuprins">Contents</div>
    <ul class="toc-list">
      <li><a href="#org"><span class="toc-num">01</span><span class="t" data-en="Organization Details" data-ro="Date Organizație">Organization Details</span></a></li>
      <li><a href="#privacy"><span class="toc-num">02</span><span class="t" data-en="Privacy Policy (GDPR)" data-ro="Politică de Confidențialitate (GDPR)">Privacy Policy (GDPR)</span></a></li>
      <li><a href="#terms"><span class="toc-num">03</span><span class="t" data-en="Participation Terms" data-ro="Termeni de Participare">Participation Terms</span></a></li>
      <li><a href="#cookies"><span class="toc-num">04</span><span class="t" data-en="Cookies" data-ro="Cookie-uri">Cookies</span></a></li>
      <li><a href="#contact-legal"><span class="toc-num">05</span><span class="t" data-en="Contact" data-ro="Contact">Contact</span></a></li>
    </ul>
  </div>

  <!-- 01 ORGANIZATION -->
  <div class="legal-section" id="org">
    <div class="section-num">01</div>
    <h2 class="section-heading t" data-en="Organization Details" data-ro="Date Organizație">Organization Details</h2>
    <div class="info-box">
      <p><strong><span class="t" data-en="Legal name:" data-ro="Denumire legală:">Legal name:</span></strong> Asociația Nonla Cultural Escape</p>
      <p><strong>CIF:</strong> 49845083</p>
      <p><strong><span class="t" data-en="Headquarters:" data-ro="Sediu:">Headquarters:</span></strong> Câmpulung Muscel, Județul Argeș, România</p>
      <p><strong><span class="t" data-en="Type:" data-ro="Tip:">Type:</span></strong> <span class="t" data-en="Non-governmental organization (NGO), non-profit" data-ro="Organizație neguvernamentală (ONG), non-profit">Non-governmental organization (NGO), non-profit</span></p>
      <p><strong><span class="t" data-en="Email:" data-ro="Email:">Email:</span></strong> <a href="mailto:contact@nonlaescape.com">contact@nonlaescape.com</a></p>
      <p><strong><span class="t" data-en="Website:" data-ro="Website:">Website:</span></strong> <a href="https://nonlaescape.com">nonlaescape.com</a></p>
    </div>
    <p class="t"
       data-en="Asociația Nonla Cultural Escape is a Romanian non-profit association registered and operating under Romanian law. The organization's purpose is to promote cultural exchange, sustainable tourism, and international understanding through immersive travel experiences."
       data-ro="Asociația Nonla Cultural Escape este o asociație non-profit română, înregistrată și funcționând conform legislației române. Scopul organizației este promovarea schimbului cultural, a turismului sustenabil și a înțelegerii internaționale prin experiențe de călătorie imersive.">
      Asociația Nonla Cultural Escape is a Romanian non-profit association registered and operating under Romanian law. The organization's purpose is to promote cultural exchange, sustainable tourism, and international understanding through immersive travel experiences.
    </p>
  </div>

  <!-- 02 PRIVACY -->
  <div class="legal-section" id="privacy">
    <div class="section-num">02</div>
    <h2 class="section-heading t" data-en="Privacy Policy" data-ro="Politică de Confidențialitate">Privacy Policy</h2>
    <p class="t"
       data-en="We are committed to protecting your personal data in accordance with Regulation (EU) 2016/679 (GDPR). This policy explains how we collect, use, and protect your information."
       data-ro="Ne angajăm să vă protejăm datele personale în conformitate cu Regulamentul (UE) 2016/679 (GDPR). Această politică explică modul în care colectăm, utilizăm și protejăm informațiile dvs.">
      We are committed to protecting your personal data in accordance with Regulation (EU) 2016/679 (GDPR). This policy explains how we collect, use, and protect your information.
    </p>
    <p><strong><span class="t" data-en="What data we collect:" data-ro="Ce date colectăm:">What data we collect:</span></strong></p>
    <ul>
      <li class="t" data-en="Name and contact details (email, phone) when you register interest or contact us" data-ro="Nume și date de contact (email, telefon) când vă înregistrați interesul sau ne contactați">Name and contact details (email, phone) when you register interest or contact us</li>
      <li class="t" data-en="Travel document information necessary for booking flights and accommodation" data-ro="Informații din documentele de călătorie necesare pentru rezervarea zborurilor și cazării">Travel document information necessary for booking flights and accommodation</li>
      <li class="t" data-en="Communication history (emails, messages) related to trip organization" data-ro="Istoricul comunicărilor (emailuri, mesaje) legate de organizarea excursiei">Communication history (emails, messages) related to trip organization</li>
      <li class="t" data-en="Anonymous usage data from this website (no personal cookies without consent)" data-ro="Date anonime de utilizare a acestui site (fără cookie-uri personale fără consimțământ)">Anonymous usage data from this website (no personal cookies without consent)</li>
    </ul>
    <p><strong><span class="t" data-en="How we use it:" data-ro="Cum o utilizăm:">How we use it:</span></strong></p>
    <ul>
      <li class="t" data-en="To organize and manage your participation in Nonla escapes" data-ro="Pentru a organiza și gestiona participarea dvs. la escapadele Nonla">To organize and manage your participation in Nonla escapes</li>
      <li class="t" data-en="To communicate trip details, updates, and important logistics" data-ro="Pentru a comunica detalii despre excursie, actualizări și logistică importantă">To communicate trip details, updates, and important logistics</li>
      <li class="t" data-en="To send occasional updates about new escapes (you can unsubscribe at any time)" data-ro="Pentru a trimite actualizări ocazionale despre escapade noi (vă puteți dezabona oricând)">To send occasional updates about new escapes (you can unsubscribe at any time)</li>
    </ul>
    <p><strong><span class="t" data-en="Your rights:" data-ro="Drepturile dvs.:">Your rights:</span></strong></p>
    <ul>
      <li class="t" data-en="Right to access your data — request a copy at any time" data-ro="Dreptul de acces la datele dvs. — solicitați o copie oricând">Right to access your data — request a copy at any time</li>
      <li class="t" data-en="Right to rectification — update incorrect information" data-ro="Dreptul la rectificare — actualizați informațiile incorecte">Right to rectification — update incorrect information</li>
      <li class="t" data-en="Right to erasure — request deletion of your data" data-ro="Dreptul la ștergere — solicitați ștergerea datelor dvs.">Right to erasure — request deletion of your data</li>
      <li class="t" data-en="Right to object — opt out of any processing at any time" data-ro="Dreptul de opoziție — renunțați la orice prelucrare oricând">Right to object — opt out of any processing at any time</li>
    </ul>
    <p class="t"
       data-en="To exercise any of these rights, contact us at contact@nonlaescape.com. We respond within 30 days."
       data-ro="Pentru a vă exercita oricare dintre aceste drepturi, contactați-ne la contact@nonlaescape.com. Răspundem în termen de 30 de zile.">
      To exercise any of these rights, contact us at <a href="mailto:contact@nonlaescape.com">contact@nonlaescape.com</a>. We respond within 30 days.
    </p>
  </div>

  <!-- 03 TERMS -->
  <div class="legal-section" id="terms">
    <div class="section-num">03</div>
    <h2 class="section-heading t" data-en="Participation Terms" data-ro="Termeni de Participare">Participation Terms</h2>
    <p class="t"
       data-en="By registering for a Nonla Cultural Escape, you agree to the following terms."
       data-ro="Prin înregistrarea pentru o Nonla Cultural Escape, acceptați următorii termeni.">
      By registering for a Nonla Cultural Escape, you agree to the following terms.
    </p>
    <p><strong><span class="t" data-en="Booking and payment:" data-ro="Rezervare și plată:">Booking and payment:</span></strong></p>
    <ul>
      <li class="t" data-en="A reservation deposit is required to confirm your place. Specific amounts are communicated at registration." data-ro="Un depozit de rezervare este necesar pentru a confirma locul dvs. Sumele specifice sunt comunicate la înregistrare.">A reservation deposit is required to confirm your place. Specific amounts are communicated at registration.</li>
      <li class="t" data-en="Full payment is due by the date specified in your registration confirmation." data-ro="Plata integrală este datorată până la data specificată în confirmarea înregistrării dvs.">Full payment is due by the date specified in your registration confirmation.</li>
      <li class="t" data-en="Prices include all items listed in the package description. Personal expenses, optional activities, and travel insurance are not included unless stated." data-ro="Prețurile includ toate elementele enumerate în descrierea pachetului. Cheltuielile personale, activitățile opționale și asigurarea de călătorie nu sunt incluse dacă nu se specifică altfel.">Prices include all items listed in the package description. Personal expenses, optional activities, and travel insurance are not included unless stated.</li>
    </ul>
    <p><strong><span class="t" data-en="Cancellation policy:" data-ro="Politică de anulare:">Cancellation policy:</span></strong></p>
    <ul>
      <li class="t" data-en="More than 60 days before departure: full refund minus administrative fees." data-ro="Cu mai mult de 60 de zile înainte de plecare: rambursare integrală minus taxe administrative.">More than 60 days before departure: full refund minus administrative fees.</li>
      <li class="t" data-en="30–60 days before departure: 50% refund." data-ro="30–60 de zile înainte de plecare: 50% rambursare.">30–60 days before departure: 50% refund.</li>
      <li class="t" data-en="Less than 30 days before departure: no refund. We strongly recommend travel insurance." data-ro="Cu mai puțin de 30 de zile înainte de plecare: fără rambursare. Recomandăm cu tărie asigurarea de călătorie.">Less than 30 days before departure: no refund. We strongly recommend travel insurance.</li>
    </ul>
    <p><strong><span class="t" data-en="Participation:" data-ro="Participare:">Participation:</span></strong></p>
    <ul>
      <li class="t" data-en="Participants must hold a valid passport for the duration of travel." data-ro="Participanții trebuie să dețină un pașaport valabil pe durata călătoriei.">Participants must hold a valid passport for the duration of travel.</li>
      <li class="t" data-en="Nonla reserves the right to modify itineraries for safety, weather, or logistical reasons." data-ro="Nonla își rezervă dreptul de a modifica itinerariile din motive de siguranță, vreme sau logistică.">Nonla reserves the right to modify itineraries for safety, weather, or logistical reasons.</li>
      <li class="t" data-en="Participants are responsible for ensuring they meet all visa and entry requirements for Vietnam." data-ro="Participanții sunt responsabili să se asigure că îndeplinesc toate cerințele de viză și intrare pentru Vietnam.">Participants are responsible for ensuring they meet all visa and entry requirements for Vietnam.</li>
    </ul>
  </div>

  <!-- 04 COOKIES -->
  <div class="legal-section" id="cookies">
    <div class="section-num">04</div>
    <h2 class="section-heading">Cookies</h2>
    <p class="t"
       data-en="This website uses minimal cookies. The only cookie stored is your language preference (EN/RO). No tracking cookies, advertising cookies, or third-party analytics are used."
       data-ro="Acest site folosește cookie-uri minime. Singurul cookie stocat este preferința dvs. de limbă (EN/RO). Nu se folosesc cookie-uri de urmărire, publicitate sau analiză de la terți.">
      This website uses minimal cookies. The only cookie stored is your language preference (EN/RO). No tracking cookies, advertising cookies, or third-party analytics are used.
    </p>
    <p class="t"
       data-en="WordPress may set technical cookies required for core functionality (session management). These do not collect personal data."
       data-ro="WordPress poate seta cookie-uri tehnice necesare funcționalității de bază (gestionarea sesiunii). Acestea nu colectează date personale.">
      WordPress may set technical cookies required for core functionality (session management). These do not collect personal data.
    </p>
  </div>

  <!-- 05 CONTACT -->
  <div class="legal-section" id="contact-legal">
    <div class="section-num">05</div>
    <h2 class="section-heading">Contact</h2>
    <p class="t"
       data-en="For any legal questions, data requests, or concerns, contact us directly:"
       data-ro="Pentru orice întrebări legale, solicitări de date sau nelămuriri, contactați-ne direct:">
      For any legal questions, data requests, or concerns, contact us directly:
    </p>
    <div class="info-box">
      <p><strong>Asociația Nonla Cultural Escape</strong></p>
      <p>Câmpulung Muscel, Județul Argeș, România</p>
      <p><a href="mailto:contact@nonlaescape.com">contact@nonlaescape.com</a></p>
    </div>
    <p style="font-size:13px;color:var(--muted);line-height:1.7;" class="t"
       data-en="This legal notice was last updated in May 2026."
       data-ro="Această mențiune legală a fost actualizată ultima dată în mai 2026.">
      This legal notice was last updated in May 2026.
    </p>
  </div>

</div>

<footer class="footer">
  <div class="footer-inner">
    <div class="footer-logo">NONLA<span>.</span></div>
    <p class="footer-tagline">Asociația Nonla Cultural Escape · CIF: 49845083 · Câmpulung Muscel, Argeș</p>
    <div class="footer-bottom">
      <span>© <?php echo date('Y'); ?> Asociația Nonla Cultural Escape</span>
      <a href="<?php echo esc_url(home_url('/')); ?>" style="color:rgba(242,232,212,.35);text-decoration:none;">← <span class="t" data-en="Back to homepage" data-ro="Înapoi la pagina principală">Back to homepage</span></a>
    </div>
  </div>
</footer>

<script>
const LANG_KEY = 'nl_lang';
let currentLang = localStorage.getItem(LANG_KEY) || 'en';
function applyLang(l) {
  currentLang = l;
  localStorage.setItem(LANG_KEY, l);
  document.querySelectorAll('.t[data-en]').forEach(el => { el.innerHTML = el.dataset[l] || el.dataset.en; });
  document.querySelectorAll('.lang-btn').forEach(b => b.classList.toggle('active', b.dataset.lang === l));
  document.documentElement.lang = l === 'ro' ? 'ro' : 'en';
}
applyLang(currentLang);
</script>

<?php wp_footer(); ?>
</body>
</html>
