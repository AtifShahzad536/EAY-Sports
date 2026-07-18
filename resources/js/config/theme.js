/**
 * ─────────────────────────────────────────────────────────
 *  EAY SPORTS — Global Theme Config
 *  Change anything here and it reflects across the ENTIRE app
 * ─────────────────────────────────────────────────────────
 */

/* ── BRAND COLORS (Hex Values) ────────────────────────── */
export const COLORS = {
  primary:       'var(--color-primary)',   // Main zinc-800 — used in header, buttons, accents
  primaryDark:   'var(--color-primary-dark)',   // Hover state of primary (zinc-900)
  primaryLight:  'var(--color-primary-light)',   // Light bg tint (zinc-100)

  secondary:     'var(--color-secondary)',   // zinc-600 — used in gradients
  accent:        'var(--color-accent)',   // zinc-400 — used in gradients
}

/* ── TEXT COLORS (Tailwind Classes) ───────────────────── */
export const TEXT = {
  dark:      'text-slate-700',   // Main headings
  mid:       'text-slate-500',   // Sub-headings, descriptions
  light:     'text-slate-400',   // Labels, captions, muted
}

/* ── BACKGROUNDS (Tailwind Classes) ───────────────────── */
export const BG = {
  page:      'bg-white',
  section:   'bg-slate-50',
  dark:      'bg-slate-950',
  primary:   'bg-indigo-600',
}

/* ── BORDERS (Tailwind Classes) ───────────────────────── */
export const BORDER = {
  light:     'border-slate-100',
  focus:     'border-indigo-600',
}

/* ── GRADIENTS ────────────────────────────────────────── */
export const GRADIENTS = {
  brand:     'bg-gradient-to-r from-indigo-600 via-indigo-500 to-indigo-300',
  brandText: 'bg-gradient-to-r from-indigo-600 via-indigo-500 to-indigo-300 bg-clip-text text-transparent',
  heroText:  'bg-gradient-to-r from-zinc-200 via-zinc-400 to-zinc-600 bg-clip-text text-transparent',
  button:    'bg-gradient-to-br from-indigo-600 to-indigo-500',
}

/* ── TYPOGRAPHY ───────────────────────────────────────── */
export const FONT = {
  // Font weight — change here to apply everywhere
  heading:   '',              // '' = browser default (normal), or use 'font-semibold' etc.
  subheading:'',
  body:      '',
  label:     '',
  button:    '',              // '' = normal weight on buttons

  // Font sizes
  h1:        'text-[40px] md:text-[60px]',
  h2:        'text-[32px] md:text-[42px]',
  h3:        'text-2xl',
  body:      'text-[15px]',
  small:     'text-sm',
  xs:        'text-xs',
}

/* ── BORDER RADIUS ────────────────────────────────────── */
export const RADIUS = {
  card:    'rounded-2xl',
  button:  'rounded-full',
  input:   'rounded-xl',
  badge:   'rounded-full',
  icon:    'rounded-xl',
}

/* ── SHADOWS ──────────────────────────────────────────── */
export const SHADOW = {
  card:    'shadow-xl shadow-slate-200/20',
  button:  'shadow-lg shadow-zinc-200',
  header:  'shadow-lg shadow-zinc-900/30',
}

/* ── HEADER ───────────────────────────────────────────── */
export const HEADER = {
  bg:          'bg-indigo-700',
  border:      'border-indigo-600',
  text:        'text-white',
  navBg:       'bg-white/10',
  activeNavBg: 'bg-white',
  activeNavText:'text-indigo-700',
  inactiveNavText: 'text-white/80',
  dealerBtn:   'bg-white text-indigo-700',
}

/* ── BUTTONS ──────────────────────────────────────────── */
export const BTN = {
  primary:   'bg-indigo-600 hover:bg-indigo-700 text-white px-8 py-4 rounded-full transition-all hover:scale-105',
  secondary: `bg-white border border-slate-200 text-slate-700 px-8 py-4 rounded-full hover:bg-slate-50 transition-all`,
  ghost:     `bg-white/10 border border-white/20 text-white px-8 py-4 rounded-full hover:bg-white/20 backdrop-blur-sm transition-all`,
  outline:   `bg-white border border-slate-200 text-slate-700 px-8 py-4 rounded-full hover:bg-slate-50 hover:border-slate-300 transition-all shadow-sm`,
}

/* ── SECTION SPACING ──────────────────────────────────── */
export const SPACING = {
  section: 'py-20',
  sectionLg: 'py-24',
  container: 'max-w-[1600px] mx-auto px-4 sm:px-12 lg:px-16 xl:px-24',
}
