# Home Care UNAI V2 design

## Premium visual direction

The interface uses a cranberry-red editorial healthcare system, the official UNAI seal, Geist for functional UI, and Newsreader for high-impact editorial headings. Licensed healthcare photography replaces decorative CSS portraits across the hero, service cards, caregiver cards, login, process, and closing call-to-action. Photography is stored locally as optimized WebP and credited in `docs/IMAGE_CREDITS.md`.

Motion is progressive enhancement: the hero enters on load, cards reveal through `IntersectionObserver`, and image crops use restrained hover movement. `prefers-reduced-motion` disables the effects without hiding content.

## Intent

Turn the supplied Laravel demo into a polished, credible healthcare-service demonstration that lets a reviewer complete the customer, administrator, and caregiver journeys without mistaking fictional data for real clinical operations.

## Goals

- Preserve Laravel as the authoritative backend and keep the existing pricing rules intact.
- Make the public preview useful on Vercel while labelling it as a standalone demo.
- Add a distinct caregiver workspace and a true five-step booking flow.
- Strengthen accessibility, responsive behavior, receipts, and realistic operational states.
- Keep every visible control functional, identify fictional profiles as sample data, and withhold fictional reviews entirely.

## Non-goals

- Real medical advice, emergency dispatch, payments, GPS, WhatsApp, or credential verification.
- Claiming the Vercel static preview is a hosted Laravel backend.
- Replacing the existing architecture with an unrelated framework rewrite.

## Constraints

- PHP and Composer are unavailable in the current workstation, so Laravel execution must be reported as unverified locally.
- The Vercel target can host the standalone preview; persistent Laravel hosting remains separate.
- Existing source assets and Home Care UNAI identity are retained.

## Approaches considered

1. Rewrite as Vue/Inertia immediately. Rejected because it risks breaking the already functional booking, CRUD, PDF, and offline preview in one large migration.
2. Incremental modernization of the current Laravel plus modular JavaScript application. Selected because it preserves verified behavior and allows high-value vertical slices.
3. Static-only redesign. Rejected because it would discard the Laravel backend and violate the requirement that server pricing remain authoritative.

## Design direction

Reading this as a healthcare operations platform for Indonesian families and demo operators, in a warm-clinical editorial language, dial ENERGY 2 / RHYTHM 2 / MOTION 2.

- Color: the latest owner direction replaces the earlier green/teal requirement with cranberry red, deep burgundy, pale rose surfaces, and restrained amber warnings. The red is intentionally dark and warm so the product reads as calm healthcare rather than an emergency alert.
- Typography: Manrope provides approachable but structured headings; DM Sans keeps dense operational text readable.
- Layout: public pages use varied editorial sections; dashboards use denser task-oriented panels.
- Spacing: generous on public content, compact in tables and operational controls.
- Cards: reserved for bounded services, appointments, and dashboard metrics where grouping improves scanning.
- Icons: simple line icons clarify actions and statuses; decorative sparkle symbols are removed.
- Motion: short opacity and transform transitions explain state changes, with reduced-motion support.

## Architecture

- Laravel API remains authoritative for quotations, booking persistence, assignments, and CRUD.
- The browser preview mirrors the same UI and business rules with local demo persistence.
- Pricing snapshots remain attached to bookings so historical receipts do not change.
- Vercel serves the preview directory only and is documented as an interim frontend demonstration.

## Acceptance

- Customer, admin, and caregiver demo role switching works.
- Booking shows five explicit steps and a live price summary.
- Existing receipt PDF and print features remain functional.
- JavaScript tests, production build, browser click-through, mobile overflow checks, and console review pass before delivery.
