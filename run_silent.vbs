Set WshShell = CreateObject("WScript.Shell")
WshShell.CurrentDirectory = "C:\xampp\htdocs\agri-equip"
WshShell.Run "php artisan serve", 0, False
WshShell.Run "npm run dev", 0, False
Set WshShell = Nothing