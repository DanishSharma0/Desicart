Deploying Desicart to Render
===========================

This document shows the minimal, safe steps to deploy the app to Render using the included Dockerfile and render.yaml and how to run migrations safely.

Prerequisites
- Push your repo with the new files (Dockerfile, docker/, render.yaml, .dockerignore).
- A Render account with access to create Services and Databases.

1. Commit & push

Run the following locally:

git add Dockerfile docker render.yaml .dockerignore
git commit -m "Add Docker + Render deployment files"
git push origin main

2. Create the Web Service on Render
- In Render: New → Web Service → Connect your Git repo.
- Option A: Let Render detect render.yaml (recommended). It will create the web service and the migration job entry defined in render.yaml.
- Option B: Create manually: Environment = Docker, Dockerfile Path = Dockerfile.

3. Environment variables (set in Service → Environment)
- APP_ENV=production
- APP_DEBUG=false
- APP_URL=https://<your-render-url>
- APP_KEY= (recommended to set explicitly; see below)
- Database vars (if using managed DB): DB_CONNECTION, DB_HOST, DB_PORT, DB_DATABASE, DB_USERNAME, DB_PASSWORD
- If using S3 for uploads: FILESYSTEM_DISK=s3, AWS_ACCESS_KEY_ID, AWS_SECRET_ACCESS_KEY, AWS_DEFAULT_REGION, AWS_BUCKET

Generate APP_KEY locally (recommended)

Run these commands locally:

composer install --no-dev --optimize-autoloader
php artisan key:generate --show

Copy the printed value into Render → Environment → APP_KEY.

4. Create a managed Database on Render (optional but recommended)
- New → PostgreSQL / MySQL Database → create.
- Copy connection details into the service Environment variables.

5. Deploy and run migrations
- Deploy the Web Service from Render (it will build the Docker image).
- After a successful deploy, run migrations using the desicart-migrate job:
  - Render Dashboard → Jobs → desicart-migrate → Run Job
  - OR open Service → Shell and run these commands:

php artisan migrate --force
php artisan storage:link

Notes
- The docker/docker-entrypoint.sh no longer runs migrations automatically. This avoids accidental schema changes on every start.
- php artisan storage:link can be run once (job or shell) to create the public storage symlink.
- If you prefer automatic migrations, I can revert that change, but it is safer to run migrations as a controlled job.

Local testing

Run these commands locally to build and run the container:

docker build -t desicart:local .
docker run -p 8080:80 --env-file .env -d desicart:local

Visit http://localhost:8080

Automating the migration job (optional)
- You can trigger the migration job after deploy via the Render API. Example (replace placeholders):

RENDER_API_KEY="<your-render-api-key>"
SERVICE_ID="<web-service-id>"
JOB_ID="<job-id>"

curl -X POST \
  -H "Accept: application/json" \
  -H "Authorization: Bearer ${RENDER_API_KEY}" \
  https://api.render.com/v1/services/${SERVICE_ID}/jobs/${JOB_ID}/executions

If you want, I can add a GitHub Actions workflow that calls this API after Render completes a deploy, or add a Render webhook to trigger the job — tell me which automation you prefer and I will add it.

Troubleshooting
- Build fails on Node step: ensure package.json and lockfile are present and the Vite build outputs to public/build (adjust Dockerfile if needed).
- Permission errors: ensure storage and bootstrap/cache are writable by www-data. Entrypoint sets these permissions.

Questions or next steps
- I can add a GitHub Actions workflow to run migrations automatically post-deploy. Reply "Add CI job".
- I can also configure S3 storage and update filesystems.php and env examples. Reply "Add S3".
