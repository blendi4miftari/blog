import { defineConfig } from "vite"
import path from "path"

export default defineConfig({
  root: "assets/js",
  build: {
    outDir: "../../dist",
    emptyOutDir: true,
    rollupOptions: {
      input: path.resolve(__dirname, "assets/js/main.js"),
      output: {
        entryFileNames: "bundle.js",
        assetFileNames: "output.css",
      },
    },
  },
})
