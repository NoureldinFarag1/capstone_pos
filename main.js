const { app, BrowserWindow } = require('electron');
const path = require('path');
const isDev = require('electron-is-dev');
const { exec } = require('child_process');
const http = require('http');

let win;

function createWindow() {
  console.log("Creating Electron Window...");

  win = new BrowserWindow({
    width: 800,
    height: 600,
    icon: path.join(__dirname, 'public/images/favicon.ico'), // Ensure this path is correct
    webPreferences: {
      nodeIntegration: true,
    },
  });

  win.loadURL(
    isDev
      ? 'http://localhost:8000'
      : `file://${path.join(__dirname, 'dist/index.html')}`
  );

  win.webContents.on('did-finish-load', () => {
    console.log("Window loaded successfully!");
  });

  win.on('closed', () => {
    console.log("Window closed.");
  });

  win.webContents.on('did-fail-load', (event, errorCode, errorDescription) => {
    console.error(`Failed to load URL: ${errorDescription} (Code: ${errorCode})`);
  });
}

function startLaravelServer() {
  console.log("Starting Laravel server...");
  const server = exec('php artisan serve');

  server.stdout.on('data', (data) => {
    console.log(`Laravel server: ${data}`);
    if (data.includes('Starting Laravel development server')) {
      waitForServerReady(createWindow);
    }
  });

  server.stderr.on('data', (data) => {
    console.error(`Laravel server error: ${data}`);
  });

  server.on('close', (code) => {
    console.log(`Laravel server exited with code ${code}`);
  });
}

function waitForServerReady(callback) {
  const interval = setInterval(() => {
    http.get('http://localhost:8000', (res) => {
      if (res.statusCode === 200) {
        clearInterval(interval);
        console.log("Laravel server is ready.");
        callback();
      }
    }).on('error', () => {
      console.log("Waiting for Laravel server to be ready...");
    });
  }, 1000);
}

app.on('ready', () => {
  if (isDev) {
    startLaravelServer();
  } else {
    createWindow();
  }
});

app.on('window-all-closed', () => {
  if (process.platform !== 'darwin') {
    app.quit();
  }
});

app.on('activate', () => {
  if (BrowserWindow.getAllWindows().length === 0) {
    createWindow();
  }
});
