param(
    [switch]$DebugMode
)

$ErrorActionPreference = 'Stop'
$script:ExitCode = 0

$root = Split-Path -Parent $MyInvocation.MyCommand.Path
Set-Location $root

$logFile = Join-Path $root 'cf.log'

# Kill any stale cloudflared or artisan processes from a previous run
try { Get-Process -Name 'cloudflared' -ErrorAction SilentlyContinue | Stop-Process -Force -ErrorAction SilentlyContinue } catch {}
try { Get-Process -Name 'php' -ErrorAction SilentlyContinue | Where-Object { $_.CommandLine -like '*artisan*' } | Stop-Process -Force -ErrorAction SilentlyContinue } catch {}
Start-Sleep -Milliseconds 500

if (Test-Path $logFile) {
    try { Remove-Item $logFile -Force -ErrorAction Stop }
    catch {
        # File still locked — overwrite with empty content instead
        try { [System.IO.File]::WriteAllText($logFile, '') } catch {}
    }
}

function Get-PhpExe {
    $phpCmd = Get-Command php -ErrorAction SilentlyContinue
    if ($phpCmd) { return $phpCmd.Source }

    $xamppPhp = 'C:\xampp\php\php.exe'
    if (Test-Path $xamppPhp) { return $xamppPhp }

    throw 'php.exe not found (install PHP or add to PATH)'
}

function Get-CloudflaredExe {
    $local = Join-Path $root 'cloudflared.exe'
    if (Test-Path $local) { return $local }

    $cmd = Get-Command cloudflared.exe -ErrorAction SilentlyContinue
    if ($cmd) { return $cmd.Source }

    throw 'cloudflared.exe not found in project folder or PATH'
}

function Ensure-Port8000Free {
    $port = 8000
    $pids = @()

    try {
        $conns = Get-NetTCPConnection -State Listen -LocalPort $port -ErrorAction Stop
        if ($conns) {
            $pids = $conns | Select-Object -ExpandProperty OwningProcess -Unique
        }
    }
    catch {
        $lines = netstat -ano | Select-String -Pattern ":$port\s+.*LISTENING"
        foreach ($line in $lines) {
            $parts = ($line.ToString() -replace '^\s+', '') -split '\s+'
            if ($parts.Count -ge 5) {
                $pid = 0
                if ([int]::TryParse($parts[4], [ref]$pid)) {
                    $pids += $pid
                }
            }
        }
        $pids = $pids | Select-Object -Unique
    }

    foreach ($pid in $pids) {
        if ($pid -and $pid -ne 0 -and $pid -ne 4) {
            try { Stop-Process -Id $pid -Force -ErrorAction SilentlyContinue } catch {}
        }
    }

    Start-Sleep -Milliseconds 500

    try {
        $stillUsed = Get-NetTCPConnection -State Listen -LocalPort $port -ErrorAction SilentlyContinue
        if ($stillUsed) {
            throw 'Port 8000 is still in use. Please close the app using port 8000 and try again.'
        }
    }
    catch {
        $line = netstat -ano | Select-String -Pattern ":$port\s+.*LISTENING"
        if ($line) {
            throw 'Port 8000 is still in use. Please close the app using port 8000 and try again.'
        }
    }

    return $port
}

function Get-ChromeExe {
    $candidates = @(
        'C:\Program Files\Google\Chrome\Application\chrome.exe',
        'C:\Program Files (x86)\Google\Chrome\Application\chrome.exe'
    )

    foreach ($exe in $candidates) {
        if (Test-Path $exe) { return $exe }
    }

    $cmd = Get-Command chrome.exe -ErrorAction SilentlyContinue
    if ($cmd) { return $cmd.Source }

    return $null
}

$artisan = $null
$cloud = $null

try {
    $phpExe = Get-PhpExe
    $cloudflaredExe = Get-CloudflaredExe
    $port = Ensure-Port8000Free

    if ($DebugMode) {
        Write-Host "[INFO] Using port $port"
    }

    $artisan = Start-Process -FilePath $phpExe -ArgumentList @('artisan', 'serve', '--host=127.0.0.1', "--port=$port") -WorkingDirectory $root -WindowStyle Hidden -PassThru

    Start-Sleep -Seconds 2

    $cfCmdLine = "`"$cloudflaredExe`" tunnel --url http://127.0.0.1:$port > `"$logFile`" 2>&1"
    $cloud = Start-Process -FilePath 'cmd.exe' -ArgumentList @('/c', $cfCmdLine) -WorkingDirectory $root -WindowStyle Hidden -PassThru

    $url = $null
    for ($i = 0; $i -lt 90; $i++) {
        Start-Sleep -Seconds 1
        if (-not (Test-Path $logFile)) { continue }

        $content = Get-Content $logFile -Raw -ErrorAction SilentlyContinue
        if ($content -match 'https://[a-z0-9\-]+\.trycloudflare\.com') {
            $url = $matches[0]
            break
        }
    }

    if (-not $url) {
        throw 'Could not get tunnel URL in time'
    }

    if ($DebugMode) {
        Write-Host "[INFO] Tunnel URL: $url"
    }

    $chromeExe = Get-ChromeExe
    $profileDir = Join-Path $env:TEMP 'agri-equip-tunnel-browser'

    if ($chromeExe) {
        $browser = Start-Process -FilePath $chromeExe -ArgumentList @("--app=$url", "--user-data-dir=$profileDir", '--new-window') -PassThru
        Wait-Process -Id $browser.Id
    }
    else {
        Start-Process $url
        throw 'Chrome not found. Opened default browser instead; auto-close tracking is not guaranteed.'
    }
}
catch {
    $script:ExitCode = 1
    $errFile = Join-Path $root 'run_tunnel_error.log'
    "$(Get-Date -Format s) :: $($_.Exception.Message)" | Out-File -FilePath $errFile -Append -Encoding UTF8

    if ($DebugMode) {
        Write-Host "[ERROR] $($_.Exception.Message)" -ForegroundColor Red
        Write-Host "See: $errFile" -ForegroundColor Yellow
        Read-Host "Press Enter to close"
    }
}
finally {
    if ($artisan -and -not $artisan.HasExited) {
        Start-Process -FilePath 'taskkill.exe' -ArgumentList @('/PID', $artisan.Id, '/T', '/F') -WindowStyle Hidden | Out-Null
    }

    if ($cloud -and -not $cloud.HasExited) {
        Start-Process -FilePath 'taskkill.exe' -ArgumentList @('/PID', $cloud.Id, '/T', '/F') -WindowStyle Hidden | Out-Null
    }
}

exit $script:ExitCode
