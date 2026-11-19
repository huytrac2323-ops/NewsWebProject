# DESIGN_SYSTEM.md — V-NEWS CMS Admin Dashboard
> Established under the **AntiSlopUI Contract** (`.agents/skills/antislop-design/SKILL.md`)  
> Product Domain: **Publishing, Enterprise Content Management, Editorial Newsroom**  
> Target Surface: **Admin Dashboard Shell & Metrics Center**

---

## 1. Executive Summary & Hard Gates Compliance

This design system establishes strict, non-negotiable visual and ergonomic tokens for the V-NEWS CMS Administration Dashboard, systematically eliminating all AI-generated clichés (purple/indigo neon gradients, uniform `rounded-3xl` bubble cards, shadow spam, low-contrast text, scaling hover transforms).

### Gate Audit Pre-Commit Check
| Gate ID | Banned AI Pattern | Compliant Token / Rule | Status |
|---|---|---|---|
| **C1 / C2** | Purple/indigo primary accent (`#6366f1`, `#4f46e5`, gradients) | Domain Hue: Slate (`#334155` / `#0F172A`) & Editorial Crimson (`#B91C1C`) semantic | **PASS** |
| **C7** | Low-contrast text / gray-on-gray | WCAG AA 4.5:1 body (`#0F172A` on `#F8FAFC` = 17.1:1, `#475569` = 7.2:1) | **PASS** |
| **T1 / T2** | Inter-only everywhere or gradient headlines | Display: `Plus Jakarta Sans` 600 + Body: `Plus Jakarta Sans` 400/500 | **PASS** |
| **K1** | Uniform 3-card or 4-card feature cards | Inverted pyramid KPIs (value > delta) + Bento-grid split (8-col / 4-col) | **PASS** |
| **K3** | Scaling on hover (`hover:scale-*`) | Strict `transition-colors duration-150` or subtle border feedback | **PASS** |
| **B3** | Pill buttons everywhere (`rounded-full`) | Strict Radius Scale: inputs/buttons `rounded-md`, cards `rounded-lg` | **PASS** |
| **N1** | Floating glassmorphic navbar with backdrop-blur | Fixed solid 56px (`h-14`) header with single `border-b border-slate-200` | **PASS** |
| **MO10** | Missing `prefers-reduced-motion` | All animations gated with `motion-reduce:transition-none` | **PASS** |

---

## 2. Color Palette & 3-Tier Elevation Model

### Tier 1: Primitive Values
- **Slate Core:**
  - `slate-50`: `#F8FAFC` (Sidebar background, muted container)
  - `slate-100`: `#F1F5F9` (Page canvas background, table headers)
  - `slate-200`: `#E2E8F0` (Hairline component borders)
  - `slate-300`: `#CBD5E1` (Dividers, subtle lines)
  - `slate-400`: `#94A3B8` (Inactive icons, metadata labels)
  - `slate-500`: `#64748B` (Secondary text, subtitles)
  - `slate-600`: `#475569` (Medium-contrast body labels)
  - `slate-700`: `#334155` (High-contrast neutral)
  - `slate-900`: `#0F172A` (Headlines, primary ink)
- **Semantic Accents (State-Only, Never Decorative):**
  - **Success / Positive:** Emerald `#047857` (bg: `#ECFDF5`, ring: `#059669/20`)
  - **Warning / Pending:** Amber `#B45309` (bg: `#FFFBEB`, ring: `#D97706/20`)
  - **Danger / Spam / Delete:** Crimson `#B91C1C` (bg: `#FEF2F2`, ring: `#DC2626/20`)
  - **Active / Primary Action:** Deep Slate `#0F172A` (with white text) or Deep Teal `#0F766E`

### Tier 2: Semantic Tokens
```css
:root {
  /* Surfaces */
  --surface-page: #F8FAFC;
  --surface-sidebar: #0F172A; /* Editorial dark sidebar: sidebar dims, content shines */
  --surface-card: #FFFFFF;
  --surface-inset: #F1F5F9;
  
  /* Borders */
  --border-subtle: #E2E8F0;
  --border-strong: #CBD5E1;
  
  /* Text */
  --text-primary: #0F172A;
  --text-secondary: #475569;
  --text-muted: #64748B;
  
  /* Interactive */
  --interactive-hover: #F1F5F9;
  --focus-ring: #0F766E;
}
```

### Tier 3: Component Surface Hierarchy
1. **Canvas (`bg-slate-100 dark:bg-slate-950`):** Base layer supporting the dashboard.
2. **Sidebar Navigation (`bg-slate-900`):** Authoritative dark rail providing visual anchor.
3. **Card Layer (`bg-white dark:bg-slate-900`):** Primary content containers, strictly bordered with `border border-slate-200 dark:border-slate-800` (NO shadow spam).
4. **Floating Overlays (Modals / Tooltips):** The ONLY elements permitted to utilize elevation shadows (`shadow-lg`).

---

## 3. Typography & Data Density

### Typography Hierarchy (Max 2 Fonts, Max 3 Weights: 400, 500, 600)
- **Primary Typeface:** `Plus Jakarta Sans`, -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif.
- **Weights:**
  - `400 (Regular)`: Body prose, descriptions, secondary table columns.
  - `500 (Medium)`: Navigation links, table headers, form labels, metadata.
  - `600 (SemiBold)`: Card titles, primary values, action buttons.

### Scale & Data Rules
- **Dashboard Page Title:** `text-lg font-semibold tracking-tight` (NO 3xl hype titles; data space is precious).
- **Section Headers:** `text-xs font-semibold uppercase tracking-wider text-slate-500`.
- **Card Titles:** `text-sm font-semibold text-slate-900 dark:text-white`.
- **Body & Data Cells:** `text-[13px] text-slate-600 dark:text-slate-300` (comfortable 44–48px row density).
- **Tabular Numerics:** Strict **`tabular-nums`** applied to **ALL** numeric values, KPI counters, percentages, and timestamps to eliminate layout shifts.

---

## 4. Radius Scale System

Child radius must always be smaller than or equal to parent container:
- `rounded-md` (6px): Form inputs, standard buttons, status badges, search bars.
- `rounded-lg` (8px): KPI cards, widget panels, data tables.
- `rounded-xl` (12px): Modals, dialog boxes, command palette.
- `rounded-full`: User avatars, active state indicator dots.
- **Explicitly Banned:** `rounded-3xl` and `rounded-2xl` on cards or tables.

---

## 5. Dashboard Grid Composition & Anatomy

### 1. App Shell (CSS Grid 2D Structure)
```
+------------------+-------------------------------------------------------+
|  SIDEBAR         |  TOPBAR (h-14, sticky, 1px border-b, no shadow)      |
|  (w-64, fixed)   +-------------------------------------------------------+
|                  |  MAIN CONTENT (overflow-y-auto, p-6 space-y-6)       |
|  - Brand Header  |                                                       |
|  - Section Groups|  [Row 1: Breadcrumb + Header Title + Primary Action]  |
|  - Nav Items     |  [Row 2: 4-Col KPI Inverted Pyramid with Delta Badge]  |
|  - User Switcher |  [Row 3: 8-Col Data Table + 4-Col Quick Actions Bento]|
|  - Status Footer |  [Row 4: System & DNS Diagnostics Status Bar]         |
+------------------+-------------------------------------------------------+
```

### 2. KPI Stat Card Anatomy (4-Layer Inverted Pyramid)
1. **Layer 1 (Label):** `text-xs font-medium text-slate-500`
2. **Layer 2 (Value):** `text-2xl font-semibold tabular-nums text-slate-900`
3. **Layer 3 (Delta / Context):** Direction icon + percentage + comparative interval (`text-xs tabular-nums`)
4. **Layer 4 (Accent Indicator):** Clean, non-intrusive semantic border or status dot

### 3. Data Table Anatomy
- Header: Sticky `bg-slate-50 dark:bg-slate-800 text-xs font-medium uppercase tracking-wider text-slate-500`.
- Body: `divide-y divide-slate-100 dark:divide-slate-800`, subtle `hover:bg-slate-50/60 transition-colors duration-150` (NO zebra stripes).
- Badges: `inline-flex items-center rounded-md px-2 py-0.5 text-xs font-medium ring-1 ring-inset`.

---

## 6. Interaction & Motion Rules
- **Interactive Feedback:** Exclusively color shifts (`hover:bg-slate-100`) or ring focus (`focus-visible:ring-2 focus-visible:ring-slate-900`).
- **Duration:** 150ms for hovers, 200ms for drawers/modals.
- **Accessibility:** `cursor-pointer` on all clickables, `aria-label` on iconography, complete keyboard operability.

---

## 7. Advanced Newsroom Telemetry & Command Palette
- **Linear-Style Command Palette (`Ctrl + K` / `Cmd + K`):** Instant keyboard accessibility for newsroom tasks (new article, review queue, categories, domains, system settings).
- **Zero-Dependency SVG Velocity Chart:** Native SVG area curve tracking 7-day publication cadence, fully cross-database compatible (SQLite & MySQL) without heavy external JavaScript libraries.
- **Micro Sparklines:** Embedded 64x24 vector sparklines in all 4 KPI cards for instant visual trajectory without clutter.
- **Real-Time Client-Side Table Filter:** Fast keystroke-driven article title filtering on the dashboard without full page reloads.
