# Remote Quadlet Deployment

These files run Linavelt on an always-on Linux host using Podman quadlet and images published to GitHub Container Registry.

## What this enables

- A remote host can keep the app and MCP server online 24/7.
- Your local machine is no longer required for uptime.
- Image updates are pulled from GHCR after the publish workflow runs on `main`.

## Prerequisites

- A Linux server with Podman and user systemd enabled.
- `systemctl --user` available for the target user.
- Network ports `8000`, `4000`, and `3306` reachable as needed.
- Repository secrets or environment values for:
  - `GHCR_OWNER`
  - `APP_KEY`
  - `APP_URL`
  - `MCP_API_KEY`
  - `MYSQL_ROOT_PASSWORD`
  - `MYSQL_PASSWORD`

## Install

Set the environment variables and run:

```bash
GHCR_OWNER=nixbys \
APP_KEY="base64:replace-with-secure-key" \
APP_URL="https://linavelt.example.com" \
MCP_API_KEY="replace-with-secure-mcp-key" \
MYSQL_ROOT_PASSWORD="replace-root-password" \
MYSQL_PASSWORD="replace-db-password" \
sh ./containers/remote-quadlet/install-remote-quadlet.sh
```

Optional values:

- `IMAGE_TAG`: defaults to `latest`
- `APP_URL`: defaults to `http://127.0.0.1:8000`

## Update flow

1. Push to `main`.
2. GitHub Actions publishes `ghcr.io/<owner>/linavelt-app:<tag>` and `ghcr.io/<owner>/linavelt-mcp:<tag>`.
3. The remote host pulls and restarts the quadlet units with the new image tag.

## Verification

```bash
systemctl --user status linavelt-pod.service
systemctl --user status linavelt-app.service
systemctl --user status linavelt-mcp.service
curl http://127.0.0.1:4000/health
```
