---
title: "Biz Buradayiz"
subtitle: "Administrator Operations Manual"
author: "Biz Buradayiz"
date: "April 2026"
lang: en-US
---

# Administrator Operations Manual

**Biz Buradayiz** — English · April 2026 · For administrative and operational staff (not a programming guide).

This manual explains what the system does, how the public search experience works, and how each major area of the admin panel fits together.

---

## 1. What this system is

Biz Buradayiz connects **visitors** looking for roadside and towing-style services with **service providers**. The product has three main surfaces:

- **Public website** — Home, provider search, provider profiles, blog, static pages, contact.
- **Provider portal** — Service providers sign in to manage their account, see subscriptions and call activity, and request paid packages.
- **Admin panel** — Your team configures the catalog, approves users, moderates reviews, adjusts site settings, and monitors activity.

Behind the scenes the application is organized into modules (users, platform, geography, content, telephony, configuration, and so on). You do not need to know module names to use the panel; they are listed only to show how responsibilities are grouped.

---

## 2. Signing in, language, and permissions

- Admins sign in through the **admin login** URL (under your localized path, for example `/en/admin/...`).
- The interface can run in **multiple languages**; language switches affect labels and, where configured, content.
- **Roles and permissions** control which menu items and actions each admin can use. If you cannot see a section, your role may not include that permission. Contact a super-admin to adjust access.

---

## 3. Dashboard

The **Dashboard** summarizes operational indicators. Typical tiles include:

- **Active package subscriptions** — Subscriptions whose status is **Active** (not all historical subscriptions).
- **Paid subscription revenue** — Revenue tied to paid subscription flows (exact rules follow your finance process and how payments are recorded).
- **Incoming calls to service providers** — Inbound call events recorded for providers (see **Verimor call events**).
- **Reviews** — Total reviews submitted, **average rating** (approved reviews only, scale 1–5), and count **awaiting moderation**.

You can often filter metrics by **date range** where the screen offers that control.

---

## 4. User management

### 4.1 Admins

**Admins** are staff accounts for the admin panel. You can create, edit, disable, or restore them according to your permissions. Admins are separate from end customers and service providers.

### 4.2 Customers and service providers

**Customers** and **service providers** are both stored as **users**, with different **types**.

- **Service providers** have extra data: the **service** they offer, **city** (and thus region), optional **profile image**, **public profile slug**, and links to **package subscriptions** and **call history**.
- **Activating a service provider** is critical: until their **status** is **Active**, they normally **cannot sign in** to the provider portal.

When a service provider becomes **Active** for the first time, the system typically:

1. Records **`approved_at`** (approval timestamp) if it was not set yet.
2. May grant a **welcome free-tier package** automatically **once per provider**, if a **free-tier package** exists that is linked to their **service** type.
3. Triggers a **background recalculation** of **search ranking scores** (see Section 10).

### 4.3 Pending registrations

Providers who **self-register** on the public site are usually created in a **pending** state. Your team must **review and activate** them when appropriate.

---

## 5. Zone management (geography)

**Countries**, **states** (regions/provinces), and **cities** (districts) power:

- Provider **location** on their profile.
- **Search filters** on the public provider search page (service + state and/or city).

Keep this data accurate and translated as needed so visitors and providers see consistent place names.

---

## 6. Platform management

### 6.1 Services

**Services** are the types of work providers perform (for example categories of roadside assistance). Fields include whether a service appears in **public search filters**. Services can be linked to **packages**.

### 6.2 Packages

**Packages** define what a provider buys: pricing, billing period, whether the package is a **free tier**, **sort order**, popularity flags, and which **services** can use that package.

### 6.3 Package subscriptions

A **package subscription** connects a **provider** to a **package snapshot** and tracks:

- **Status** (for example active vs pending payment).
- **Payment status** and **payment method** (for example bank transfer awaiting verification).
- **Start / end dates** (some packages have no fixed end).
- **Remaining connections** — a quota that must be **greater than zero** for the subscription to count as **active** in public search.

Your team may create subscriptions, confirm bank payments, adjust status, and add **admin notes** according to your internal process.

### 6.4 Verimor call events

**Verimor call events** are records of **telephony activity** (for example inbound calls) associated with a provider. They are used for:

- **Operational visibility** in admin and on the provider dashboard.
- **Activity** in the **ranking score** formula (see Section 10).
- **Review verification** — a customer can only submit a **review** if their phone number matches an **answered inbound call** to that provider that has not already been used for a review (see Section 9).

---

## 7. Review management

**Reviews** can be **pending** or **approved** (and may support other states depending on configuration).

- Only **approved** reviews should affect the **public average rating** shown on profiles and in ranking inputs.
- When reviews are created, updated, or removed, the system **updates rating aggregates** for the provider and **queues a ranking recalculation**.

Train moderators on your **content policy** (what is allowed in text, how to handle disputes, and when to reject).

---

## 8. SEO management

**SEO entries** let you manage **meta titles and descriptions** (and related fields) for important public URLs so search engines display sensible snippets. Keep wording aligned with marketing and legal guidelines.

---

## 9. How customer reviews are tied to real calls

To reduce fake reviews, **public review submission** requires a **verified call**:

- The customer enters a **phone number**.
- The system looks for an **inbound**, **answered** call to that **provider** where the **caller number** matches (normalized), and that call is **not already linked** to another review.
- If no matching call exists, the review is rejected with a validation message.

The review is stored as **pending** until staff **approve** it. Explain this policy to support staff so they can answer provider questions fairly.

---

## 10. Public provider search — visibility, featured area, and ranking

This section describes **exactly what visitors experience** and **what you control** in settings.

### 10.1 Who appears in search

The public search results list only providers who **all** of the following apply:

- Account type is **service provider**.
- Account **status** is **Active**.
- They have at least one **active package subscription**, meaning: **status Active**, **payment status Paid**, **remaining connections > 0**, and the subscription is **not expired** (if it has an end date, the end date must still be in the future; some packages have open-ended validity).

If a provider loses any of these, they **disappear from public search** until the situation is fixed.

### 10.2 Filters visitors use

Visitors narrow results using:

- **Service** (optional).
- **State** and/or **city** (optional).

If **city** is chosen, results are filtered by that city. If only **state** is chosen, results include providers in any city belonging to that state. There is **no free-text keyword search** on this page; narrowing is by these structured filters.

### 10.3 Pagination

Results are **paginated** (a fixed number of providers per page). Typical layout: **12** providers per page after the featured area.

### 10.4 Featured providers (“Öne çıkan”) — first page only

On **page 1 only**, a **featured** strip may appear above the main list.

- **How many slots?** Controlled by the setting **featured providers count** (admin default in software is often **3**).
- **Who is selected?**
  1. If **new provider hours** is greater than zero: the system first tries to fill all slots with providers whose **`approved_at`** falls within the **last N hours**, ordered by **newest approval first**. If there are enough such providers to fill **all** slots, **only** those newcomers appear in featured — nobody else is mixed in for that strip.
  2. If there are **not enough** newcomers, or the new-provider window is disabled: the system fills (remaining) slots from the **same filtered search pool** using the highest **`ranking_score`**.
- **No duplicates:** Providers shown as **featured** on page 1 are **removed** from the regular list on that page so they are not shown twice.

Featured does **not** repeat on page 2, 3, and so on.

### 10.5 Main list order (below featured)

After featured handling, the ordinary list is sorted by:

1. **`ranking_score`** — **highest first**.
2. **First name**, then **last name** — alphabetical tie-breakers.

### 10.6 How `ranking_score` is computed

The system maintains a numeric **ranking score** per **active service provider**. It is **recalculated in the background** (not always instant) when, for example:

- Reviews change (create, edit, delete, restore).
- A new **Verimor call event** is recorded for a provider.
- A provider becomes **active** for the first time.
- You save **ranking-related settings** (weights, featured count, new-provider window).

The formula is **relative**: each ingredient is **min–max normalized across all active providers** at calculation time, then combined with weights.

**Ingredients:**

1. **Ratings** — Based on the provider’s stored **average review rating** (which reflects **approved** reviews after internal aggregation).
2. **Activity** — Based on the **count of Verimor call events** linked to that provider.
3. **Experience / time on platform** — Based on **days since `approved_at`** (when the provider was first activated).

**Weights** (must not sum to more than 100% in admin validation):

- **Ranking weight — rating** (default often 50%).
- **Ranking weight — activity** (default often 30%).
- **Ranking weight — experience** (default often 20%).

If all three weights were saved as zero, the software falls back to sensible defaults (50 / 30 / 20) so the job still runs.

**Important:** Because scores are **relative**, a provider’s rank can change **even if their own numbers stay the same** — for example when another provider receives more calls or better reviews.

---

## 11. Settings

Settings are grouped for clarity. Groups include:

- **General** — Site-wide basics.
- **Social media** — Links and handles surfaced on the public site where configured.
- **Contact info** — Phone, email, address-style fields as used by the theme.
- **Platform** — Operational text such as **bank transfer instructions** shown to providers, and other platform-facing content.
- **Media** — Asset-related configuration.
- **Mobile** — Mobile app or push-related keys and toggles if used.
- **Developers** — Technical integrations (treat as sensitive).
- **Provider ranking** — **Featured providers count**, **new provider window (hours)**, and the three **ranking weights** described in Section 10.

Changes to ranking-related keys trigger **ranking recalculation** so new weights take effect after the job runs.

---

## 12. Content management (CMS)

### 12.1 Contents

**Contents** are individual editorial items with types (blog article, static page, and so on — as configured). Editors manage titles, bodies, publishing state, SEO-friendly slugs, and relationships to categories and tags where applicable.

---

## 13. CRM

### 13.1 Contact requests

**Contact us requests** store messages sent through the public **contact** form. Assign staff to respond according to your SLA.

---

## 14. Operational notes and limitations

- **Ranking updates are asynchronous.** After large data changes, scores may take a short time to refresh.
- **Featured newcomers** depend on accurate **`approved_at`** and the **new provider hours** window.
- **Packages and “connections”** are contractual/business concepts in addition to technical rules; align admin procedures with your commercial terms.
- **Telephony** must be correctly integrated so **call events** and **review verification** remain trustworthy.

---

## Document control

| Item        | Value        |
|------------|--------------|
| Product    | Biz Buradayiz |
| Audience   | Administrators |
| Language   | English      |
| Month      | April 2026   |

*End of manual.*
