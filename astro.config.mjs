// @ts-check
import { defineConfig } from "astro/config";

// https://astro.build/config
export default defineConfig({
  i18n: {
    locales: ["en", "fr"],
    defaultLocale: "en",
    fallback: {
      fr: "en",
    },
    routing: {
      fallbackType: "rewrite",
    },
  },
  site: "https://fanny-vandewiele.com",
  base: "/",
  build: {
    format: "directory",
  },
});
