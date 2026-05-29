# Podman Quadlet Files

This folder contains Podman quadlet files that run the Laravel app, MariaDB, and MCP server in one pod.

## Files

- `linavelt.pod`: shared pod with exposed ports 8000 (app), 4000 (MCP), and 3306 (DB)
- `linavelt-db.container`: MariaDB container in the pod
- `linavelt-app.container`: Laravel app container in the pod
- `linavelt-mcp.container`: MCP server container in the pod
- `up.sh`: one-command local bootstrap (build images, create pod, start containers, run migrations, verify health)

## Usage

1. Quick start (recommended):

   ```sh
   APP_KEY="$(openssl rand -base64 32 | sed 's#^#base64:#')" \
   MCP_API_KEY="dev-local-key" \
   sh ./containers/quadlet/up.sh
   ```

2. Build app and MCP images manually:

   ```sh
   sh ./containers/quadlet/build-images.sh
   ```

3. Install and start quadlet units:

   ```sh
   sh ./containers/quadlet/install-quadlet.sh
   ```

4. Check services:

   ```sh
   systemctl --user status linavelt-pod.service
   systemctl --user status linavelt-app.service
   systemctl --user status linavelt-mcp.service
   systemctl --user status linavelt-db.service
   ```

5. Verify endpoints:

   ```sh
   curl http://127.0.0.1:8000
   curl http://127.0.0.1:4000/health
   ```

6. If running quadlet units manually, initialize database schema once:

   ```sh
   podman exec linavelt-app php artisan migrate --force
   ```

## Notes

- Set a secure `APP_KEY` and `MCP_API_KEY` for non-dev environments.
- Replace default database credentials before production use.
- `podman` and user systemd must be available on the host.
