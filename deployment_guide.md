# MediVault Deployment Guide

This guide details the steps to deploy the **MediVault** application to **Vercel** (for the main web application and client assets) and **Render** (for persistent background queue workers like emails).

---

## 🛠️ Required Environment Variables (Both Platforms)

You will need to set up the following environment variables on both your Vercel and Render dashboards:

| Environment Variable | Recommended Production Value |
| :--- | :--- |
| `APP_NAME` | `MediVault` |
| `APP_ENV` | `production` |
| `APP_DEBUG` | `false` |
| `APP_URL` | `https://medivault0.vercel.app` (The domain you are deploying to) |
| `APP_KEY` | Your unique production encryption key (Generate one locally using `php artisan key:generate --show`) |
| `DB_CONNECTION` | `mongodb` |
| `MONGODB_URI` | Your production MongoDB Connection String (e.g., MongoDB Atlas) |
| `DB_DATABASE` | `medivault` |
| `QUEUE_CONNECTION` | `database` (pushes queue jobs to the MongoDB `jobs` collection) |
| `SESSION_DRIVER` | `file` (or `database`) |
| `MAIL_MAILER` | `smtp` |
| `MAIL_HOST` | `smtp.gmail.com` or `sandbox.smtp.mailtrap.io` (depending on your mail server) |
| `MAIL_PORT` | `587` or `2525` |
| `MAIL_USERNAME` | Your SMTP username / email address |
| `MAIL_PASSWORD` | Your SMTP App Password or credential password |
| `MAIL_FROM_ADDRESS` | `noreply@medivault.com` or your verified sender address |
| `MAIL_FROM_NAME` | `MediVault` |

---

## 🔒 Crucial MongoDB Atlas Settings
Because Vercel serverless functions run from dynamic IP addresses, you **must allow connections from any IP** in your MongoDB Atlas network access settings:
1. Go to your **MongoDB Atlas Dashboard**.
2. Navigate to **Security** ➜ **Network Access**.
3. Click **"Add IP Address"**.
4. Select **"Allow Access From Anywhere"** (adds IP `0.0.0.0/0`).
5. Click **"Confirm"**.

---

## 1. ⚡ Deploying to Vercel (Web Application)

Vercel hosts your Laravel web routes and compiles all Vite client assets.

### Step 1: Prepare and Push Git Repository
Ensure all configuration files are committed and pushed to your GitHub repository:
- `vercel.json` (Vercel routes & function handler configuration)
- `api/index.php` (Laravel Vercel entrypoint)
- `composer.json` & `composer.lock` (containing the mocked `ext-mongodb` platform requirement)
- `.gitignore` (ignores local storage and `.vercel` cache folders)

Run the following in your terminal to push your latest changes:
```bash
git add composer.json composer.lock vercel.json api/index.php app/Http/Controllers/RecordController.php app/Models/Record.php
git commit -m "chore: prepare codebase for successful vercel and render deployment"
git push origin main
```

### Step 2: Import Project on Vercel
1. Go to the [Vercel Dashboard](https://vercel.com/dashboard) and click **"Add New"** ➜ **"Project"**.
2. Connect your Git provider and import the repository.
3. In **Configure Project**:
   - **Framework Preset**: Select **"Other"** (Vercel will parse `vercel.json` automatically).
   - **Root Directory**: `./`
   - **Build and Output Settings**: Leave as default (Vercel automatically installs Node packages, runs `npm run build`, and invokes `vercel-php` to handle PHP dependencies).
4. Expand **Environment Variables** and add all the variables from the table above (especially `APP_URL` set to `https://medivault0.vercel.app` and `MONGODB_URI`).
5. Click **"Deploy"**.

### Step 3: Configure Domain
If the project was not auto-assigned the `medivault0.vercel.app` URL:
1. Go to your Vercel project's **Settings** ➜ **Domains**.
2. Enter `medivault0.vercel.app` and click **"Add"**.

---

## 2. 🛡️ Deploying to Render (Persistent Queue Workers)

Render handles background processing (like queueing emails and executing audits) that serverless Vercel cannot run persistently.

### Option A: Using Render Blueprints (Recommended)
Our repository contains a `render.yaml` blueprint. This automatically sets up both the web app and the queue worker.

1. Go to the [Render Dashboard](https://dashboard.render.com/) and click **"Blueprints"** ➜ **"New Blueprint Instance"**.
2. Select your Git repository.
3. Render will read the `render.yaml` configuration and prompt you to input the environment variables.
4. Click **"Approve"**. Render will deploy both services:
   - **medivault-web**: The Laravel web server (built as a Docker container).
   - **medivault-queue-worker**: The background worker processing your emails and clinical audits.

### Option B: Manual Web Service & Worker Setup
If you prefer not to use the automated Blueprint:

#### 1. Set up the Web Service (Main App)
1. Click **"New +"** ➜ **"Web Service"**.
2. Select your Git repository.
3. Configure the following:
   - **Name**: `medivault-web`
   - **Environment/Runtime**: `Docker`
   - **Dockerfile Path**: `Dockerfile` (default)
4. Add the environment variables listed in the table above.
5. Click **"Deploy Web Service"**.

#### 2. Set up the Worker Service (Background Queue)
Since the web service alone does not process background jobs, you must set up a separate worker service:
1. Click **"New +"** ➜ **"Worker Service"**.
2. Select your Git repository.
3. Configure the following:
   - **Name**: `medivault-queue-worker`
   - **Environment/Runtime**: `Docker`
   - **Dockerfile Path**: `Dockerfile`
   - **Docker Command**: `php artisan queue:work --tries=3 --timeout=90` (this overrides the default web CMD)
4. Add the same environment variables listed in the table above.
5. Click **"Deploy Worker Service"**.

