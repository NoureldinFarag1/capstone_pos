const { app, BrowserWindow } = require('electron');
const path = require('path');

let win;

function createWindow() {
  console.log("Creating Electron Window...");

  win = new BrowserWindow({
    width: 1280,
    height: 800,
    icon: path.join(__dirname, 'public/images/favicon.ico'),
    webPreferences: {
      nodeIntegration: true,
    },
  });

  win.loadURL('http://localhost:8000');  // Laravel backend URL

  win.webContents.on('did-finish-load', () => {
    console.log("Window loaded successfully!");
  });

  win.on('closed', () => {
    console.log("Window closed.");
  });
}

app.whenReady().then(createWindow);

app.on('window-all-closed', () => {
  if (process.platform !== 'darwin') {
    app.quit();
  }
});
