# vue

This template should help get you started developing with Vue 3 in Vite.

## Recommended IDE Setup

[VS Code](https://code.visualstudio.com/) + [Volar](https://marketplace.visualstudio.com/items?itemName=johnsoncodehk.volar) (and disable Vetur).

## Type Support for `.vue` Imports in TS

Since TypeScript cannot handle type information for `.vue` imports, they are shimmed to be a generic Vue component type by default. In most cases this is fine if you don't really care about component prop types outside of templates.

However, if you wish to get actual prop types in `.vue` imports (for example to get props validation when using manual `h(...)` calls), you can run `Volar: Switch TS Plugin on/off` from VS Code command palette.

## Customize configuration

See [Vite Configuration Reference](https://vitejs.dev/config/).

## Project Setup

```sh
export NODE_OPTIONS="--max-old-space-size=8192"   # 8 GB
yarn install
```

### Compile and Hot-Reload for Development

```sh
npm run dev
```

### Type-Check, Compile and Minify for Production

```sh
npm run build
```


# 0) Make install layout consistent across dev + CI
echo "node-linker=hoisted" >> .npmrc   # or delete the CLI flag and commit this

# 1) Recompute lockfile ONLY (no node_modules written, no scripts)
pnpm install --lockfile-only --ignore-scripts --no-optional

# 2) Commit the updated lockfile
git add pnpm-lock.yaml .npmrc
git commit -m "chore: sync pnpm-lock.yaml with package.json"

# 3) CI install (fast, reproducible, still skips heavy scripts)
CI=1 NODE_OPTIONS="--max-old-space-size=4096" \
PLAYWRIGHT_SKIP_BROWSER_DOWNLOAD=1 \
PUPPETEER_SKIP_DOWNLOAD=1 \
CYPRESS_INSTALL_BINARY=0 \
ELECTRON_SKIP_BINARY_DOWNLOAD=1 \
pnpm install --frozen-lockfile --ignore-scripts
