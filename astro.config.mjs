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
      fallbackType: "rewrite"
    }
  },
  site: "https://ess100.netlify.app/",
//   trailingSlash: "always",
//   build: {
//     format: "directory",
//   },
  // vite: {
  //     ssr: {
  //         noExternal: ['@astrojs/image', 'astro-icon'],
  //     },
  // },
});
