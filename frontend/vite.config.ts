import { reactRouter } from "@react-router/dev/vite";
import tailwindcss from "@tailwindcss/vite";
import { defineConfig } from "vite";
import tsconfigPaths from "vite-tsconfig-paths";

const isTest = process.env.VITEST == "true" ? true : false;
export default defineConfig({
  plugins: [tailwindcss(), !isTest && reactRouter(), tsconfigPaths()],
  test: {
    globals: true,
    setupFiles: './app/tests/setup.ts',
    environment: 'jsdom',
    coverage: {
      reporter: ['text', 'json', 'html'],
      exclude: [
        'node_modules/',
        'app/tests/',
        'react-router.config.ts',
        'app/pages/layout/PageLayout.tsx',
        'app/types/types.ts',
        'app/root.tsx',
        'app/routes.ts',
        'vite.config.ts',
        '.react-router*',
      ],
    },
  },
});
