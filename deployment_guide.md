# MediVault Deployment Guide

This guide details the steps to deploy the **MediVault** application using **Vercel** (for the serverless PHP/Laravel web app and asset layer) and/or **Render** (for standard persistent web services and background queue workers).

---

## 🛠️ Required Environment Variables (Both Platforms)

You will need to set up the following environment variables on both Vercel and Render dashboards:

| Environment Variable | Recommended Production Value |
| :--- | :--- |
| `APP_NAME` | `MediVault` |
| `APP_ENV` | `production` |
| `APP_DEBUG` | `false` |
| `APP_URL` | The live URL of your deployed application (e.g. `https://medivault.vercel.app` or `https://medivault.onrender.com`) |
| `APP_KEY` | Your unique production encryption key (Generate one locally using `php artisan key:generate --show`) |
| `DB_CONNECTION` | `mongodb` |
| `MONGODB_URI` | Your production MongoDB Connection String (e.g., MongoDB Atlas) |
| `DB_DATABASE` | `medivault` |
| `QUEUE_CONNECTION` | `database` (this pushes jobs to the MongoDB `jobs` collection) |
| `SESSION_DRIVER` | `file` (or `database`) |
| `MAIL_MAILER` | `smtp` |
| `MAIL_HOST` | e.g. `smtp.gmail.com` or `smtp.mailtrap.io` |
| `MAIL_PORT` | `587` |
| `MAIL_USERNAME` | Your SMTP username |
| `MAIL_PASSWORD` | Your SMTP password or App Password |
| `MAIL_FROM_ADDRESS` | `noreply@yourdomain.com` |
| `MAIL_FROM_NAME` | `MediVault` |

---

## 1. ⚡ Deploying to Vercel (Serverless Web App)

Vercel is excellent for fast response times and scaling the web frontend.

### Step 1: Prepare Git Repository
Ensure all newly added deployment files are committed and pushed to your GitHub/GitLab repository:
- `vercel.json`
- `api/index.php`
- `.gitignore`

### Step 2: Import Project on Vercel
1. Go to the [Vercel Dashboard](https://vercel.com/dashboard) and click **"Add New"** ➜ **"Project"**.
2. Connect your Git provider and import the **med** repository.
3. In **Configure Project**:
   - **Framework Preset**: Leave as `Other` (Vercel will read `vercel.json`).
   - **Root Directory**: `./`
4. Expand **Environment Variables** and add all the variables listed in the table above.
5. Click **"Deploy"**.

---

## 2. 🛡️ Deploying to Render (Persistent Web & Queue Workers)

Render is required to run persistent background services like Laravel queues, cron schedules, and web sockets.

### Option A: Using Render Blueprints (Recommended)
We have created a `render.yaml` file in your repository. This file defines both the web application and the background worker services automatically.

1. Go to the [Render Dashboard](https://dashboard.render.com/) and click **"Blueprints"** ➜ **"New Blueprint Instance"**.
2. Connect your Git provider and select the **med** repository.
3. Render will read the `render.yaml` and prompt you to enter the environment variables (like `APP_KEY`, `MONGODB_URI`, etc.).
4. Click **"Approve"**. Render will deploy both services:
   - **medivault-web**: The web server.
   - **medivault-queue-worker**: The background worker processing your emails and clinical audits.

### Option B: Manual Web Service Setup
If you want to set up services manually:
1. Click **"New +"** ➜ **"Web Service"**.
2. Select your Git repository.
3. Configure the following:
   - **Language**: `PHP`
   - **Build Command**: `chmod +x build.sh && ./build.sh`
   - **Start Command**: Leave blank (Render uses native Nginx serving the `public` folder).
4. Add the environment variables, specifically setting `DOCUMENT_ROOT` to `public` and `PHP_VERSION` to `8.2`.
5. Click **"Deploy Web Service"**.

---

## 📋 Queue Worker Verification
To verify emails (like reset password links, clinical shares, consent warnings) are successfully sending:
1. Ensure the `medivault-queue-worker` is active and running in Render.
2. Monitor the log files in Render under the worker service to see jobs executing:
   ```text
   Processing jobs from the [default] queue.
   Processed: App\Mail\MediVaultMail
   ```
