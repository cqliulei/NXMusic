import { fileURLToPath, URL } from 'node:url'

import { defineConfig } from 'vite'
import vue from '@vitejs/plugin-vue'

import AutoImport from 'unplugin-auto-import/vite'
import Components from 'unplugin-vue-components/vite'
import { ElementPlusResolver } from 'unplugin-vue-components/resolvers'

import Icons from 'unplugin-icons/vite'
import IconsResolver from 'unplugin-icons/resolver'
import path from 'node:path'


// https://vitejs.dev/config/
export default defineConfig({
  plugins: [
    vue(),
    AutoImport({
      // 自动导入 Vue 相关函数，如：ref, reactive, toRef 等
      // imports: ['vue'],
      resolvers: [
        ElementPlusResolver(),
        // 自动导入图标组件
        IconsResolver({
          prefix: 'Icon',

        }),
      ],
    }),
    Components({
      resolvers: [
        // 自动导入 Element Plus 组件
        ElementPlusResolver(),
        // 自动注册图标组件
        IconsResolver({
          //# 图标结构
          // # 它由三部分组成：{prefix}-{collection}-{icon}
          //# prefix：icon的前缀，默认值为'i',可设置成false,如果设置成false,那么组件使用就变成 <ep-edit/>
          //# collection: iconify 唯一name;
          //# icon: 图标名字
          prefix: false, // 默认为i,设置为false则不显示前缀
          enabledCollections: ['ep'],//ep,即element-plus的缩写，代表的是element-plus的图标，它会帮你安装@iconify-json/ep依赖，当然你可以把这个设置成别的，如:mdi,fa等等，这样它会加载其他图标库，也都会在package.json给你安装对应的依赖。
          alias: {
            nx: "ep"  //配置别名
          },

        }),
      ],
    }),
    Icons({
      autoInstall: true,
    }),
  ],

  resolve: {
    alias: {
      '@': fileURLToPath(new URL('./src', import.meta.url))
    }
  },
  //代理配置
  server: {  
    https: false,
    proxy: {
      '/api': {
        target: 'http://localhost:8088/',
        changeOrigin: true,
        ws: true,
        rewrite: (path) => path.replace(/^\/api/, "")
      }
    }
  },


})
