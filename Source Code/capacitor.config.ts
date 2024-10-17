import type { CapacitorConfig } from '@capacitor/cli';

const config: CapacitorConfig = {
  appId: 'com.haveit.haveit',
  appName: 'HaveIt',
  webDir: 'dist',
  server: {
    url: 'http://192.168.253.109:8101', // Example server URL
    cleartext: true // Allow cleartext traffic
  }
  plugins: {
    CapacitorHttp: {
      enabled: true,
    },
  },
};

export default config;
