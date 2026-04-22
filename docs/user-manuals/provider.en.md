---
title: "Biz Buradayiz"
subtitle: "Service Provider Guide"
author: "Biz Buradayiz"
date: "April 2026"
lang: en-US
---

# Service Provider Guide

**Biz Buradayiz** — English · April 2026 · For service providers using the portal and listed on the public site (not a programming guide).

This guide explains how to register, stay visible to customers, use your dashboard, and understand **why** some providers appear higher on the search page.

---

## 1. What Biz Buradayiz does for you

Biz Buradayiz helps people who need roadside-style services **find and contact providers**. Your **public profile** can show your service type, area, and reviews. Customers may reach you by phone; the platform may also record **incoming calls** for activity reporting and **verified reviews**.

You interact with the product mainly through the **provider portal** (login, dashboard, account).

---

## 2. Registering as a provider

### 2.1 Application form

You complete a registration form with your **name**, **email**, **phone**, **password**, **service type**, and **city** (location). Submitting the form creates an account in a **pending** state.

### 2.2 Waiting for approval

While pending, you typically **cannot sign in**. The operations team **reviews** new providers, checks information, and **activates** eligible accounts.

**Tip:** Use a real email and phone you monitor; approvals and package matters may be communicated through those channels.

---

## 3. When you are activated

When an administrator sets your account to **Active**:

- The system records your **approval time** (`approved_at`). This timestamp matters for **“new provider”** visibility on the public search page (see Section 8).
- You may receive a **welcome free-tier package** **once**, if the business has configured a **free package** for your **service type**. That package still must satisfy the technical rules for being **active** (paid status, remaining connections, and validity dates as applicable).

After activation you can **log in** to the provider portal.

---

## 4. Signing in and account security

- Sign in with your **email** and **password**.
- If you forget your password, use **forgot password** on the login screen (if offered) and follow email instructions.
- If you see a message that your account is **not active**, your registration may still be **pending** or suspended — contact support.

---

## 5. Your account page

On **Account**, you can usually update **profile details** (within rules set by the platform), **change password**, and manage **location** selections that affect how you appear in **regional search**. Keep your **phone numbers** accurate; they are part of how customers reach you and how **call records** align with you.

Your **public profile address** on the website uses a **stable slug** derived from your name when you first became a provider. Do not rely on changing the URL yourself; ask support if a correction is needed.

---

## 6. Dashboard overview

The **Dashboard** typically shows:

- **Current package** — The subscription snapshot (name, status, dates, remaining **connections**).
- **Subscription history** — Past and pending subscriptions with paging.
- **Call statistics** — Counts such as **total calls** and **answered calls** from telephony records.
- **Call log** — A chronological list of **call events** associated with your account.

You may also see **paid packages** available for your **service type** so you can **request** an upgrade.

### 6.1 Requesting a paid package

When you request a paid package, the system usually creates a subscription in a **pending payment** state with **bank transfer** as the method. **Bank instructions** shown on the portal come from **site settings** maintained by administrators.

If **WhatsApp** is configured for subscription support, you may see a link that opens a chat with a **pre-filled message** (your name, email, package, subscription id, and sometimes the same bank text). Use it exactly as your contract describes.

**Until payment is confirmed and the subscription becomes fully **active** with **remaining connections**, you might not meet the rules for **public search listing** (see Section 7).

---

## 7. When you appear on the public search page

The public **search** page lists providers so visitors can filter by **service** and **location**.

You appear **only if all** of the following are true:

- Your account **status** is **Active**.
- You have an **active package subscription**: **Active** status, **Paid** payment status, **remaining connections greater than zero**, and you are **not past the subscription end date** (subscriptions without an end date may remain valid indefinitely — depending on package design).

If any condition fails, you may **disappear from search** until it is resolved — for example if your **connections** drop to zero or a period **expires**.

### 7.1 Previewing your own profile

If you are logged in as the owner, you may be allowed to **open your public profile URL** even when you **temporarily** do not meet every search rule, so you can check wording or images. **Customers** will still follow the stricter **search rules**.

---

## 8. Featured area vs the main list (why order changes)

Understanding this helps you interpret **visibility**, not to “game” the system unethically, but to know what the product rewards.

### 8.1 Featured strip (top of page 1)

On the **first page** of search results only, a **featured** row may highlight a few providers.

- **How many?** A number configured by administrators (often around **three**).
- **Who gets in?**
  - **New providers first:** If administrators enabled a **time window** (for example **24 hours**) after approval, the system may fill **all** featured slots with the **most recently approved** providers inside that window.
  - **Otherwise:** Featured slots go to providers with the highest **`ranking_score`** among everyone matching the visitor’s filters.

Featured does **not** repeat on later pages of results.

### 8.2 Main list order

Below featured, providers are sorted by **`ranking_score` (highest first)**, then by **first name** and **last name**.

### 8.3 What goes into `ranking_score` (plain language)

Your **ranking score** is recalculated in the **background** when relevant data changes. It blends three ideas, each compared **relative to all other active providers**:

1. **Customer ratings** — Better **approved** review averages help.
2. **Call activity** — More **recorded call events** (for example answered inbound calls) contribute to the **activity** portion.
3. **Time on the platform** — Longer tenure since **approval** contributes to the **experience** portion.

Administrators choose **percentage weights** for those three ingredients (for example 50% / 30% / 20%). Because the math is **relative**, your position can change when **other providers** improve even if you stay the same.

**Scores are not always instant** after a call or a new review; allow a short delay.

---

## 9. Reviews from customers

### 9.1 Why reviews require a real call

To protect quality, customers can submit a review only if their **phone number** matches an **answered inbound call** to you that is **not already used** for another review. If they never reached you on that number, the form will reject the submission.

### 9.2 Moderation

New reviews usually start as **pending**. Staff **approve** legitimate feedback. Only **approved** reviews should affect your **public average** and therefore the **rating** portion of ranking.

If you disagree with a review, follow the platform’s **support or dispute process** rather than arguing with customers on the record.

---

## 10. Getting help

- Use **contact** details published on the main website (phone, email, WhatsApp if shown).
- For **payments and packages**, follow the **bank instructions** on your dashboard and any **WhatsApp** workflow your contract references.
- For **technical outages**, describe what you tried, your account email, and screenshots if safe to share.

---

## Document control

| Item        | Value           |
|------------|-----------------|
| Product    | Biz Buradayiz   |
| Audience   | Service providers |
| Language   | English         |
| Month      | April 2026      |

*End of guide.*
