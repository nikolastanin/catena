import { defineConfig } from 'vite'
import { resolve } from 'path'

export default defineConfig({
    root: 'src',
    build: {
        outDir: '../assets/dist',
        emptyOutDir: true,
        rollupOptions: {
            input: {
                'slots-frontend': resolve(__dirname, 'src/js/slots-public.js'),
                'slots-admin': resolve(__dirname, 'src/js/slots-admin.js')
            },
            output: {
                entryFileNames: 'js/[name].js',
                chunkFileNames: 'js/[name]-[hash].js',
                assetFileNames: (assetInfo) => {
                    if (assetInfo.name.endsWith('.css')) {
                        // Generate separate CSS files for frontend and admin
                        if (assetInfo.name.includes('slots-public')) {
                            return 'css/slots-frontend.css'
                        } else if (assetInfo.name.includes('slots-admin')) {
                            return 'css/slots-admin.css'
                        }
                        return 'css/[name][extname]'
                    }
                    return 'assets/[name][extname]'
                }
            }
        },
        sourcemap: false,
        minify: 'esbuild',
        target: 'es2015'
    },
    server: {
        port: 3000,
        open: false
    },
    resolve: {
        alias: {
            '@': resolve(__dirname, 'src')
        }
    }
})